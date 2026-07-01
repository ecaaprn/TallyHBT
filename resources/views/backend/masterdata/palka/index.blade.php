@extends('layouts.admin')

@section('title', 'Data Palka')

@section('content')

<div class="card shadow rounded">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center" style="border-bottom: none;">
        <h4 class="mb-2 mb-md-0 fw-bold text-uppercase" style="color: #000000;">DATA PALKA</h4>
        <div class="d-flex align-items-center gap-2" style="justify-content: flex-end; flex-wrap: nowrap;">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari palka" style="border-radius: 8px; width: 300px; min-width: 180px;">
            <button id="toggleCreateBtn" class="btn" style="background-color: #0158a4; color: #fff; border: none; font-weight: 600; transition: .2s; border-radius: 8px;" onmouseover="this.style.backgroundColor='#003366'; this.style.transform='scale(1.03)'" onmouseout="this.style.backgroundColor='#0158a4'; this.style.transform='scale(1)'" onmousedown="this.style.transform='scale(0.97)'" onmouseup="this.style.transform='scale(1.03)'">
                <i class="fa-solid fa-square-plus me-2"></i>Tambah
            </button>
        </div>
    </div>

    <div class="card-body">
        <div id="formContainerWrapper">
            @include('backend.masterdata.palka.create')
            @include('backend.masterdata.palka.edit')
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-hover" style="min-width: 750px; text-align: center; font-size: 0.95rem;">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center; background-color: #0158a4; color: #fff;">No</th>
                        <th style="text-align: center; background-color: #0158a4; color: #fff;">Nama Palka</th>
                        <th style="width: 160px; text-align: center; background-color: #0158a4; color: #fff;">Dibuat</th>
                        <th style="width: 140px; text-align: center; background-color: #0158a4; color: #fff;">Diperbarui</th>
                        <th style="width: 100px; text-align: center; background-color: #0158a4; color: #fff;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="palkaTableBody">
                    <tr><td colspan="5" class="text-center text-muted py-3">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="paginationContainer" class="d-flex justify-content-center mt-4"></div>
    </div>
</div>

<style>
.pagination {
    border-radius: 8px;
}
.page-item .page-link {
    border: none !important;
    padding: .5rem .75rem;
    margin: 0 5px;
    color: #495057;
    background: transparent !important;
    font-weight: 600;
    border-radius: 8px !important;
    transition: .15s;
}
.page-item .page-link:hover {
    color: #003366;
    background: #e9ecef !important;
}
.page-item.active .page-link {
    background: #0158a4 !important;
    color: white !important;
    pointer-events: none;
}
</style>

<script>
const baseUrl="{{ url('admin/masterdata/palka') }}";
let editingId=null;
let searchTimer=null;
let currentSearch='';

function formatDateTime(x){
    if(!x) return '-';
    const d=new Date(x.replace(" ","T"));
    if(isNaN(d.getTime())) return '-';
    const day=String(d.getDate()).padStart(2,'0');
    const month=String(d.getMonth()+1).padStart(2,'0');
    const year=d.getFullYear();
    const hours=String(d.getHours()).padStart(2,'0');
    const minutes=String(d.getMinutes()).padStart(2,'0');
    const seconds=String(d.getSeconds()).padStart(2,'0');
    return `${day}-${month}-${year} ${hours}.${minutes}.${seconds}`;
}

function renderPaginationNumeric(links){
    const c=$('#paginationContainer');
    c.empty();
    const filteredLinks = links.filter(x => /^\d+$/.test(x.label) || x.active || x.label.includes('...'));

    if(filteredLinks.length <= 3 && filteredLinks.every(l => !l.url)) return;

    const ul=$('<ul class="pagination mb-0"></ul>');
    filteredLinks.forEach(l=>{
        const li=$('<li class="page-item"></li>');
        const btn=$(`<button class="page-link" type="button">${l.label}</button>`);
        if(l.active) li.addClass('active');
        else if(l.url){
            const p=new URL(l.url).searchParams.get('page');
            btn.attr('data-page',p);
            btn.click(()=>fetchPalka(currentSearch,p));
        } else li.addClass('disabled');
        li.append(btn);
        ul.append(li);
    });
    c.append(ul);
}

function fetchPalka(q='',page=1){
    currentSearch=q;
    $.get(`${baseUrl}?search_palka=${encodeURIComponent(q)}&page=${page}&ajax=1`,res=>{
        const p=res.palkas;
        const data=p.data;
        const tb=$('#palkaTableBody');
        tb.empty();
        if(!data.length){
            tb.append('<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada data ditemukan</td></tr>');
            renderPaginationNumeric([]);
            return;
        }
        let no=p.from;
        data.forEach((x,i)=>{
            tb.append(`
                <tr>
                    <td style="white-space: nowrap; text-align: center;">${no+i}</td>
                    <td style="white-space: nowrap; text-align: center;">${x.nama}</td>
                    <td style="white-space: nowrap; text-align: center;">${formatDateTime(x.created_at)}</td>
                    <td style="white-space: nowrap; text-align: center;">${formatDateTime(x.updated_at)}</td>
                    <td style="white-space: nowrap; text-align: center;">
                        <button class="btn btn-sm btn-warning editPalkaBtn"
                            data-id="${x.id}"
                            data-nama="${x.nama}">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
        renderPaginationNumeric(p.links);
    }).fail(()=>{
        $('#palkaTableBody').html('<tr><td colspan="5" class="text-center text-danger py-3">Gagal memuat data</td></tr>');
        renderPaginationNumeric([]);
    });
}

$('#toggleCreateBtn').click(()=>{
    $('#formEditContainer').stop(true, true).slideUp(300);
    $('#formCreateContainer').stop(true, true).slideDown(300);
    $('#createPalkaForm')[0].reset();
});

$(document).on('click','.editPalkaBtn',function(){
    $('#formCreateContainer').stop(true, true).slideUp(300);

    editingId=$(this).data('id');
    $('#formEditContainer').stop(true, true).slideDown(300, function() {
        $('html,body').animate({scrollTop:$('#formEditContainer').offset().top-100},400);
    });

    $('#edit_namaPalka').val($(this).data('nama'));
});

$('#searchInput').on('input',function(){
    clearTimeout(searchTimer);
    searchTimer=setTimeout(()=>fetchPalka($(this).val()),400);
});

$(document).ready(()=>fetchPalka());
</script>

@endsection
