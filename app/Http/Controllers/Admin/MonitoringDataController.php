<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonitoringDataController extends Controller
{
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

        if ($access['cabang_id']) {
            $query->leftJoin('akses_menus', "{$userTableAlias}.id", '=', 'akses_menus.user_id')
                  ->where('akses_menus.cabang_id', $access['cabang_id']);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = DB::table('job_orders as j')
            ->leftJoin('time_lists as t', 'j.id', '=', 't.job_order_id')
            ->leftJoin('users as u_job', 'j.user_id', '=', 'u_job.id')
            ->leftJoin('users as u_time', 't.user_id', '=', 'u_time.id')
            ->select(
                'j.id', 'j.Tanggal as tanggal', 'j.NoShift as shift', 'j.Kapal as kapal_nama',
                'j.NoJobOrder as no_job_order', 'j.NoTruck as no_truck', 'j.WaktuTiba as waktu_tiba',
                't.plugging', 't.open_valve', 't.close_valve', 't.unplugging', 't.kategori',
                'j.status as status', 't.Catatan as catatan', 'u_job.nama as petugas_joborder',
                'u_time.nama as petugas_timelist', 'j.created_at', 'j.updated_at'
            );

        $query = $this->applyCabangFilterToQueryBuilder($query, 'u_job');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('j.NoJobOrder', 'like', '%'.$request->search.'%')
                  ->orWhere('j.Kapal', 'like', '%'.$request->search.'%')
                  ->orWhere('j.NoTruck', 'like', '%'.$request->search.'%');
            });
        }

        $data = $query->orderBy('j.id', 'desc')->paginate(10);

        if ($request->ajax()) {
            return response()->json(['data' => $data]);
        }

        return view('backend.monitoring-data.index');
    }

    private function formatTglJam($value)
    {
        if (!$value || $value == '-') return '-';
        try {
            return Carbon::parse($value)->format('d/m/Y H:i');
        } catch (\Exception $e) {
            return $value;
        }
    }

    private function hitungDurasi($start, $end)
    {
        if (!$start || !$end || $start == '-' || $end == '-') return '-';
        try {
            $s = Carbon::parse($start);
            $e = Carbon::parse($end);
            $diff = $s->diff($e);
            $res = [];
            if ($diff->d > 0) $res[] = $diff->d . ' hari';
            if ($diff->h > 0) $res[] = $diff->h . ' jam';
            if ($diff->i > 0) $res[] = $diff->i . ' menit';
            return count($res) > 0 ? implode(' ', $res) : '0 menit';
        } catch (\Exception $ex) {
            return '-';
        }
    }

    public function export(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $search = $request->input('search');

        $start = Carbon::createFromFormat('d-m-Y', $fromDate)->startOfDay();
        $end = Carbon::createFromFormat('d-m-Y', $toDate)->endOfDay();

        $query = DB::table('job_orders as j')
            ->leftJoin('time_lists as t', 'j.id', '=', 't.job_order_id')
            ->leftJoin('users as u_job', 'j.user_id', '=', 'u_job.id')
            ->leftJoin('users as u_time', 't.user_id', '=', 'u_time.id')
            ->select(
                'j.Tanggal', 'j.NoShift', 'j.Kapal', 'u_job.nama as petugas_job',
                'u_time.nama as petugas_time', 'j.NoJobOrder', 'j.NoTruck',
                't.kategori', 't.NoPalka', 't.NoHose', 'j.WaktuTiba',
                't.plugging', 't.open_valve', 't.close_valve', 't.unplugging', 't.Catatan'
            );

        $query = $this->applyCabangFilterToQueryBuilder($query, 'u_job');
        $query->whereBetween('j.Tanggal', [$start, $end]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('j.NoJobOrder', 'like', '%' . $search . '%')
                  ->orWhere('j.Kapal', 'like', '%' . $search . '%')
                  ->orWhere('j.NoTruck', 'like', '%' . $search . '%');
            });
        }

        $data = $query->orderBy('j.Tanggal', 'asc')->get();

        $durasiSmd = [];
        foreach ($data as $d) {
            if ($d->plugging && $d->unplugging) {
                $diff = Carbon::parse($d->plugging)->diffInMinutes(Carbon::parse($d->unplugging));
                $durasiSmd[] = ['truck' => $d->NoTruck, 'menit' => $diff];
            }
        }

        $avgMenit = count($durasiSmd) > 0 ? collect($durasiSmd)->avg('menit') : 0;
        $tercepat = count($durasiSmd) > 0 ? collect($durasiSmd)->sortBy('menit')->first() : null;
        $terlama = count($durasiSmd) > 0 ? collect($durasiSmd)->sortByDesc('menit')->first() : null;

        $formatDurasiSimple = function($totalMenit) {
            $h = floor($totalMenit / 60);
            $m = round($totalMenit % 60);
            return ($h > 0 ? $h . ' jam ' : '') . $m . ' menit';
        };

        $fileName = 'Laporan Job Order - ' . $fromDate . ' s.d ' . $toDate . '.csv';
        $headers = ["Content-type" => "text/csv", "Content-Disposition" => "attachment; filename=$fileName"];

        $callback = function() use ($data, $fromDate, $toDate, $formatDurasiSimple, $avgMenit, $tercepat, $terlama) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Laporan Job Order']);
            fputcsv($file, []);
            fputcsv($file, ['Tanggal', $fromDate . ' s.d ' . $toDate, '', 'Rata-rata Waktu Proses', $formatDurasiSimple($avgMenit)]);
            fputcsv($file, ['Jumlah Truk', $data->unique('NoTruck')->count(), '', 'Waktu Tercepat', $tercepat ? $tercepat['truck'] : '-', $tercepat ? $formatDurasiSimple($tercepat['menit']) : '-']);
            fputcsv($file, ['Jumlah Ritase', $data->count(), '', 'Waktu Terlama', $terlama ? $terlama['truck'] : '-', $terlama ? $formatDurasiSimple($terlama['menit']) : '-']);
            fputcsv($file, []);
            fputcsv($file, [
                'No', 'Kapal', 'Shift', 'Nama (Input Data)', 'Nama (Input Waktu)', 'No Job Order', 'No Truck',
                'Kategori', 'No Palka', 'No Hose', 'Waktu Tiba', 'Plugging', 'Open Valve', 'Close Valve', 'Unplugging',
                'Durasi (Tiba -> Plugging)', 'Durasi (Plugging -> Open Valve)', 'Durasi (Open Valve -> Close Valve)',
                'Durasi (Close Valve -> Unplugging)', 'Total Durasi (Plugging -> Unplugging)', 'Catatan'
            ]);
            foreach ($data as $index => $row) {
                fputcsv($file, [
                    $index + 1, $row->Kapal, $row->NoShift, $row->petugas_job, $row->petugas_time,
                    $row->NoJobOrder, $row->NoTruck, $row->kategori, $row->NoPalka, $row->NoHose,
                    $this->formatTglJam($row->WaktuTiba), $this->formatTglJam($row->plugging),
                    $this->formatTglJam($row->open_valve), $this->formatTglJam($row->close_valve),
                    $this->formatTglJam($row->unplugging),
                    $this->hitungDurasi($row->WaktuTiba, $row->plugging),
                    $this->hitungDurasi($row->plugging, $row->open_valve),
                    $this->hitungDurasi($row->open_valve, $row->close_valve),
                    $this->hitungDurasi($row->close_valve, $row->unplugging),
                    $this->hitungDurasi($row->plugging, $row->unplugging),
                    $row->Catatan,
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'no_truck'   => 'nullable|string|max:50',
            'waktu_tiba' => 'nullable|string|max:25',
            'plugging'   => 'nullable|string|max:25',
            'open_valve' => 'nullable|string|max:25',
            'close_valve'=> 'nullable|string|max:25',
            'unplugging' => 'nullable|string|max:25',
            'kategori'   => 'nullable|string|max:50',
            'status'     => 'nullable|string|max:50',
            'catatan'    => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($validated, $id) {
                DB::table('job_orders')->where('id', $id)->update([
                    'NoTruck'    => $validated['no_truck'],
                    'WaktuTiba'  => $validated['waktu_tiba'],
                    'status'     => $validated['status'],
                    'updated_at' => now(),
                ]);

                DB::table('time_lists')->where('job_order_id', $id)->update([
                    'plugging'   => $validated['plugging'],
                    'open_valve' => $validated['open_valve'],
                    'close_valve'=> $validated['close_valve'],
                    'unplugging' => $validated['unplugging'],
                    'kategori'   => $validated['kategori'],
                    'Catatan'    => $validated['catatan'],
                    'updated_at' => now(),
                ]);
            });
            return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui!']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}