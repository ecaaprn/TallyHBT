@extends('layouts.admin')

@section('title', 'Data Kapal')

@section('content')
<div class="card shadow rounded">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center" style="border-bottom: none;">
        <h4 class="fw-bold text-uppercase mb-2 mb-md-0" style="color: #000000;">DATA KAPAL</h4>
        <div class="d-flex align-items-center gap-2" style="justify-content: flex-end; flex-wrap: nowrap;">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari kapal" style="border-radius: 8px; width: 300px; min-width: 180px;">
            <button id="toggleCreateBtn" class="btn"
                style="border-radius: 8px; background-color: #0158a4; color: #fff; border: none; font-weight: 600; transition: all .2s ease;"
                onmouseover="this.style.backgroundColor='#003366'; this.style.transform='scale(1.03)'"
                onmouseout="this.style.backgroundColor='#0158a4'; this.style.transform='scale(1)'"
                onmousedown="this.style.transform='scale(0.97)'" onmouseup="this.style.transform='scale(1.03)'">
                <i class="fa-solid fa-square-plus me-2"></i>Tambah
            </button>
        </div>
    </div>

    <div class="card-body">
        <div id="formContainerWrapper">
            @include('backend.masterdata.kapal.create')
            @include('backend.masterdata.kapal.edit')
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-hover table-nowrap text-center" style="min-width: 1000px; font-size: 0.95rem;">
                <thead>
                    <tr>
                        <th style="width: 60px; background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">No</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Nama Kapal</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Tanggal</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Status</th>
                        <th style="width: 160px; background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Dibuat</th>
                        <th style="width: 140px; background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Diperbarui</th>
                        <th style="width: 100px; background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="kapalTableBody">
                    <tr>
                        <td colspan="7" class="py-3 text-muted">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="paginationContainer" class="d-flex justify-content-center mt-4"></div>
    </div>
</div>

<style>
    .header-no-border { border-bottom: none; }
    .text-black { color: #000000; }
    .btn-wrapper { justify-content: flex-end; flex-wrap: nowrap; }
    
    .search-box { 
        border-radius: 8px; 
        width: 300px; 
        min-width: 180px; 
    }

    .btn-primary-custom {
        border-radius: 8px;
        background-color: #0158a4;
        color: #fff;
        border: none;
        font-weight: 600;
        transition: all .2s ease;
    }

    .btn-primary-custom:hover {
        background-color: #003366;
        transform: scale(1.03);
        color: #fff;
    }

    .btn-primary-custom:active {
        transform: scale(0.97);
    }

    .table-custom {
        min-width: 1000px;
        font-size: 0.95rem;
    }

    .table-custom thead th {
        background-color: #0158a4 !important;
        color: #fff !important;
        text-align: center;
        white-space: nowrap;
        padding: 12px 8px;
        border: none;
    }

    .col-no { width: 60px; }
    .col-created { width: 160px; }
    .col-updated { width: 140px; }
    .col-action { width: 100px; }

    .status-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 600;
        font-size: .8rem;
        color: #fff;
        white-space: nowrap;
    }

    .status-active { background: #198754; }
    .status-inactive { background: #dc3545; }

    .pagination { border-radius: 8px; }

    .page-item .page-link {
        border: none !important;
        padding: .5rem .75rem;
        margin: 0 5px;
        color: #495057;
        background: transparent !important;
        font-weight: 600;
        border-radius: 8px !important;
    }

    .page-item .page-link:hover {
        color: #003366;
        background: #e9ecef !important;
    }

    .page-item.active .page-link {
        background: #0158a4 !important;
        color: white !important;
    }
</style>

<script>
    const baseUrl = "{{ url('admin/masterdata/kapal') }}";
    let editingId = null, searchTimer = null, currentSearch = '';

    function formatDateTime(x) {
        if (!x) return '-';
        const d = new Date(typeof x === 'string' ? x.replace(" ", "T") : x);
        if (isNaN(d.getTime())) return '-';
        const pad = n => String(n).padStart(2, '0');
        return `${pad(d.getDate())}-${pad(d.getMonth() + 1)}-${d.getFullYear()} ${pad(d.getHours())}.${pad(d.getMinutes())}.${pad(d.getSeconds())}`;
    }

    function paginate(links) {
        const c = $('#paginationContainer').empty();
        const filtered = links.filter(x => /^\d+$/.test(x.label) || x.active || x.label.includes('...'));
        if (filtered.length <= 3 && filtered.every(l => !l.url)) return;
        const ul = $('<ul class="pagination mb-0"></ul>');
        filtered.forEach(l => {
            const li = $('<li class="page-item"></li>');
            const b = $(`<button class="page-link" type="button">${l.label}</button>`);
            if (l.active) li.addClass('active');
            else if (l.url) {
                const p = new URL(l.url).searchParams.get('page');
                b.click(() => loadKapal(currentSearch, p));
            } else li.addClass('disabled');
            ul.append(li.append(b));
        });
        c.append(ul);
    }

    function loadKapal(q = '', page = 1) {
        currentSearch = q;
        $.get(`${baseUrl}?search_kapal=${encodeURIComponent(q)}&page=${page}&ajax=1`, res => {
            const p = res.kapals, d = p.data, tb = $('#kapalTableBody').empty();
            if (!d.length) {
                tb.append('<tr><td colspan="7" class="py-3 text-muted">Tidak ada data</td></tr>');
                paginate([]); return;
            }
            let startIdx = p.from;
            d.forEach((k, idx) => {
                const s = k.status === 'aktif' || k.status === 'active';
                tb.append(`
                    <tr>
                        <td>${startIdx + idx}</td>
                        <td>${k.nama}</td>
                        <td>${k.tanggal ?? '-'}</td>
                        <td><span class="status-badge ${s ? 'status-active' : 'status-inactive'}">${s ? 'Aktif' : 'NonAktif'}</span></td>
                        <td style="white-space: nowrap;">${formatDateTime(k.created_at)}</td>
                        <td style="white-space: nowrap;">${formatDateTime(k.updated_at)}</td>
                        <td>
                            <button class="btn btn-sm btn-warning editKapalBtn" data-id="${k.id}" data-nama="${k.nama}" data-tanggal="${k.tanggal ?? ''}" data-status="${k.status}">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>`);
            });
            paginate(p.links);
        }).fail(() => $('#kapalTableBody').html('<tr><td colspan="7" class="text-danger">Gagal memuat data</td></tr>'));
    }

    $('#toggleCreateBtn').click(() => {
        $('#formEditContainer').stop(true, true).slideUp(300);
        $('#formCreateContainer').stop(true, true).slideDown(300);
        $('#createKapalForm')[0].reset();
    });

    $(document).on('click', '.editKapalBtn', function() {
        $('#formCreateContainer').stop(true, true).slideUp(300);
        editingId = $(this).data('id');
        $('#formEditContainer').stop(true, true).slideDown(300, () => {
            $('html,body').animate({ scrollTop: $('#formEditContainer').offset().top - 100 }, 400);
        });
        $('#edit_namaKapal').val($(this).data('nama'));
        $('#edit_tanggalKapal').val($(this).data('tanggal'));
        let s = $(this).data('status');
        $('#edit_statusKapal').val(s === 'active' ? 'aktif' : (s === 'inactive' ? 'nonaktif' : s));
    });

    $('#searchInput').on('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => loadKapal($(this).val()), 400);
    });

    $(document).ready(() => loadKapal());
</script>
@endsection