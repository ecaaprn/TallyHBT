@extends('layouts.app')

@section('title', 'Data Monitoring')

@section('content')
    <style>
        thead { border-bottom: 2px solid #dee2e6; }
        tbody { border-bottom: 1px solid #dee2e6; }
        tbody:last-of-type { border-bottom: none; }
        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }
    </style>
<div class="container">
    <div class="d-flex flex-column gap-4">
        <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-stretch align-items-md-center gap-2">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('menu') }}" class="btn btn-link text-secondary fs-5 p-0" title="Kembali">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h2 class="fs-2 fw-bold text-dark mb-0 text-nowrap">DATA MONITORING</h2>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm">
                    <input type="text" id="date-search-input" class="form-control" placeholder="Cari Tanggal / Kapal">
                </div>
                <a href="#" class="btn btn-danger btn-sm shadow-sm" title="Keluar" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="p-2 p-md-3 bg-white rounded-3 shadow-sm">
            <table id="monitoring-table" class="table table-borderless mb-0">
                <thead>
                    <tr>
                        <th class="text-center py-2" style="width:5%;">No</th>
                        <th class="text-center py-2 text-nowrap" style="width:30%;">Tanggal</th>
                        <th class="text-center py-2" style="width:15%;">Shift</th>
                        <th class="text-center py-2" style="width:20%;">Kapal</th>
                        <th class="text-center py-2" style="width:30%;">Aksi</th>
                    </tr>
                </thead>
                @php
                    $groupedByDate = $jobOrders->groupBy('Tanggal');
                    $iteration = 0;
                @endphp
                @forelse ($groupedByDate as $tanggal => $jobsByDate)
                    @php
                        $groupedByShift = $jobsByDate->groupBy('NoShift');
                        $totalRowsForDate = 0;
                        foreach ($groupedByShift as $jobsByShift) {
                            $totalRowsForDate += $jobsByShift->groupBy('Kapal')->count();
                        }
                        $isFirstDateRow = true;
                        $iteration++;
                    @endphp
                    <tbody data-date="{{ $tanggal }}">
                        @foreach ($groupedByShift as $shiftNumber => $jobsByShift)
                            @php
                                $groupedByKapal = $jobsByShift->groupBy('Kapal');
                                $isFirstShiftRow = true;
                            @endphp
                            @foreach ($groupedByKapal as $namaKapal => $jobs)
                                <tr>
                                    @if ($isFirstDateRow && $isFirstShiftRow)
                                        <td class="text-center align-middle" rowspan="{{ $totalRowsForDate }}">{{ $iteration }}</td>
                                        <td class="text-center align-middle" rowspan="{{ $totalRowsForDate }}">{{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y') }}</td>
                                    @endif

                                    @if ($isFirstShiftRow)
                                        <td class="text-center align-middle" rowspan="{{ $groupedByKapal->count() }}">{{ $shiftNumber }}</td>
                                    @endif

                                    <td class="text-center align-middle">{{ $namaKapal }}</td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex flex-wrap justify-content-center gap-2">
                                            <a href="{{ route('job-order.input', ['date' => $tanggal, 'shift' => $shiftNumber, 'kapal' => $namaKapal]) }}" class="btn btn-sm btn-primary btn-icon" title="Lihat/Ubah Data">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <a href="{{ route('export', ['date' => $tanggal, 'shift' => $shiftNumber, 'kapal' => $namaKapal]) }}" class="btn btn-sm btn-success btn-icon" title="Unduh Laporan">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @php
                                    $isFirstShiftRow = false;
                                    $isFirstDateRow = false;
                                @endphp
                            @endforeach
                        @endforeach
                    </tbody>
                @empty
                    <tbody>
                        <tr><td colspan="5" class="text-center text-muted p-4">Tidak ada data.</td></tr>
                    </tbody>
                @endforelse
                <tbody id="no-results-row" style="display: none;">
                    <tr>
                        <td colspan="5" class="text-center text-muted p-4">Data tidak ditemukan.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Konfirmasi Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                Apakah Anda yakin ingin keluar dari akun?
            </div>
            <div class="modal-footer d-flex justify-content-center gap-3 border-0 pt-0">
                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Ya</button>
                </form>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tidak</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('date-search-input');
    const table = document.getElementById('monitoring-table');
    const allTbody = table.querySelectorAll('tbody[data-date]');
    const noResultsRow = document.getElementById('no-results-row');

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        let visibleCount = 0;

        allTbody.forEach(tbody => {
            const date = tbody.dataset.date || '';
            const formattedDateCell = tbody.querySelector('td[rowspan]');
            const formattedDate = formattedDateCell ? formattedDateCell.textContent.toLowerCase() : '';
            const kapalCells = tbody.querySelectorAll('td:nth-child(4)');
            let kapalMatch = false;
            kapalCells.forEach(cell => {
                if (cell.textContent.toLowerCase().includes(searchTerm)) {
                    kapalMatch = true;
                }
            });

            if (date.includes(searchTerm) || formattedDate.includes(searchTerm) || kapalMatch) {
                tbody.style.display = '';
                visibleCount++;
            } else {
                tbody.style.display = 'none';
            }
        });

        if (allTbody.length > 0) {
            noResultsRow.style.display = visibleCount === 0 ? '' : 'none';
        }
    });
});
</script>
@endpush
