@extends('layouts.admin')

@section('title', 'Monitoring Data')

@section('content')
<div class="card shadow rounded">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center" style="border-bottom: none;">
        <h4 class="mb-2 mb-md-0 fw-bold text-uppercase" style="color: #000;">MONITORING DATA</h4>
        <div class="d-flex align-items-center gap-2">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari Job Order, Shift" style="border-radius: 8px; width: 300px;">
            <button id="downloadBtn" class="btn btn-primary-custom">
                <i class="fa-solid fa-download me-1"></i>Download
            </button>
        </div>
    </div>
    <div class="card-body">
        <div id="formContainerWrapper">
            @include('backend.monitoring-data.edit')
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-hover text-center" style="min-width: 1300px; font-size: 0.95rem;">
                <thead>
                    <tr>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">No</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Tanggal</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Shift</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Kapal</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">No Job Order</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">No Truck</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Petugas Job Order</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Petugas Timelist</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Waktu Tiba</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Plugging</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Open Valve</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Close Valve</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Unplugging</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Kategori</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Status</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Catatan</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Dibuat</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px; width: 140px;">Diperbarui</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr>
                        <td colspan="19" class="text-center text-muted py-3">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="paginationContainer" class="d-flex justify-content-center mt-4"></div>
    </div>
</div>

<style>
    .btn-primary-custom {
        border-radius: 8px;
        background-color: #0158a4;
        color: #fff;
        font-weight: 600;
        transition: all .2s ease;
        border: none;
    }
    .btn-primary-custom:hover {
        background-color: #003366;
        transform: scale(1.03);
        color: #fff;
    }
    .btn-primary-custom:active {
        transform: scale(0.97);
    }
    .uniform-field {
        height: 42px;
        border-radius: 8px;
        font-size: 15px;
        border: 1px solid #ced4da;
        padding: 0 12px;
    }
    .uniform-field[readonly] {
        background-color: #f8f9fa;
    }
    .editMonitoringBtn {
        background-color: #ffc107;
        border: none;
        border-radius: 6px;
        padding: 5px 7px;
        transition: background-color 0.2s;
    }
    .editMonitoringBtn:hover {
        background-color: #e0a800;
    }
    .editMonitoringBtn i {
        color: #000;
        font-size: 1rem;
    }
    .page-item .page-link {
        border: none !important;
        padding: 0.5rem 0.75rem;
        margin: 0 5px;
        color: #495057;
        background-color: transparent !important;
        font-weight: 600;
        border-radius: 8px !important;
        transition: background-color 0.15s ease-in-out;
    }
    .page-item .page-link:hover {
        color: #003366;
        background-color: #e9ecef !important;
    }
    .page-item.active .page-link {
        background-color: #0158a4 !important;
        color: #ffffff !important;
        pointer-events: none;
        border-radius: 8px !important;
    }
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border-radius: 8px !important;
        border: 1px solid #ced4da !important;
        display: flex !important;
        align-items: center !important;
        padding: 0 8px !important;
    }
    .select2-selection__rendered {
        font-size: 15px !important;
        color: #212529 !important;
        line-height: 42px !important;
    }
    .select2-selection__arrow {
        height: 42px !important;
        top: 0 !important;
    }
    .swal-date-row {
        text-align: left;
        padding-top: 10px;
        font-size: 15px;
    }
    .swal-label {
        color: #000;
    }
    .swal-date-input {
        height: 40px;
        border-radius: 6px;
        font-size: 15px;
        border: 1px solid #000;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

@push('scripts')
<script>
    $(function () {
        if ($.fn.datepicker) {
            $.fn.datepicker.dates['id'] = {
                days: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
                daysShort: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                daysMin: ["M", "S", "S", "R", "K", "J", "S"],
                months: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
                monthsShort: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"],
                today: "Hari Ini",
                clear: "Kosongkan"
            };
        }
    });

    let editingId = null, searchTimer = null, currentSearch = '';
    const baseUrl = '{{ url("admin/monitoring-data") }}';
    const downloadUrl = '{{ route("export.all.shift", [], false) }}';

    const timeFormat = t => t ? t.toString().substring(0, 5) : '-';

    function formatDateOnly(v) {
        if (!v) return '-';
        const p = v.split(' ')[0].split('-');
        return p.length === 3 ? `${p[2]}-${p[1]}-${p[0]}` : v;
    }

    function formatDateTime(v) {
        if (!v) return '-';
        const d = new Date(v);
        if (isNaN(d.getTime())) return '-';
        const pad = n => String(n).padStart(2, '0');
        return `${pad(d.getDate())}-${pad(d.getMonth() + 1)}-${d.getFullYear()} ${pad(d.getHours())}.${pad(d.getMinutes())}.${pad(d.getSeconds())}`;
    }

    const badgeStatus = s => {
        s = (s || '').toString().toLowerCase();
        if (['active', 'aktif'].includes(s)) return '<span class="badge bg-success">Aktif</span>';
        if (['inactive', 'nonaktif', 'non-aktif'].includes(s)) return '<span class="badge bg-danger">NonAktif</span>';
        if (['cancel', 'batal'].includes(s)) return '<span class="badge bg-dark">Batal</span>';
        return '<span class="badge bg-secondary">-</span>';
    };

    const badgeKategori = k => {
        k = (k || '').toString().toLowerCase();
        if (k === 'booster') return '<span class="badge bg-primary">Booster</span>';
        if (k === 'non-booster' || k === 'nonbooster') return '<span class="badge bg-secondary">Non-Booster</span>';
        return '<span class="badge bg-secondary">-</span>';
    };

    function renderPaginationNumeric(links) {
        const c = $('#paginationContainer').empty();
        if (links.length <= 3 && links.every(l => l.url === null || l.active)) return;
        const ul = $('<ul class="pagination mb-0"></ul>');
        links.filter(l => /^\d+$/.test(l.label) || l.label.includes('...') || l.active).forEach(l => {
            const li = $('<li class="page-item"></li>');
            const b = $(`<button class="page-link" type="button">${l.label}</button>`);
            if (l.active) li.addClass('active');
            else if (l.url) {
                const pN = new URL(l.url).searchParams.get('page');
                b.click(() => fetchMonitoringData(currentSearch, pN));
            } else li.addClass('disabled');
            ul.append(li.append(b));
        });
        c.append(ul);
    }

    function fetchMonitoringData(q = '', p = 1) {
        currentSearch = q;
        $.get(`${baseUrl}?search=${encodeURIComponent(q)}&page=${p}&ajax=1`, res => {
            const pg = res.data, d = pg.data, tb = $('#tableBody').empty();
            if (!d.length) {
                tb.append('<tr><td colspan="19" class="text-center py-3 text-muted">Tidak ada data ditemukan</td></tr>');
                renderPaginationNumeric([]);
                return;
            }
            let no = pg.from;
            d.forEach(x => {
                tb.append(`
                    <tr>
                        <td class="text-nowrap">${no++}</td>
                        <td class="text-nowrap">${formatDateOnly(x.tanggal)}</td>
                        <td class="text-nowrap">${x.shift ?? '-'}</td>
                        <td class="text-nowrap">${x.kapal_nama ?? '-'}</td>
                        <td class="text-nowrap">${x.no_job_order ?? '-'}</td>
                        <td class="text-nowrap">${x.no_truck ?? '-'}</td>
                        <td class="text-nowrap">${x.petugas_joborder ?? '-'}</td>
                        <td class="text-nowrap">${x.petugas_timelist ?? '-'}</td>
                        <td class="text-nowrap">${timeFormat(x.waktu_tiba)}</td>
                        <td class="text-nowrap">${timeFormat(x.plugging)}</td>
                        <td class="text-nowrap">${timeFormat(x.open_valve)}</td>
                        <td class="text-nowrap">${timeFormat(x.close_valve)}</td>
                        <td class="text-nowrap">${timeFormat(x.unplugging)}</td>
                        <td class="text-nowrap">${badgeKategori(x.kategori)}</td>
                        <td class="text-nowrap">${badgeStatus(x.status)}</td>
                        <td class="text-nowrap">${x.catatan ?? '-'}</td>
                        <td class="text-nowrap">${formatDateTime(x.created_at)}</td>
                        <td class="text-nowrap">${formatDateTime(x.updated_at)}</td>
                        <td class="text-nowrap">
                            <button class="editMonitoringBtn btn btn-sm"
                                data-id="${x.id}" data-tanggal="${x.tanggal ?? ''}" data-shift="${x.shift ?? ''}"
                                data-kapal="${x.kapal_nama ?? ''}" data-no_job_order="${x.no_job_order ?? ''}"
                                data-no_truck="${x.no_truck ?? ''}" data-petugas_joborder="${x.petugas_joborder ?? ''}"
                                data-petugas_timelist="${x.petugas_timelist ?? ''}" data-waktu_tiba="${timeFormat(x.waktu_tiba)}"
                                data-plugging="${timeFormat(x.plugging)}" data-open_valve="${timeFormat(x.open_valve)}"
                                data-close_valve="${timeFormat(x.close_valve)}" data-unplugging="${timeFormat(x.unplugging)}"
                                data-kategori="${x.kategori ?? ''}" data-status="${x.status ?? ''}" data-catatan="${x.catatan ?? ''}">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>`);
            });
            renderPaginationNumeric(pg.links);
        }).fail(() => $('#tableBody').html('<tr><td colspan="19" class="text-center py-3 text-danger">Gagal memuat data.</td></tr>'));
    }

    $('#searchInput').on('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => fetchMonitoringData($(this).val()), 400);
    });

    $('#downloadBtn').click(function () {
        const fmt = d => `${d.getDate().toString().padStart(2, '0')}-${(d.getMonth() + 1).toString().padStart(2, '0')}-${d.getFullYear()}`;
        const today = new Date(), first = new Date(today.getFullYear(), today.getMonth(), 1);
        
        Swal.fire({
            title: 'Pilih Rentang Tanggal',
            background: '#f0f8ff',
            showCloseButton: true,
            confirmButtonText: 'Download',
            confirmButtonColor: '#0158a4',
            html: `
                <div class="row g-3 justify-content-center swal-date-row">
                    <div class="col-6">
                        <label class="form-label fw-medium swal-label">Dari Tanggal</label>
                        <input type="text" id="swal-from-date" class="form-control datepicker-swal swal-date-input" value="${fmt(first)}">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-medium swal-label">Sampai Tanggal</label>
                        <input type="text" id="swal-to-date" class="form-control datepicker-swal swal-date-input" value="${fmt(today)}">
                    </div>
                </div>`,
            preConfirm: () => {
                const f = $('#swal-from-date').val(), t = $('#swal-to-date').val();
                return (!f || !t) ? Swal.showValidationMessage('Mohon isi kedua tanggal.') : { fromDate: f, toDate: t };
            },
            didOpen: () => {
                $('#swal-from-date, #swal-to-date').datepicker({ format: "dd-mm-yyyy", autoclose: true, todayHighlight: true, language: 'id', container: '.swal2-container' });
            }
        }).then((res) => {
            if (res.isConfirmed) {
                window.location.href = `${downloadUrl}?search=${encodeURIComponent(currentSearch)}&from_date=${res.value.fromDate}&to_date=${res.value.toDate}`;
            }
        });
    });

    $(document).ready(() => {
        $('.select2').select2({ width: '100%', minimumResultsForSearch: Infinity });
        fetchMonitoringData();
    });
</script>
@endpush
@endsection