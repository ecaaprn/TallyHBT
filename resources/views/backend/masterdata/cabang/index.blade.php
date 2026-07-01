@extends('layouts.admin')

@section('title','Data Cabang')

@section('content')

<div class="card shadow rounded">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center" style="border-bottom: none !important;">
        <h4 class="mb-2 mb-md-0 fw-bold text-uppercase" style="color: #000000;">DATA CABANG</h4>
        <div class="d-flex align-items-center gap-2" style="justify-content: flex-end; flex-wrap: nowrap;">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari cabang" style="border-radius: 8px; width: 300px; min-width: 180px;">
            <button id="showCreateFormBtn" class="btn" style="border-radius: 8px; background-color: #0158a4; color: #fff; border: none; font-weight: 600; transition: all .2s ease;" onmouseover="this.style.backgroundColor='#003366'; this.style.transform='scale(1.03)'" onmouseout="this.style.backgroundColor='#0158a4'; this.style.transform='scale(1)'" onmousedown="this.style.transform='scale(0.97)'" onmouseup="this.style.transform='scale(1.03)'"><i class="fa-solid fa-square-plus me-2"></i>Tambah</button>
        </div>
    </div>

    <div class="card-body">
        <div id="formContainerWrapper">
            @include('backend.masterdata.cabang.create')
            @include('backend.masterdata.cabang.edit')
        </div>
        <div class="table-responsive">
            <table class="table align-middle text-center table-hover table-nowrap" style="min-width: 700px; font-size: 0.95rem;">
                <thead>
                    <tr>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">No</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Cabang</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Dibuat</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px; width: 140px;">Diperbarui</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="cabangTableBody">
                    <tr><td colspan="5" class="text-center text-muted py-3">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>

        <div id="paginationContainer" class="d-flex justify-content-center mt-4"></div>
    </div>
</div>

<style>
.editCabangBtn {
    background: #ffc107;
    border: none;
    border-radius: 6px;
    padding: 5px 7px;
    transition: background-color 0.2s;
}
.editCabangBtn:hover {
    background: #e0a800;
}
.editCabangBtn i {
    color: #000;
    font-size: 1rem;
}
.pagination {
    border-radius: 8px;
}
.page-item .page-link {
    border: none;
    padding: .5rem .75rem;
    margin: 0 5px;
    color: #495057;
    background: transparent;
    font-weight: 600;
    border-radius: 8px;
}
.page-item .page-link:hover {
    color: #003366;
    background: #e9ecef;
}
.page-item.active .page-link {
    background: #0158a4;
    color: white;
    border-radius: 8px;
    pointer-events: none;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const baseUrl = "{{ url('admin/masterdata/cabang') }}";
let searchTimer = null, currentSearch = '';

function formatDateTime(x) {
    if (!x) return '-';
    const d = new Date(x.replace(" ", "T"));
    if (isNaN(d.getTime())) return '-';
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    const seconds = String(d.getSeconds()).padStart(2, '0');
    return `${day}-${month}-${year} ${hours}.${minutes}.${seconds}`;
}

function renderPaginationNumeric(links) {
    const c = $('#paginationContainer');
    c.empty();
    const filteredLinks = links.filter(l => /^\d+$/.test(l.label) || l.active || l.label.includes('...'));

    if (filteredLinks.length <= 3 && filteredLinks.every(l => !l.url)) return;

    const ul = $('<ul class="pagination mb-0"></ul>');
    filteredLinks.forEach(l => {
        const li = $('<li class="page-item"></li>');
        const btn = $(`<button class="page-link" type="button">${l.label}</button>`);
        if (l.active) li.addClass('active');
        else if (l.url) {
            const page = new URL(l.url).searchParams.get('page');
            btn.click(() => fetchCabangData(currentSearch, page));
        } else li.addClass('disabled');
        li.append(btn);
        ul.append(li);
    });
    c.append(ul);
}

function fetchCabangData(q='', page=1) {
    currentSearch = q;

    $.get(`${baseUrl}?search_cabang=${encodeURIComponent(q)}&page=${page}&ajax=1`, function(res) {
        const p = res.cabangs;
        const data = p.data;
        const tbody = $('#cabangTableBody');
        tbody.empty();

        if (!data.length) {
            tbody.append(`
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">Tidak ada data ditemukan</td>
                </tr>
            `);
            renderPaginationNumeric([]);
            return;
        }

        let no = p.from;

        data.forEach((c, i) => {
            tbody.append(`
                <tr>
                    <td style="white-space: nowrap;">${no + i}</td>
                    <td style="white-space: nowrap;">${c.nama}</td>
                    <td style="white-space: nowrap;">${formatDateTime(c.created_at)}</td>
                    <td style="white-space: nowrap;">${formatDateTime(c.updated_at)}</td>
                    <td style="white-space: nowrap;">
                        <button class="btn btn-sm editCabangBtn"
                            data-id="${c.id}"
                            data-nama="${c.nama}">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                    </td>
                </tr>
            `);
        });

        renderPaginationNumeric(p.links);
    })
    .fail(() => {
        $('#cabangTableBody').html(`
            <tr>
                <td colspan="5" class="text-center text-danger py-3">Gagal memuat data.</td>
            </tr>
        `);
    });
}

$(document).on('click', '.editCabangBtn', function() {
    $('#formCreateContainer').stop(true, true).slideUp(300);

    const cabangName = $(this).data('nama');
    $('#edit_namaCabang').val(cabangName);

    $('#formEditContainer').stop(true, true).slideDown(300, function() {
        $('html,body').animate({scrollTop: $('#formEditContainer').offset().top - 100}, 400);
    });
});

$('#showCreateFormBtn').click(function() {
    $('#formEditContainer').stop(true, true).slideUp(300);
    $('#formCreateContainer').stop(true, true).slideDown(300);
});

$('#searchInput').on('input',function(){
    clearTimeout(searchTimer);
    searchTimer=setTimeout(()=>fetchCabangData($(this).val()),400);
});

$(document).ready(function() {
    fetchCabangData();
});
</script>

@endsection
