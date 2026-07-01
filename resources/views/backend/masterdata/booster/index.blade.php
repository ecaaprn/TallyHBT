@extends('layouts.admin')

@section('title', 'Data Booster')

@section('content')

<div class="card shadow rounded">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center" style="border-bottom: none !important;">
        <h4 class="mb-2 mb-md-0 fw-bold text-uppercase" style="color: #000000;">DATA BOOSTER</h4>
        <div class="d-flex align-items-center gap-2" style="justify-content: flex-end; flex-wrap: nowrap;">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari booster" style="border-radius: 8px; width: 300px; min-width: 180px;">
            <button id="showCreateForm" class="btn" style="border-radius: 8px; background-color: #0158a4; color: #fff; border: none; font-weight: 600; transition: all .2s ease;" onmouseover="this.style.backgroundColor='#003366'; this.style.transform='scale(1.03)'" onmouseout="this.style.backgroundColor='#0158a4'; this.style.transform='scale(1)'" onmousedown="this.style.transform='scale(0.97)'" onmouseup="this.style.transform='scale(1.03)'">
                <i class="fa-solid fa-square-plus me-2"></i>Tambah
            </button>
        </div>
    </div>

    <div class="card-body">
        <div id="formContainerWrapper">
            @include('backend.masterdata.booster.create')
            @include('backend.masterdata.booster.edit')
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-hover" style="min-width: 750px; text-align: center; font-size: 0.95rem;">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center; background-color: #0158a4; color: #fff; padding: 12px 5px;">No</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Nama Booster</th>
                        <th style="width: 160px; background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Dibuat</th>
                        <th style="width: 140px; background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Diperbarui</th>
                        <th style="width: 100px; background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="boosterTableBody">
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="paginationContainer" class="d-flex justify-content-center mt-4"></div>
    </div>
</div>

<style>
.editBoosterBtn {
    background-color: #ffc107;
    border: none;
    border-radius: 6px;
    padding: 5px 7px;
    transition: background-color 0.2s;
}
.editBoosterBtn:hover {
    background-color: #e0a800;
}
.editBoosterBtn i {
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
const baseUrl = "{{ url('admin/masterdata/booster') }}";
let searchTimer = null;
let currentSearch = '';

function formatDateTime(v){
    if(!v) return '-';
    const dateString = typeof v === 'string' ? v.replace(" ", "T") : v;
    const d = new Date(dateString);
    if(isNaN(d.getTime())) return '-';
    const day = String(d.getDate()).padStart(2,'0');
    const month = String(d.getMonth()+1).padStart(2,'0');
    const year = d.getFullYear();
    const hour = String(d.getHours()).padStart(2,'0');
    const min = String(d.getMinutes()).padStart(2,'0');
    const sec = String(d.getSeconds()).padStart(2,'0');
    return `${day}-${month}-${year} ${hour}.${min}.${sec}`;
}

function renderPaginationNumeric(links){
    const container = $('#paginationContainer');
    container.empty();
    const filteredLinks = links.filter(x => /^\d+$/.test(x.label) || x.active || x.label.includes('...'));

    if(filteredLinks.length <= 3 && filteredLinks.every(l => !l.url)) return;

    const ul = $('<ul class="pagination mb-0"></ul>');
    filteredLinks.forEach(l => {
        const li = $('<li class="page-item"></li>');
        const btn = $(`<button class="page-link" type="button">${l.label}</button>`);

        if(l.active){
            li.addClass('active');
            btn.prop('disabled', true);
        } else if(l.url){
            const page = new URL(l.url).searchParams.get('page');
            btn.attr('data-page', page);
            btn.click(() => fetchBoosterData(currentSearch, page));
        } else {
            li.addClass('disabled');
            btn.prop('disabled', true);
        }
        li.append(btn);
        ul.append(li);
    });
    container.append(ul);
}

function fetchBoosterData(q='', page=1){
    currentSearch = q;

    $.get(`${baseUrl}?search_booster=${encodeURIComponent(q)}&page=${page}&ajax=1`, function(res){
        const p = res.boosters;
        const data = p.data;
        const tbody = $('#boosterTableBody');
        tbody.empty();

        if(!data.length){
            tbody.append(`<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada data ditemukan</td></tr>`);
            renderPaginationNumeric([]);
            return;
        }

        let no = p.from;

        data.forEach((b,i)=>{
            tbody.append(`
                <tr>
                    <td style="white-space: nowrap; width: 50px;">${no+i}</td>
                    <td style="white-space: nowrap;">${b.nama}</td>
                    <td style="white-space: nowrap;">${formatDateTime(b.created_at)}</td>
                    <td style="white-space: nowrap;">${formatDateTime(b.updated_at)}</td>
                    <td style="white-space: nowrap;">
                        <button class="btn btn-sm editBoosterBtn"
                                data-id="${b.id}"
                                data-nama="${b.nama}">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                    </td>
                </tr>
            `);
        });

        renderPaginationNumeric(p.links);
    }).fail(()=>{
        $('#boosterTableBody').html(`<tr><td colspan="5" class="text-center text-danger py-3">Gagal memuat data.</td></tr>`);
    });
}

$('#showCreateForm').click(function(){
    $('#formEditContainer').stop(true, true).slideUp(300);
    $('#formCreateContainer').stop(true, true).slideDown(300);
});

$(document).on('click', '.editBoosterBtn', function() {
    $('#formCreateContainer').stop(true, true).slideUp(300);

    const boosterName = $(this).data('nama');
    $('#edit_namaBooster').val(boosterName);

    $('#formEditContainer').stop(true, true).slideDown(300, function() {
        $('html,body').animate({scrollTop: $('#formEditContainer').offset().top - 100}, 400);
    });
});

$('#searchInput').on('input',function(){
    clearTimeout(searchTimer);
    searchTimer = setTimeout(()=>fetchBoosterData($(this).val()),400);
});

$(document).ready(function(){
    fetchBoosterData();
});
</script>

@endsection
