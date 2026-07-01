<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterKapal;
use App\Models\JobOrder;
use App\Models\User;
use App\Models\TimeList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private function getChartData(string $modelClass, string $statusValue)
    {
        $data = $modelClass::select(
            DB::raw("YEAR(created_at) as year"),
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"),
            DB::raw('count(*) as cnt')
        )
            ->where('status', 'LIKE', $statusValue)
            ->groupBy('year', 'ym')
            ->orderBy('year', 'asc')
            ->get();

        $chartData = [];
        foreach ($data as $item) {
            $year = (string) $item->year;
            $monthIndex = (int) Carbon::parse($item->ym)->format('n') - 1;

            if (!isset($chartData[$year])) {
                $chartData[$year] = array_fill(0, 12, 0);
            }
            $chartData[$year][$monthIndex] = $item->cnt;
        }

        return $chartData;
    }

    private function getJobOrderActiveChartData()
    {
        $data = JobOrder::select(
            DB::raw("YEAR(created_at) as year"),
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"),
            DB::raw('count(*) as cnt')
        )
            ->where('status', 'NOT LIKE', 'nonaktif')
            ->where('status', 'NOT LIKE', 'cancel')
            ->groupBy('year', 'ym')
            ->orderBy('year', 'asc')
            ->get();

        $chartData = [];
        foreach ($data as $item) {
            $year = (string) $item->year;
            $monthIndex = (int) Carbon::parse($item->ym)->format('n') - 1;

            if (!isset($chartData[$year])) {
                $chartData[$year] = array_fill(0, 12, 0);
            }
            $chartData[$year][$monthIndex] = $item->cnt;
        }

        return $chartData;
    }

    private function getDistinctYears()
    {
        $years_kapal = MasterKapal::select(DB::raw('YEAR(created_at) as year'))->distinct()->pluck('year')->toArray();
        $years_job = JobOrder::select(DB::raw('YEAR(created_at) as year'))->distinct()->pluck('year')->toArray();
        $years_user = User::select(DB::raw('YEAR(created_at) as year'))->distinct()->pluck('year')->toArray();

        $years = array_unique(array_merge($years_kapal, $years_job, $years_user));
        rsort($years);

        return $years;
    }

    public function index(Request $request)
    {
        $selectedKapal = $request->get('kapal', 'all');
        $selectedTruck = $request->get('truck', 'all');
        $selectedYear = $request->get('year', 'all');
        $selectedMonth = $request->get('month', 'all');
        $selectedRitaseKapal = $request->get('ritase_kapal', 'all');
        $selectedRitaseYear = $request->get('ritase_tahun', 'all');

        $kapalList = JobOrder::select('Kapal')
            ->distinct()
            ->where('status', 'Aktif')
            ->orderBy('Kapal', 'asc')
            ->pluck('Kapal')
            ->toArray();

        $aktifQuery = JobOrder::where('status', 'Aktif');
        $batalQuery = JobOrder::where('status', 'Batal');
        $monitoringQuery = JobOrder::where('status', 'Aktif')
            ->whereHas('timeList', function ($query) {
                $query->whereNotNull('plugging')->whereNull('unplugging');
            })
            ->with('timeList');

        $nonaktifQuery = JobOrder::where(function ($query) {
            $query->whereHas('timeList', function ($q) {
                $q->whereNotNull('unplugging');
            })->orWhere('status', '!=', 'Aktif');
        });

        foreach ([$aktifQuery, $batalQuery, $monitoringQuery, $nonaktifQuery] as $query) {
            if ($selectedKapal !== 'all') $query->where('Kapal', $selectedKapal);
            if ($selectedYear !== 'all') $query->whereYear('created_at', $selectedYear);
            if ($selectedMonth !== 'all') $query->whereMonth('created_at', $selectedMonth);
        }

        $jobOrderAktif = $aktifQuery->count();
        $jobOrderBatal = $batalQuery->count();
        $jobOrderNonaktif = $nonaktifQuery->count();
        $monitoringData = $monitoringQuery->orderBy('created_at', 'desc')->get();
        
        $jobOrderAktifList = $monitoringData->map(fn($item) => [
            'id' => $item->id,
            'NoJobOrder' => $item->NoJobOrder,
            'NoTruck' => $item->NoTruck
        ]);

        $jobOrderNonaktifList = $nonaktifQuery->orderBy('created_at', 'desc')->get()->map(fn($item) => [
            'id' => $item->id,
            'NoJobOrder' => $item->NoJobOrder,
            'NoTruck' => $item->NoTruck
        ]);

        $trucksWithRitase = $this->calculateRitaseSummary($selectedKapal, $selectedYear);
        $completedTrucks = $trucksWithRitase->where('ritase_minutes', '>', 0);
        $fastestTruck = $completedTrucks->sortBy('ritase_minutes')->first();
        $slowestTruck = $completedTrucks->sortByDesc('ritase_minutes')->first();
        $activeTrucks = $trucksWithRitase->pluck('NoTruck')->unique()->count();

        $selectedRitaseKapal = ($selectedRitaseKapal === 'all') ? $selectedKapal : $selectedRitaseKapal;
        $selectedRitaseYear = ($selectedRitaseYear === 'all') ? $selectedYear : $selectedRitaseYear;

        $ritaseData = $this->calculateRitaseChartData($selectedRitaseKapal, $selectedRitaseYear, $selectedTruck);
        $ritaseChartData = $ritaseData['chartData'];
        $truckList = $ritaseData['truckList'];

        $totalKapal = MasterKapal::where('status', 'aktif')->count();
        $totalUser = User::where('status', 'aktif')->count();
        $totalJobOrder = JobOrder::whereNotIn('status', ['nonaktif', 'cancel'])->count();
        $totalMonitoring = JobOrder::where('status', 'nonaktif')->count();

        $chartKapal = $this->getChartData(MasterKapal::class, 'aktif');
        $chartUser = $this->getChartData(User::class, 'aktif');
        $chartJobOrder = $this->getJobOrderActiveChartData();
        $chartMonitoring = $this->getChartData(JobOrder::class, 'nonaktif');

        $tahunList = $this->getDistinctYears() ?: [date('Y')];
        $defaultYear = (string) reset($tahunList);
        $chartData = $this->getMonitoringChartData($selectedKapal, $selectedYear);

        return view('backend.dashboard.index', compact(
            'totalKapal', 'totalJobOrder', 'totalUser', 'totalMonitoring', 'tahunList',
            'chartKapal', 'chartJobOrder', 'chartUser', 'chartMonitoring', 'defaultYear',
            'kapalList', 'selectedKapal', 'selectedTruck', 'selectedYear', 'selectedMonth',
            'selectedRitaseKapal', 'selectedRitaseYear', 'monitoringData', 'chartData',
            'jobOrderAktif', 'jobOrderAktifList', 'jobOrderNonaktif', 'jobOrderNonaktifList',
            'jobOrderBatal', 'trucksWithRitase', 'activeTrucks', 'fastestTruck', 'slowestTruck', 
            'truckList', 'ritaseChartData'
        ))->with([
            'defaultKapalValues' => $chartKapal[$defaultYear] ?? array_fill(0, 12, 0),
            'defaultJobOrderValues' => $chartJobOrder[$defaultYear] ?? array_fill(0, 12, 0),
            'defaultUserValues' => $chartUser[$defaultYear] ?? array_fill(0, 12, 0),
            'defaultMonitoringValues' => $chartMonitoring[$defaultYear] ?? array_fill(0, 12, 0),
        ]);
    }

    private function calculateRitaseSummary($kapal, $year)
    {
        $query = DB::table('job_orders as j')
            ->leftJoin('time_lists as t', 'j.id', '=', 't.job_order_id')
            ->select('j.id', 'j.Kapal', 'j.NoTruck', 'j.NoJobOrder', 'j.WaktuTiba', 'j.Tanggal', 't.plugging', 't.unplugging')
            ->where('j.status', 'Aktif');

        if ($kapal !== 'all') $query->where('j.Kapal', $kapal);
        if ($year !== 'all') $query->whereYear('j.created_at', $year);

        return $query->get()->groupBy('NoTruck')->map(function ($group, $truckNo) {
            $totalMinutes = 0;
            $count = 0;
            $sorted = $group->sortBy(fn($t) => Carbon::parse($t->Tanggal . ' ' . ($t->WaktuTiba ?? '00:00:00'))->timestamp);
            $first = $sorted->first();

            foreach ($sorted as $truck) {
                if ($truck->WaktuTiba && $truck->unplugging) {
                    $start = Carbon::parse($truck->Tanggal . ' ' . $truck->WaktuTiba);
                    $end = Carbon::parse($truck->Tanggal . ' ' . $truck->unplugging);
                    if ($end->lt($start)) $end->addDay();
                    $diff = $start->diffInMinutes($end);
                    if ($diff > 0) {
                        $totalMinutes += $diff;
                        $count++;
                    }
                }
            }

            return (object) [
                'id' => $first->id, 'Kapal' => $first->Kapal, 'NoTruck' => $truckNo,
                'NoJobOrder' => $first->NoJobOrder, 'WaktuTiba' => $first->WaktuTiba,
                'Tanggal' => $first->Tanggal, 'plugging' => $first->plugging,
                'unplugging' => $first->unplugging, 'ritase_minutes' => $totalMinutes,
                'ritase_formatted' => $totalMinutes > 0 ? (string)$totalMinutes : '-',
                'job_order_count' => $count
            ];
        })->values();
    }

    private function calculateRitaseChartData($kapal, $year, $selectedTruck)
    {
        $query = DB::table('job_orders as j')
            ->leftJoin('time_lists as t', 'j.id', '=', 't.job_order_id')
            ->select('j.Kapal', 'j.NoTruck', 'j.WaktuTiba', 'j.Tanggal', 't.unplugging')
            ->where('j.status', 'Aktif');

        if ($kapal !== 'all') $query->where('j.Kapal', $kapal);
        if ($year !== 'all') $query->whereYear('j.created_at', $year);

        $data = $query->get();
        $truckList = $data->pluck('NoTruck')->unique()->sort()->values()->toArray();
        $chartData = [];

        if ($selectedTruck === 'all') {
            $chartData = $data->groupBy('NoTruck')->map(function ($group, $no) {
                $total = 0;
                foreach ($group as $t) {
                    if ($t->WaktuTiba && $t->unplugging) {
                        $s = Carbon::parse($t->Tanggal . ' ' . $t->WaktuTiba);
                        $e = Carbon::parse($t->Tanggal . ' ' . $t->unplugging);
                        if ($e->lt($s)) $e->addDay();
                        $total += $s->diffInMinutes($e);
                    }
                }
                return ['label' => $no, 'ritase' => (int)$total];
            })->filter(fn($item) => $item['ritase'] > 0)->sortByDesc('ritase')->values()->toArray();
        } else {
            $sorted = $data->where('NoTruck', $selectedTruck)->sortBy(fn($t) => Carbon::parse($t->Tanggal . ' ' . ($t->WaktuTiba ?? '00:00:00'))->timestamp);
            $phase = 1;
            foreach ($sorted as $t) {
                if ($t->WaktuTiba && $t->unplugging) {
                    $s = Carbon::parse($t->Tanggal . ' ' . $t->WaktuTiba);
                    $e = Carbon::parse($t->Tanggal . ' ' . $t->unplugging);
                    if ($e->lt($s)) $e->addDay();
                    $chartData[] = ['label' => 'Fase ' . $phase++, 'ritase' => (int)$s->diffInMinutes($e)];
                }
            }
        }

        return ['chartData' => $chartData, 'truckList' => $truckList];
    }

    private function getMonitoringChartData($selectedKapal = 'all', $selectedYear = 'all')
    {
        $query = JobOrder::where('status', 'Aktif')
            ->whereHas('timeList', fn($q) => $q->whereNotNull('plugging')->whereNull('unplugging'));

        if ($selectedKapal !== 'all') $query->where('Kapal', $selectedKapal);
        if ($selectedYear !== 'all') $query->whereYear('created_at', $selectedYear);

        $data = $query->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"), DB::raw('count(*) as cnt'))
            ->groupBy('ym')->orderBy('ym', 'asc')->get();

        $chartData = array_fill(0, 12, 0);
        foreach ($data as $item) {
            $chartData[(int)Carbon::parse($item->ym)->format('n') - 1] = $item->cnt;
        }

        return array_values($chartData);
    }

    public function getChartDataAjax(Request $request)
    {
        $selectedKapal = $request->get('kapal', 'all');
        $selectedTruck = $request->get('truck', 'all');
        $selectedYear = $request->get('year', 'all');
        $selectedMonth = $request->get('month', 'all');

        $aktifQuery = JobOrder::where('status', 'Aktif');
        $batalQuery = JobOrder::where('status', 'Batal');
        $nonaktifQuery = JobOrder::where(function ($query) {
            $query->whereHas('timeList', fn($q) => $q->whereNotNull('unplugging'))->orWhere('status', '!=', 'Aktif');
        });

        foreach ([$aktifQuery, $batalQuery, $nonaktifQuery] as $q) {
            if ($selectedKapal !== 'all') $q->where('Kapal', $selectedKapal);
            if ($selectedYear !== 'all') $q->whereYear('created_at', $selectedYear);
            if ($selectedMonth !== 'all') $q->whereMonth('created_at', $selectedMonth);
        }

        $ritaseData = $this->calculateRitaseChartData($selectedKapal, $selectedYear, $selectedTruck);
        $mainTrucks = $this->calculateRitaseSummary($selectedKapal, $selectedYear);
        $completed = $mainTrucks->where('ritase_minutes', '>', 0);
        $fastest = $completed->sortBy('ritase_minutes')->first();
        $slowest = $completed->sortByDesc('ritase_minutes')->first();

        return response()->json([
            'chartData' => $this->getMonitoringChartData($selectedKapal, $selectedYear),
            'ritaseChartData' => $ritaseData['chartData'],
            'truckList' => $ritaseData['truckList'],
            'jobOrderAktif' => $aktifQuery->count(),
            'jobOrderNonaktif' => $nonaktifQuery->count(),
            'jobOrderBatal' => $batalQuery->count(),
            'fastestTruck' => $fastest ? ['NoTruck' => $fastest->NoTruck, 'NoJobOrder' => $fastest->NoJobOrder, 'ritase_minutes' => (int)round($fastest->ritase_minutes)] : null,
            'slowestTruck' => $slowest ? ['NoTruck' => $slowest->NoTruck, 'NoJobOrder' => $slowest->NoJobOrder, 'ritase_minutes' => (int)round($slowest->ritase_minutes)] : null
        ]);
    }
}