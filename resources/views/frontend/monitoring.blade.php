@extends('layouts.app')

@section('title', 'Data Monitoring')

@section('content')
<style>
    table { border-collapse: collapse; }
    thead th {
        border-bottom: 2px solid #dee2e6;
        background-color: #1C6EA4;
        color: white;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    tbody td { border-bottom: 1px solid #dee2e6; }
    tbody:last-of-type td { border-bottom: none; }
    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }
    .pagination-container p.small.text-muted { display: none !important; }
    @media (min-width: 769px) {
        .pagination-container nav > div:first-child { display: none !important; }
    }
    .pagination-container .page-link {
        border: none !important;
        background-color: transparent !important;
        color: #6c757d;
        box-shadow: none !important;
    }
    .pagination-container .page-item.active .page-link {
        background-color: #1C6EA4 !important;
        border-radius: 0.375rem !important;
        color: white !important;
    }
    .pagination-container .page-link:hover {
        color: #1C6EA4;
        box-shadow: none !important;
    }
    .table-card-wrapper { min-width: 0; }
    #monitoring-table tbody tr:nth-of-type(odd) { background-color: #FFF9AF; }
    #monitoring-table tbody tr:nth-of-type(even) { background-color: #FFFFFF; }
    #monitoring-table tbody tr:hover { background-color: #33A1E0; color: white; }
    .table .btn-primary {
        background-color: #33A1E0;
        border-color: #33A1E0;
        color: white;
    }
    .table .btn-primary:hover, .table .btn-primary:focus {
        background-color: #1C6EA4;
        border-color: #1C6EA4;
    }
    .table .btn-success {
        background-color: #154D71;
        border-color: #154D71;
        color: white;
    }
    .table .btn-success:hover, .table .btn-success:focus {
        background-color: #1C6EA4;
        border-color: #1C6EA4;
    }
    .btn-dark-download {
        background-color: #154D71;
        border-color: #154D71;
        color: white;
    }
    .btn-dark-download:hover, .btn-dark-download:focus {
        background-color: #1C6EA4;
        border-color: #1C6EA4;
    }
    .btn-danger {
        background-color: #DC3545;
        border-color: #DC3545;
        color: white;
    }
    .btn-danger:hover, .btn-danger:focus {
        background-color: #C82333;
        border-color: #C82333;
    }
    h2.fs-3.fw-bold {
        color: white;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.25);
    }
    .btn-back-custom {
        color: white !important;
        text-decoration: none;
    }
    .btn-back-custom:hover {
        color: #dddddd !important;
    }
    @media (max-width: 768px) {
        h2.fs-1 { font-size: 1.4rem !important; }
        #monitoring-table th, #monitoring-table td { font-size: 0.8rem; }
        .pagination-container nav {
            display: flex !important;
            justify-content: center !important;
            flex-wrap: wrap;
        }
    }
</style>

<div class="container">
    <div class="d-flex flex-column gap-2">

        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('menu') }}" class="btn-back-custom fs-5 p-0" title="Kembali">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="fs-3 fw-bold mb-0 text-center flex-grow-1">DATA MONITORING</h2>
            <a class="btn btn-danger btn-sm shadow-sm" title="Keluar" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>

        <div class="d-flex justify-content-between align-items-center" style="gap: 1rem;">
            <div class="input-group input-group-sm" style="max-width: 300px;">
                <input type="text" id="date-search-input" class="form-control form-control-sm" placeholder="Cari tanggal / kapal">
            </div>
            <div class="dropdown">
                <button class="btn btn-dark-download btn-sm shadow-sm dropdown-toggle text-nowrap" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-download me-1"></i>Download semua
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('export.all.shift') }}">Download semua (Shift)</a></li>
                    <li><a class="dropdown-item" href="{{ route('export.all.kapal') }}">Download semua (Kapal)</a></li>
                </ul>
            </div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="p-2 p-md-3 bg-white rounded-3 shadow-sm table-card-wrapper mt-2">
            <div class="table-responsive-md">
                <table id="monitoring-table" class="table mb-0">
                    <thead>
                        <tr>
                            <th class="text-center py-2">No</th>
                            <th class="text-center py-2">Tanggal</th>
                            <th class="text-center py-2">Shift</th>
                            <th class="text-center py-2">Kapal</th>
                            <th class="text-center py-2">Aksi</th>
                        </tr>
                    </thead>
                    @php
                        $iteration = ($monitoringGroups->currentPage() - 1) * $monitoringGroups->perPage();
                    @endphp
                    @forelse ($monitoringGroups->groupBy('Tanggal') as $tanggal => $groupsByDate)
                        <tbody data-tanggal-search="{{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y') }}" data-kapal-search="{{ $groupsByDate->pluck('Kapal')->unique()->implode(' ') }}">
                            @php
                                $groupedByShift = $groupsByDate->groupBy('NoShift');
                                $isFirstDateRow = true;
                                $iteration++;
                            @endphp
                            @foreach ($groupedByShift as $shiftNumber => $groupsByShift)
                                @php $isFirstShiftRow = true; @endphp
                                @foreach ($groupsByShift as $group)
                                    <tr>
                                        @if ($isFirstDateRow)
                                            <td class="text-center align-middle" rowspan="{{ $groupsByDate->count() }}">{{ $iteration }}</td>
                                            <td class="text-center align-middle text-wrap" rowspan="{{ $groupsByDate->count() }}">
                                                {{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                                            </td>
                                        @endif
                                        @if ($isFirstShiftRow)
                                            <td class="text-center align-middle" rowspan="{{ $groupsByShift->count() }}">{{ $shiftNumber }}</td>
                                        @endif
                                        <td class="text-center align-middle">{{ $group->Kapal }}</td>
                                        <td class="text-center align-middle">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('job-order.input', ['date' => $group->Tanggal, 'shift' => $group->NoShift, 'kapal' => $group->Kapal]) }}" class="btn btn-sm btn-primary btn-icon" title="Ubah Data">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-success btn-icon dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Unduh Laporan">
                                                        <i class="fa-solid fa-download"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="{{ route('export.shift', ['date' => $group->Tanggal, 'shift' => $group->NoShift, 'kapal' => $group->Kapal]) }}">Download Shift</a></li>
                                                        <li><a class="dropdown-item" href="{{ route('export.kapal', ['date' => $group->Tanggal, 'kapal' => $group->Kapal]) }}">Download Kapal</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @php
                                        $isFirstDateRow = false;
                                        $isFirstShiftRow = false;
                                    @endphp
                                @endforeach
                            @endforeach
                        </tbody>
                    @empty
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center text-muted p-4">Tidak ada data.</td>
                            </tr>
                        </tbody>
                    @endforelse
                </table>
            </div>
            @if ($monitoringGroups->hasPages())
                <div class="d-flex justify-content-center mt-3 pagination-container">
                    {{ $monitoringGroups->onEachSide(1)->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('date-search-input');
    const table = document.getElementById('monitoring-table');
    const allTbody = table.querySelectorAll('tbody[data-tanggal-search]');
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;
        allTbody.forEach(tbody => {
            const tanggal = tbody.dataset.tanggalSearch.toLowerCase();
            const kapal = tbody.dataset.kapalSearch.toLowerCase();
            if (tanggal.includes(searchTerm) || kapal.includes(searchTerm)) {
                tbody.style.display = '';
                visibleCount++;
            } else {
                tbody.style.display = 'none';
            }
        });
        let noResultsBody = table.querySelector('#no-results-row');
        if (!noResultsBody) {
            noResultsBody = document.createElement('tbody');
            noResultsBody.id = 'no-results-row';
            noResultsBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted p-4">Data tidak ditemukan.</td></tr>';
            table.appendChild(noResultsBody);
        }
        const emptyDataRow = Array.from(table.querySelectorAll('tbody tr td[colspan="5"]')).find(td => td.textContent.includes('Tidak ada data'));
        if (emptyDataRow) {
            emptyDataRow.closest('tbody').style.display = 'none';
        }
        noResultsBody.style.display = (visibleCount === 0 && !emptyDataRow) ? '' : 'none';
    }
    searchInput.addEventListener('input', filterTable);
});
</script>
@endpush
