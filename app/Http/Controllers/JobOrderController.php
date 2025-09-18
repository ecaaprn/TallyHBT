<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;

class JobOrderController extends Controller
{
    public function monitoring()
    {
        $jobOrders = JobOrder::with(['timeList.user', 'user'])->orderBy('Tanggal', 'asc')->orderBy('NoShift', 'asc')->get();
        return view('frontend.monitoring', ['jobOrders' => $jobOrders]);
    }

    public function input(Request $request, $date, $shift, $kapal = null)
    {
        $query = JobOrder::with(['timeList.user', 'user'])
            ->where('Tanggal', $date)
            ->where('NoShift', $shift);

        if ($kapal) {
            $query->where('Kapal', $kapal);
        }

        $jobOrders = $query->orderBy('created_at', 'desc')->get();

        return view('frontend.job-order', [
            'jobOrders' => $jobOrders,
            'currentDate' => Carbon::parse($date),
            'currentShift' => $shift,
            'selectedKapal' => $kapal,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'Tanggal' => 'required|date',
                'NoShift' => 'required|integer',
                'Kapal' => 'required|string|max:255',
                'NoJobOrder' => 'required|string|max:255|unique:job_orders,NoJobOrder',
                'NoTruck' => ['required', Rule::in(['Truck-1', 'Truck-2', 'Truck-3'])],
                'WaktuTiba' => 'required|date_format:H:i',
            ]);

            $validated['user_id'] = Auth::id();

            $jobOrder = DB::transaction(function () use ($validated) {
                $jobOrder = JobOrder::create($validated);

                $jobOrder->timeList()->create([
                    'truck_no' => $validated['NoTruck'],
                    'user_id' => $validated['user_id'],
                ]);

                return $jobOrder;
            });

            return response()->json($jobOrder->load(['timeList.user', 'user']));
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function update(Request $request, JobOrder $jobOrder)
    {
        try {
            $request->validate([
                'NoTruck' => ['sometimes', 'required', Rule::in(['Truck-1', 'Truck-2', 'Truck-3'])],
                'WaktuTiba' => 'nullable|date_format:H:i:s,H:i',
                'time_list' => 'nullable|array',
            ]);

            DB::transaction(function () use ($request, $jobOrder) {
                if ($request->has('NoTruck')) {
                    $jobOrder->update(['NoTruck' => $request->input('NoTruck')]);
                }
                if ($request->has('WaktuTiba')) {
                    $jobOrder->update(['WaktuTiba' => $this->normalizeToSeconds($request->input('WaktuTiba'))]);
                }

                if ($request->has('time_list')) {
                    $timeListData = $request->input('time_list');

                    foreach ($timeListData as $key => $value) {
                        if (in_array($key, ['plugging', 'open_valve', 'close_valve', 'unplugging'])) {
                            $timeListData[$key] = $this->normalizeToSeconds($value);
                        }
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
                        $jobOrder->timeList()->update($timeListData);
                    }
                }
            });

            $jobOrder->refresh();

            return response()->json($jobOrder->load(['timeList.user', 'user']));
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function export($date, $shift, $kapal)
    {
        $jobOrders = JobOrder::with('timeList')
            ->where('Tanggal', $date)
            ->where('NoShift', $shift)
            ->where('Kapal', $kapal)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($jobOrders->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor.');
        }

        $fileName = "Laporan - {$kapal} - Shift {$shift} - " . Carbon::parse($date)->format('d-m-Y') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($jobOrders, $date, $shift, $kapal) {
            $file = fopen('php://output', 'w');

            $allDurations = [];
            foreach ($jobOrders as $jobOrder) {
                if ($jobOrder->timeList && $jobOrder->timeList->plugging && $jobOrder->timeList->unplugging) {
                    $duration = $this->getDiffInMinutes($jobOrder->timeList->plugging, $jobOrder->timeList->unplugging, $date);
                    if (is_numeric($duration)) {
                        $allDurations[] = [
                            'duration' => $duration,
                            'truck' => $jobOrder->NoTruck
                        ];
                    }
                }
            }

            $formatMinutes = function($totalMinutes) {
                if (!is_numeric($totalMinutes) || $totalMinutes < 0) return '0 menit';
                if ($totalMinutes == 0) return '0 menit';
                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;
                $parts = [];
                if ($hours > 0) $parts[] = $hours . ' jam';
                if ($minutes > 0) $parts[] = $minutes . ' menit';
                return implode(' ', $parts);
            };

            $avgDuration = 0;
            $fastest = ['duration' => 0, 'truck' => ''];
            $slowest = ['duration' => 0, 'truck' => ''];

            if (!empty($allDurations)) {
                $avgDuration = array_sum(array_column($allDurations, 'duration')) / count($allDurations);

                $fastest = ['duration' => INF, 'truck' => ''];
                $slowest = ['duration' => -1, 'truck' => ''];

                foreach ($allDurations as $item) {
                    if ($item['duration'] < $fastest['duration']) $fastest = $item;
                    if ($item['duration'] > $slowest['duration']) $slowest = $item;
                }
            }

            $uniqueTrucks = $jobOrders->pluck('NoTruck')->unique()->count();
            $totalRitase = $jobOrders->count();

            fputcsv($file, ['Laporan Job Order', '', '', 'Rata2 Waktu/Segmen (Plugging -> Unplugging)', 'Semua Truk', $formatMinutes($avgDuration)]);
            fputcsv($file, ['Tanggal', Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y'), '', 'Waktu Tercepat Truk', $fastest['truck'], $formatMinutes($fastest['duration'])]);
            fputcsv($file, ['Shift', $shift, '', 'Waktu Terlama Truk', $slowest['truck'], $formatMinutes($slowest['duration'])]);
            fputcsv($file, ['Kapal', $kapal]);
            fputcsv($file, ['Jumlah Truk', $uniqueTrucks]);
            fputcsv($file, ['Jumlah Ritase', $totalRitase]);
            fputcsv($file, []);

            $columnTitles = ['No', 'No Job Order', 'No Truck', 'Kapal', 'No Palka', 'No Hose', 'Waktu Tiba', 'Plugging', 'Open Valve', 'Close Valve', 'Unplugging', 'Total Durasi'];
            fputcsv($file, $columnTitles);

            foreach ($jobOrders as $index => $jobOrder) {
                $timeList = $jobOrder->timeList;
                $totalDuration = 'N/A';
                if ($timeList && $timeList->plugging && $timeList->unplugging) {
                    $totalDuration = $formatMinutes($this->getDiffInMinutes($timeList->plugging, $timeList->unplugging, $date));
                }

                fputcsv($file, [
                    $index + 1,
                    $jobOrder->NoJobOrder,
                    $jobOrder->NoTruck,
                    $jobOrder->Kapal,
                    $timeList->NoPalka ?? 'N/A',
                    $timeList->NoHose ?? 'N/A',
                    $jobOrder->WaktuTiba ? Carbon::parse($jobOrder->WaktuTiba)->format('H:i') : 'N/A',
                    $timeList->plugging ? Carbon::parse($timeList->plugging)->format('H:i') : 'N/A',
                    $timeList->open_valve ? Carbon::parse($timeList->open_valve)->format('H:i') : 'N/A',
                    $timeList->close_valve ? Carbon::parse($timeList->close_valve)->format('H:i') : 'N/A',
                    $timeList->unplugging ? Carbon::parse($timeList->unplugging)->format('H:i') : 'N/A',
                    $totalDuration
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    private function getDiffInMinutes($start, $end, $jobDate)
    {
        if (!$start || !$end) return null;
        try {
            $startTime = Carbon::parse($jobDate . ' ' . $start);
            $endTime = Carbon::parse($jobDate . ' ' . $end);
            if ($endTime->lessThan($startTime)) {
                $endTime->addDay();
            }
            return $startTime->diffInMinutes($endTime);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function normalizeToSeconds(string $time = null)
    {
        if ($time === null) return null;
        $time = trim($time);
        if (preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time)) {
            return $time . ':00';
        }
        if (preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d$/', $time)) {
            return $time;
        }
        return null;
    }
}
