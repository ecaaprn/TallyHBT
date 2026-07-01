<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use App\Models\TimeList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;

class JobOrderController extends Controller
{
    protected function applyCabangFilter($query)
    {
        $access = request()->input('user_cabang_access', [
            'akses_cabang' => 'spesifik',
            'cabang_id' => null,
            'cabang_nama' => null
        ]);

        if ($access['akses_cabang'] === 'semua') {
            return $query;
        }

        if ($access['cabang_id']) {
            $query->whereHas('user.aksesMenu', function ($q) use ($access) {
                $q->where('cabang_id', $access['cabang_id']);
            });
        }

        return $query;
    }

    protected function applyCabangFilterToQueryBuilder($query, $userTableAlias = 'u_job')
    {
        $access = request()->input('user_cabang_access', [
            'akses_cabang' => 'spesifik',
            'cabang_id' => null,
            'cabang_nama' => null
        ]);

        if ($access['akses_cabang'] === 'semua') {
            return $query;
        }

        if ($access['cabang_nama']) {
            $query->where("{$userTableAlias}.cabang", $access['cabang_nama']);
        }

        return $query;
    }
    public function monitoring(Request $request)
    {
        $perPage = 5;
        $currentPage = $request->input('page', 1);

        $access = request()->input('user_cabang_access', [
            'akses_cabang' => 'spesifik',
            'cabang_id' => null,
            'cabang_nama' => null
        ]);

        $query = DB::table('job_orders')
            ->join('users', 'job_orders.user_id', '=', 'users.id')
            ->leftJoin('akses_menus', 'users.id', '=', 'akses_menus.user_id')
            ->select('job_orders.Tanggal')
            ->distinct();

        if ($access['akses_cabang'] === 'spesifik' && $access['cabang_id']) {
            $query->where('akses_menus.cabang_id', $access['cabang_id']);
        }

        $allUniqueDates = $query->orderBy('job_orders.Tanggal', 'desc')
            ->pluck('Tanggal');

        $datesOnCurrentPage = $allUniqueDates->slice(($currentPage - 1) * $perPage, $perPage);

        $monitoringData = collect([]);
        if ($datesOnCurrentPage->isNotEmpty()) {
            $query = DB::table('job_orders')
                ->join('users', 'job_orders.user_id', '=', 'users.id')
                ->leftJoin('akses_menus', 'users.id', '=', 'akses_menus.user_id')
                ->select('job_orders.Tanggal', 'job_orders.NoShift', 'job_orders.Kapal')
                ->whereIn('job_orders.Tanggal', $datesOnCurrentPage)
                ->distinct();

            if ($access['akses_cabang'] === 'spesifik' && $access['cabang_id']) {
                $query->where('akses_menus.cabang_id', $access['cabang_id']);
            }

            $monitoringData = $query->orderBy('job_orders.Tanggal', 'desc')
                ->orderBy('job_orders.NoShift', 'asc')
                ->orderBy('job_orders.Kapal', 'asc')
                ->get();
        }

        $paginatedResult = new LengthAwarePaginator(
            $monitoringData,
            $allUniqueDates->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url()]
        );

        return view('frontend.monitoring', ['monitoringGroups' => $paginatedResult]);
    }

    public function input(Request $request, $date, $shift, $kapal = null)
    {
        $carbonDate = Carbon::parse($date);

        $query = JobOrder::with(['timeList.user', 'user'])
            ->leftJoin('time_lists', 'job_orders.id', '=', 'time_lists.job_order_id')
            ->where('job_orders.Kapal', $kapal);

        $query = $this->applyCabangFilter($query);

        $jobOrders = $query->orderBy('job_orders.Tanggal', 'asc')
            ->select('job_orders.*')
            ->orderByRaw('time_lists.plugging IS NOT NULL DESC, job_orders.created_at ASC')
            ->get();

        $today = Carbon::now()->format('d/m/Y');

        $masterKapal = DB::table('master_kapals')
            ->where('status', 'aktif')
            ->select('nama', 'tanggal')
            ->orderByRaw("
                CASE 
                    WHEN STR_TO_DATE(tanggal, '%d/%m/%Y') = STR_TO_DATE(?, '%d/%m/%Y')
                    THEN 0
                    ELSE 1
                END
            ", [$today])
            ->orderByRaw("STR_TO_DATE(tanggal, '%d/%m/%Y') DESC")
            ->orderBy('nama')
            ->get()
            ->map(function ($item) {
                if (!empty($item->tanggal)) {
                    try {
                        $item->display = $item->nama . ' - ' . Carbon::createFromFormat('d/m/Y', $item->tanggal)->format('d/m/Y');
                    } catch (\Exception $e) {
                        $item->display = $item->nama;
                    }
                } else {
                    $item->display = $item->nama;
                }

                return $item;
            });

        $masterTruck = DB::table('master_trucks')->select('nama')->orderBy('nama')->get();
        $masterHose = DB::table('master_hoses')->select('nama')->orderBy('nama')->get();
        $masterPalka = DB::table('master_palkas')->select('nama')->orderBy('nama')->get();

        return view('frontend.job-order', [
            'jobOrders' => $jobOrders,
            'currentDate' => $carbonDate,
            'currentShift' => $shift,
            'selectedKapal' => $kapal,
            'masterKapal' => $masterKapal,
            'masterTruck' => $masterTruck,
            'masterHose' => $masterHose,
            'masterPalka' => $masterPalka,
        ]);
    }

    public function inputByKapal(Request $request, $kapal)
    {
        $currentDate = Carbon::now();
        $hour = $currentDate->hour;

        if ($hour >= 8 && $hour < 16) {
            $currentShift = 1;
        } elseif ($hour >= 16 && $hour < 24) {
            $currentShift = 2;
        } else {
            $currentShift = 3;
        }

        $query = JobOrder::with(['timeList.user', 'user'])
            ->leftJoin('time_lists', 'job_orders.id', '=', 'time_lists.job_order_id')
            ->where('job_orders.Kapal', $kapal);

        $query = $this->applyCabangFilter($query);

        $jobOrders = $query->orderBy('job_orders.Tanggal', 'asc')
            ->select('job_orders.*')
            ->orderByRaw('time_lists.plugging IS NOT NULL DESC, job_orders.created_at ASC')
            ->get();

        $today = Carbon::now()->format('d/m/Y');

        $masterKapal = DB::table('master_kapals')
            ->where('status', 'aktif')
            ->select('nama', 'tanggal')
            ->orderByRaw("
                CASE 
                    WHEN STR_TO_DATE(tanggal, '%d/%m/%Y') = STR_TO_DATE(?, '%d/%m/%Y')
                    THEN 0
                    ELSE 1
                END
            ", [$today])
            ->orderByRaw("STR_TO_DATE(tanggal, '%d/%m/%Y') DESC")
            ->orderBy('nama')
            ->get()
            ->map(function ($item) {
                if (!empty($item->tanggal)) {
                    try {
                        $item->display = $item->nama . ' - ' . Carbon::createFromFormat('d/m/Y', $item->tanggal)->format('d/m/Y');
                    } catch (\Exception $e) {
                        $item->display = $item->nama;
                    }
                } else {
                    $item->display = $item->nama;
                }

                return $item;
            });

        $masterTruck = DB::table('master_trucks')->select('nama')->orderBy('nama')->get();
        $masterHose = DB::table('master_hoses')->select('nama')->orderBy('nama')->get();
        $masterPalka = DB::table('master_palkas')->select('nama')->orderBy('nama')->get();

        return view('frontend.job-order', [
            'jobOrders' => $jobOrders,
            'currentDate' => $currentDate,
            'currentShift' => $currentShift,
            'selectedKapal' => $kapal,
            'masterKapal' => $masterKapal,
            'masterTruck' => $masterTruck,
            'masterHose' => $masterHose,
            'masterPalka' => $masterPalka,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $availableTrucks = DB::table('master_trucks')->pluck('nama')->toArray();
            $availableKapals = DB::table('master_kapals')->where('status', 'aktif')->pluck('nama')->toArray();

            $rules = [
                'Tanggal' => 'required|date',
                'NoShift' => 'required|integer',
                'Kapal' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) use ($availableKapals) {
                        if (!in_array($value, $availableKapals)) {
                            $fail("Kapal tidak valid.");
                        }
                    }
                ],
                'NoJobOrder' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) {
                        $exists = DB::table('job_orders')
                            ->where('NoJobOrder', $value)
                            ->whereIn('status', ['Aktif', 'NonAktif'])
                            ->exists();

                        if ($exists) {
                            $fail("No Job Order {$value} sudah digunakan dan belum dibatalkan.");
                        }
                    }
                ],
                'NoTruck' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) use ($availableTrucks) {
                        if (!in_array($value, $availableTrucks)) {
                            $fail("No Truck tidak valid.");
                        }
                    }
                ],
                'WaktuTiba' => 'required|date_format:H:i',
            ];

            $validated = $request->validate($rules);
            $validated['user_id'] = Auth::id();

            $jobOrder = DB::transaction(function () use ($validated, $request) {
                $jobOrder = JobOrder::create($validated);

                DB::table('time_lists')->updateOrInsert(
                    ['job_order_id' => $jobOrder->id],
                    [
                        'truck_no' => $validated['NoTruck'],
                        'NoPalka' => $request->input('NoPalka'),
                        'NoHose' => $request->input('NoHose'),
                        'user_id' => $validated['user_id'],
                        'Catatan' => '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                return $jobOrder;
            });

            return response()->json($jobOrder->load(['timeList.user', 'user']));
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, JobOrder $jobOrder)
    {
        try {
            $availableTrucks = DB::table('master_trucks')->pluck('nama')->toArray();
            $availableKapals = DB::table('master_kapals')->where('status', 'aktif')->pluck('nama')->toArray();
            $availableHoses = DB::table('master_hoses')->pluck('nama')->toArray();
            $availablePalkas = DB::table('master_palkas')->pluck('nama')->toArray();

            $request->validate([
                'Kapal' => [
                    'sometimes',
                    'required',
                    'string',
                    function ($attribute, $value, $fail) use ($availableKapals) {
                        if (!in_array($value, $availableKapals)) {
                            $fail("Kapal tidak valid.");
                        }
                    }
                ],
                'NoTruck' => [
                    'sometimes',
                    'required',
                    'string',
                    function ($attribute, $value, $fail) use ($availableTrucks) {
                        if (!in_array($value, $availableTrucks)) {
                            $fail("No Truck tidak valid.");
                        }
                    }
                ],
                'NoHose' => [
                    'nullable',
                    'string',
                    function ($attribute, $value, $fail) use ($availableHoses) {
                        if ($value && !in_array($value, $availableHoses)) {
                            $fail("No Hose tidak valid.");
                        }
                    }
                ],
                'NoPalka' => [
                    'nullable',
                    'string',
                    function ($attribute, $value, $fail) use ($availablePalkas) {
                        if ($value && !in_array($value, $availablePalkas)) {
                            $fail("No Palka tidak valid.");
                        }
                    }
                ],
                'WaktuTiba' => 'nullable|date_format:H:i:s,H:i',
                'time_list' => 'nullable|array',
            ]);

            DB::transaction(function () use ($request, $jobOrder) {
                $updateData = [];

                foreach (['Kapal', 'NoTruck', 'WaktuTiba'] as $field) {
                    if ($request->has($field)) {
                        $value = $field === 'WaktuTiba' ? $this->normalizeToSeconds($request->input($field)) : $request->input($field);
                        $updateData[$field] = $value;
                    }
                }

                if (!empty($updateData)) {
                    $jobOrder->update($updateData);
                }

                if ($request->has('time_list')) {
                    $timeListData = $request->input('time_list');

                    foreach ($timeListData as $key => $value) {
                        if (in_array($key, ['plugging', 'open_valve', 'close_valve', 'unplugging'])) {
                            $timeListData[$key] = $this->normalizeToSeconds($value);
                        }
                    }

                    if (array_key_exists('Catatan', $timeListData) && $timeListData['Catatan'] === null) {
                        $timeListData['Catatan'] = '';
                    }

                    if ($request->has('NoTruck')) {
                        $timeListData['truck_no'] = $request->input('NoTruck');
                    }

                    if (!empty($timeListData)) {
                        unset(
                            $timeListData['id'],
                            $timeListData['job_order_id'],
                            $timeListData['created_at'],
                            $timeListData['updated_at'],
                            $timeListData['user']
                        );

                        $timeListData['user_id'] = Auth::id();
                        DB::table('time_lists')->updateOrInsert(
                            ['job_order_id' => $jobOrder->id],
                            array_merge($timeListData, ['updated_at' => now()])
                        );
                    }
                }
            });

            $jobOrder->refresh();

            $tl = $jobOrder->timeList;

            if ($tl && $tl->plugging && $tl->open_valve && $tl->close_valve && $tl->unplugging) {
                $jobOrder->update(['status' => 'NonAktif']);
            }

            return response()->json($jobOrder->load(['timeList.user', 'user']));
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function exportByShift($date, $shift, $kapal)
    {
        $reportDate = Carbon::parse($date);

        switch ((int) $shift) {
            case 1:
                $startTime = $reportDate->copy()->setTime(8, 0, 0);
                $endTime = $reportDate->copy()->setTime(15, 59, 59);
                break;
            case 2:
                $startTime = $reportDate->copy()->setTime(16, 0, 0);
                $endTime = $reportDate->copy()->endOfDay();
                break;
            case 3:
                $startTime = $reportDate->copy()->startOfDay();
                $endTime = $reportDate->copy()->setTime(7, 59, 59);
                break;
            default:
                return redirect()->back()->with('error', 'Shift tidak valid');
        }

        $queryStartDate = $reportDate->copy()->subDays(2)->toDateString();
        $queryEndDate = $reportDate->copy()->toDateString();

        $query = JobOrder::with(['timeList.user', 'user'])
            ->where('Kapal', $kapal)
            ->where('status', '!=', 'Batal')
            ->whereBetween('Tanggal', [$queryStartDate, $queryEndDate])
            ->whereHas('timeList', function ($query) {
                $query->whereNotNull('plugging')->whereNotNull('unplugging');
            });

        $query = $this->applyCabangFilter($query);
        $potentialJobOrders = $query->get();

        $jobOrders = $potentialJobOrders->filter(function ($jobOrder) use ($startTime, $endTime) {
            $timeList = $jobOrder->timeList;
            if (!$timeList)
                return false;

            $jobDateCarbon = Carbon::parse($jobOrder->Tanggal);
            if ($jobOrder->NoShift == 3) {
                $jobDateCarbon->addDay();
            }
            $jobDate = $jobDateCarbon->toDateString();

            $waktuTibaDateTime = $jobOrder->WaktuTiba ? $this->calculateFullTimestamp(null, $jobOrder->WaktuTiba, $jobDate) : null;
            $pluggingDateTime = $this->calculateFullTimestamp($waktuTibaDateTime, $timeList->plugging, $jobDate);
            $openValveDateTime = $this->calculateFullTimestamp($pluggingDateTime, $timeList->open_valve, $jobDate);
            $closeValveDateTime = $this->calculateFullTimestamp($openValveDateTime, $timeList->close_valve, $jobDate);
            $unpluggingDateTime = $this->calculateFullTimestamp($closeValveDateTime, $timeList->unplugging, $jobDate);

            if ($unpluggingDateTime) {
                return $unpluggingDateTime->between($startTime, $endTime, true);
            }
            return false;
        });

        return $this->streamCsv($jobOrders, $date, $shift, $kapal, false);
    }

    public function exportByKapal($date, $kapal)
    {
        $reportDate = Carbon::parse($date);
        $startTime = $reportDate->copy()->startOfDay();
        $endTime = $reportDate->copy()->endOfDay();

        $queryStartDate = $reportDate->copy()->subDays(2)->toDateString();
        $queryEndDate = $reportDate->copy()->toDateString();

        $query = JobOrder::with(['timeList.user', 'user'])
            ->where('Kapal', $kapal)
            ->where('status', '!=', 'Batal')
            ->whereBetween('Tanggal', [$queryStartDate, $queryEndDate])
            ->whereHas('timeList', function ($query) {
                $query->whereNotNull('plugging')
                    ->whereNotNull('open_valve')
                    ->whereNotNull('close_valve')
                    ->whereNotNull('unplugging')
                    ->whereNotNull('NoHose')
                    ->whereNotNull('NoPalka');
            });

        $query = $this->applyCabangFilter($query);
        $potentialJobOrders = $query->get();

        $jobOrders = $potentialJobOrders->filter(function ($jobOrder) use ($startTime, $endTime) {
            $timeList = $jobOrder->timeList;
            if (!$timeList)
                return false;

            $jobDateCarbon = Carbon::parse($jobOrder->Tanggal);
            if ($jobOrder->NoShift == 3) {
                $jobDateCarbon->addDay();
            }
            $jobDate = $jobDateCarbon->toDateString();

            $waktuTibaDateTime = $jobOrder->WaktuTiba ? $this->calculateFullTimestamp(null, $jobOrder->WaktuTiba, $jobDate) : null;
            $pluggingDateTime = $this->calculateFullTimestamp($waktuTibaDateTime, $timeList->plugging, $jobDate);
            $openValveDateTime = $this->calculateFullTimestamp($pluggingDateTime, $timeList->open_valve, $jobDate);
            $closeValveDateTime = $this->calculateFullTimestamp($openValveDateTime, $timeList->close_valve, $jobDate);
            $unpluggingDateTime = $this->calculateFullTimestamp($closeValveDateTime, $timeList->unplugging, $jobDate);

            if ($unpluggingDateTime) {
                return $unpluggingDateTime->between($startTime, $endTime, true);
            }
            return false;
        });

        return $this->streamCsv($jobOrders, $date, null, $kapal, true);
    }

    public function exportAllShift()
    {
        $query = JobOrder::with(['timeList.user', 'user'])
            ->where('status', '!=', 'Batal')
            ->whereHas('timeList', function ($query) {
                $query->whereNotNull('plugging')
                    ->whereNotNull('open_valve')
                    ->whereNotNull('close_valve')
                    ->whereNotNull('unplugging')
                    ->whereNotNull('NoHose')
                    ->whereNotNull('NoPalka');
            });

        $query = $this->applyCabangFilter($query);
        $jobOrders = $query->orderBy('Tanggal', 'asc')
            ->orderBy('Kapal', 'asc')
            ->orderBy('NoShift', 'asc')
            ->get();

        return $this->streamCsv($jobOrders, now()->format('Y-m-d'), 'Semua', 'Semua Data', false);
    }

    public function exportAllKapal()
    {
        $query = JobOrder::with(['timeList.user', 'user'])
            ->where('status', '!=', 'Batal')
            ->whereHas('timeList', function ($query) {
                $query->whereNotNull('plugging')
                    ->whereNotNull('open_valve')
                    ->whereNotNull('close_valve')
                    ->whereNotNull('unplugging')
                    ->whereNotNull('NoHose')
                    ->whereNotNull('NoPalka');
            });

        $query = $this->applyCabangFilter($query);
        $jobOrders = $query->orderBy('Tanggal', 'asc')
            ->orderBy('Kapal', 'asc')
            ->orderBy('NoShift', 'asc')
            ->get();

        return $this->streamCsv($jobOrders, now()->format('Y-m-d'), null, 'Semua Data', true);
    }

    private function streamCsv($jobOrders, $date, $shift, $kapal, $isFullKapal)
    {
        if ($jobOrders->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data lengkap untuk diekspor');
        }

        $dateForDisplay = '';
        $dateForFileName = '';

        if ($kapal === 'Semua Data') {
            $firstDate = $jobOrders->min('Tanggal');
            $lastDate = $jobOrders->max('Tanggal');
            $formattedFirst = Carbon::parse($firstDate)->locale('id')->translatedFormat('d M Y');
            $formattedLast = Carbon::parse($lastDate)->locale('id')->translatedFormat('d M Y');
            $dateForDisplay = ($formattedFirst === $formattedLast) ? $formattedFirst : "$formattedFirst - $formattedLast";
            $dateForFileName = ($firstDate === $lastDate) ? $firstDate : "{$firstDate}_sampai_{$lastDate}";
        } else {
            $dateForDisplay = Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y');
            $dateForFileName = $date;
        }

        $fileName = $isFullKapal ? "Laporan - {$kapal} - {$dateForFileName}.csv" : "Laporan - {$kapal} - Shift {$shift} - {$dateForFileName}.csv";
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($jobOrders, $dateForDisplay, $shift, $kapal, $isFullKapal) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fwrite($file, "sep=;\n");

            $formatMinutes = function ($totalMinutes) {
                if (!is_numeric($totalMinutes) || $totalMinutes < 0)
                    return '0 menit';
                if ($totalMinutes == 0)
                    return '0 menit';
                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;
                $parts = [];
                if ($hours > 0)
                    $parts[] = $hours . ' jam';
                if ($minutes > 0)
                    $parts[] = $minutes . ' menit';
                return implode(' ', $parts);
            };

            $totalPlugToUnplugDurations = [];
            $fastestSlowestDurations = [];
            foreach ($jobOrders as $jobOrder) {
                $timeList = $jobOrder->timeList;
                if ($timeList) {
                    $jobDateCarbon = Carbon::parse($jobOrder->Tanggal);
                    if ($jobOrder->NoShift == 3) {
                        $jobDateCarbon->addDay();
                    }
                    $jobDate = $jobDateCarbon->toDateString();

                    $waktuTibaDateTime = $jobOrder->WaktuTiba ? $this->calculateFullTimestamp(null, $jobOrder->WaktuTiba, $jobDate) : null;
                    $pluggingDateTime = $this->calculateFullTimestamp($waktuTibaDateTime, $timeList->plugging, $jobDate);
                    $unpluggingDateTime = $this->calculateFullTimestamp($pluggingDateTime, $timeList->unplugging, $jobDate);

                    if ($pluggingDateTime && $unpluggingDateTime) {
                        $duration = $pluggingDateTime->diffInMinutes($unpluggingDateTime);
                        $totalPlugToUnplugDurations[] = $duration;
                        $fastestSlowestDurations[] = ['duration' => $duration, 'truck' => $jobOrder->NoTruck];
                    }
                }
            }

            $calculateAvg = function ($durations) use ($formatMinutes) {
                if (empty($durations))
                    return 'N/A';
                return $formatMinutes(round(array_sum($durations) / count($durations)));
            };

            $avgTotalDuration = $calculateAvg($totalPlugToUnplugDurations);
            $fastest = ['duration' => null, 'truck' => 'N/A'];
            $slowest = ['duration' => null, 'truck' => 'N/A'];

            if (!empty($fastestSlowestDurations)) {
                $fastest = ['duration' => INF, 'truck' => ''];
                $slowest = ['duration' => -1, 'truck' => ''];
                foreach ($fastestSlowestDurations as $item) {
                    if ($item['duration'] < $fastest['duration'])
                        $fastest = $item;
                    if ($item['duration'] > $slowest['duration'])
                        $slowest = $item;
                }
            }

            $uniqueTrucks = $jobOrders->pluck('NoTruck')->unique()->count();
            $totalRitase = $jobOrders->count();
            $delimiter = ';';

            fputcsv($file, ['Laporan Job Order'], $delimiter);
            fputcsv($file, [], $delimiter);

            if ($isFullKapal) {
                fputcsv($file, ['Tanggal', $dateForDisplay, '', 'Rata-rata Waktu Proses', $avgTotalDuration], $delimiter);
                fputcsv($file, ['Kapal', $kapal, '', 'Waktu Tercepat', $fastest['truck'], $formatMinutes($fastest['duration'])], $delimiter);
                fputcsv($file, ['Jumlah Truk', $uniqueTrucks, '', 'Waktu Terlama', $slowest['truck'], $formatMinutes($slowest['duration'])], $delimiter);
                fputcsv($file, ['Jumlah Ritase', $totalRitase], $delimiter);
            } else {
                fputcsv($file, ['Tanggal', $dateForDisplay, '', 'Rata-rata Waktu Proses', $avgTotalDuration], $delimiter);
                fputcsv($file, ['Shift', $shift, '', 'Waktu Tercepat', $fastest['truck'], $formatMinutes($fastest['duration'])], $delimiter);
                fputcsv($file, ['Jumlah Truk', $uniqueTrucks, '', 'Waktu Terlama', $slowest['truck'], $formatMinutes($slowest['duration'])], $delimiter);
                fputcsv($file, ['Jumlah Ritase', $totalRitase], $delimiter);
            }
            fputcsv($file, [], $delimiter);

            $baseHeaders = ['No', 'Nama (Input Data)', 'Nama (Input Waktu)', 'No Job Order', 'No Truck', 'Kategori', 'No Palka', 'No Hose', 'Waktu Tiba', 'Plugging', 'Open Valve', 'Close Valve', 'Unplugging', 'Durasi (Tiba -> Plugging)', 'Durasi (Plugging -> Open Valve)', 'Durasi (Open Valve -> Close Valve)', 'Durasi (Close Valve -> Unplugging)', 'Total Durasi (Plugging -> Unplugging)', 'Catatan'];

            if ($isFullKapal) {
                array_splice($baseHeaders, 1, 0, 'Tanggal');
                array_splice($baseHeaders, 2, 0, 'Shift');
            } else {
                array_splice($baseHeaders, 1, 0, 'Kapal');
            }
            fputcsv($file, $baseHeaders, $delimiter);

            $rowNumber = 1;
            foreach ($jobOrders as $jobOrder) {
                $timeList = $jobOrder->timeList;

                $jobDateCarbon = Carbon::parse($jobOrder->Tanggal);
                if ($jobOrder->NoShift == 3) {
                    $jobDateCarbon->addDay();
                }
                $jobDate = $jobDateCarbon->toDateString();

                $waktuTibaDateTime = $jobOrder->WaktuTiba ? $this->calculateFullTimestamp(null, $jobOrder->WaktuTiba, $jobDate) : null;
                $pluggingDateTime = $this->calculateFullTimestamp($waktuTibaDateTime, $timeList?->plugging, $jobDate);
                $openValveDateTime = $this->calculateFullTimestamp($pluggingDateTime, $timeList?->open_valve, $jobDate);
                $closeValveDateTime = $this->calculateFullTimestamp($openValveDateTime, $timeList?->close_valve, $jobDate);
                $unpluggingDateTime = $this->calculateFullTimestamp($closeValveDateTime, $timeList?->unplugging, $jobDate);

                $d1 = ($waktuTibaDateTime && $pluggingDateTime) ? $waktuTibaDateTime->diffInMinutes($pluggingDateTime) : null;
                $d2 = ($pluggingDateTime && $openValveDateTime) ? $pluggingDateTime->diffInMinutes($openValveDateTime) : null;
                $d3 = ($openValveDateTime && $closeValveDateTime) ? $openValveDateTime->diffInMinutes($closeValveDateTime) : null;
                $d4 = ($closeValveDateTime && $unpluggingDateTime) ? $closeValveDateTime->diffInMinutes($unpluggingDateTime) : null;
                $d5 = ($pluggingDateTime && $unpluggingDateTime) ? $pluggingDateTime->diffInMinutes($unpluggingDateTime) : null;

                $rowData = [
                    $rowNumber,
                    $jobOrder->user?->nama ?? 'N/A',
                    $timeList?->user?->nama ?? 'N/A',
                    $jobOrder->NoJobOrder,
                    $jobOrder->NoTruck,
                    $timeList?->kategori ?? 'N/A',
                    $timeList?->NoPalka ?? 'N/A',
                    $timeList?->NoHose ?? 'N/A',
                    $waktuTibaDateTime ? $waktuTibaDateTime->format('d/m/Y H:i') : 'N/A',
                    $pluggingDateTime ? $pluggingDateTime->format('d/m/Y H:i') : 'N/A',
                    $openValveDateTime ? $openValveDateTime->format('d/m/Y H:i') : 'N/A',
                    $closeValveDateTime ? $closeValveDateTime->format('d/m/Y H:i') : 'N/A',
                    $unpluggingDateTime ? $unpluggingDateTime->format('d/m/Y H:i') : 'N/A',
                    is_null($d1) ? 'N/A' : $formatMinutes($d1),
                    is_null($d2) ? 'N/A' : $formatMinutes($d2),
                    is_null($d3) ? 'N/A' : $formatMinutes($d3),
                    is_null($d4) ? 'N/A' : $formatMinutes($d4),
                    is_null($d5) ? 'N/A' : $formatMinutes($d5),
                    $timeList?->Catatan ?? ''
                ];

                if ($isFullKapal) {
                    array_splice($rowData, 1, 0, Carbon::parse($jobOrder->Tanggal)->format('d/m/Y'));
                    array_splice($rowData, 2, 0, $jobOrder->NoShift);
                } else {
                    array_splice($rowData, 1, 0, $jobOrder->Kapal);
                }

                fputcsv($file, $rowData, $delimiter);
                $rowNumber++;
            }
            fclose($file);
        };
        return new StreamedResponse($callback, 200, $headers);
    }

    private function calculateFullTimestamp($previousDateTime, $currentTimeString, $baseDateString)
    {
        if (!$currentTimeString)
            return null;

        $anchorDateTime = $previousDateTime ? $previousDateTime->copy() : Carbon::parse($baseDateString);
        $currentDateTime = Carbon::parse($anchorDateTime->toDateString() . ' ' . $currentTimeString);

        if ($previousDateTime && $currentDateTime->lessThan($anchorDateTime)) {
            $currentDateTime->addDay();
        }
        return $currentDateTime;
    }

    private function getDiffInMinutes($start, $end, $jobDate)
    {
        if (!$start || !$end)
            return null;
        try {
            $startTime = $this->calculateFullTimestamp(null, $start, $jobDate);
            $endTime = $this->calculateFullTimestamp($startTime, $end, $jobDate);
            if (!$startTime || !$endTime)
                return null;

            return $startTime->diffInMinutes($endTime);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function normalizeToSeconds(string $time = null)
    {
        if ($time === null)
            return null;
        $time = trim($time);
        if (preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time)) {
            return $time . ':00';
        }
        if (preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d$/', $time)) {
            return $time;
        }
        return null;
    }

    public function batal(Request $request, $id)
    {
        $validated = $request->validate([
            'Catatan' => 'nullable|string|max:255',
        ]);

        $jobOrder = JobOrder::findOrFail($id);

        DB::transaction(function () use ($jobOrder, $validated) {
            $jobOrder->status = 'Batal';
            $jobOrder->save();

            DB::table('time_lists')->updateOrInsert(
                ['job_order_id' => $jobOrder->id],
                [
                    'Catatan' => $validated['Catatan'],
                    'user_id' => Auth::id(),
                    'truck_no' => $jobOrder->NoTruck,
                    'updated_at' => now(),
                    'created_at' => DB::table('time_lists')
                        ->where('job_order_id', $jobOrder->id)
                        ->exists() ? DB::raw('created_at') : now()
                ]
            );
        });

        return response()->json([
            'success' => true,
            'message' => "Job Order {$jobOrder->NoJobOrder} berhasil dibatalkan",
            'jobOrder' => $jobOrder->fresh()->load(['timeList.user', 'user'])
        ]);
    }

    public function index(Request $request)
    {
        $query = JobOrder::with(['timeList.user', 'user']);
        $query = $this->applyCabangFilter($query);
        $jobOrders = $query->orderBy('created_at', 'desc')->get();

        return response()->json($jobOrders);
    }
}
