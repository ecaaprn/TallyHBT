@extends('layouts.admin')

@section('title', 'Data Truck')

@section('content')

<div class="card shadow rounded">
    <div class="card-header bg-white d-flex justify-content-between align-items-center" style="border-bottom:none;">
        <h4 class="fw-bold text-uppercase" style="color:#000;">DATA TRUCK</h4>
        <div class="d-flex gap-2">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari no truck" style="width:300px;border-radius:8px;">
            <button id="openCreateForm" class="btn" style="background-color:#0158a4;color:#fff;border:none;font-weight:600;border-radius:8px;">
                <i class="fa-solid fa-square-plus me-2"></i>Tambah
            </button>
        </div>
    </div>

    <div class="card-body">
        <div id="formContainerWrapper">
            @include('backend.masterdata.truck.create')
            @include('backend.masterdata.truck.edit')
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-hover text-center">
                <thead>
                    <tr>
                        <th style="background:#0158a4;color:#fff;">No</th>
                        <th style="background:#0158a4;color:#fff;">No Truck</th>
                        <th style="background:#0158a4;color:#fff;">Dibuat</th>
                        <th style="background:#0158a4;color:#fff;">Diperbarui</th>
                        <th style="background:#0158a4;color:#fff;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="truckTableBody">
                    <tr>
                        <td colspan="5" class="text-muted py-3">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="paginationContainer" class="d-flex justify-content-center mt-4"></div>
    </div>
</div>

<style>
.editTruckBtn{
    background:#ffc107;
    border:none;
    border-radius:6px;
    padding:5px 7px;
}
.editTruckBtn:hover{background:#e0a800}
.pagination{border-radius:8px}
.page-item .page-link{
    border:none!important;
    padding:.5rem .75rem;
    margin:0 5px;
    color:#495057;
    background:transparent!important;
    font-weight:600;
    border-radius:8px!important;
}
.page-item .page-link:hover{
    color:#003366;
    background:#e9ecef!important;
}
.page-item.active .page-link{
    background:#0158a4!important;
    color:white!important;
    pointer-events:none;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const baseUrl = "{{ url('admin/masterdata/truck') }}"
let currentSearch = ''
let searchTimer = null
let editingId = null

function formatDateTime(x){
    if(!x) return '-'
    const d = new Date(x.replace(' ','T'))
    if(isNaN(d.getTime())) return '-'
    const day = String(d.getDate()).padStart(2,'0')
    const month = String(d.getMonth()+1).padStart(2,'0')
    const year = d.getFullYear()
    const h = String(d.getHours()).padStart(2,'0')
    const m = String(d.getMinutes()).padStart(2,'0')
    const s = String(d.getSeconds()).padStart(2,'0')
    return `${day}-${month}-${year} ${h}.${m}.${s}`
}

function renderPaginationNumeric(links){
    const c = $('#paginationContainer')
    c.empty()

    const filtered = links.filter(l => /^\d+$/.test(l.label) || l.active || l.label.includes('...'))
    if(filtered.length <= 3 && filtered.every(l => !l.url)) return

    const ul = $('<ul class="pagination mb-0"></ul>')
    filtered.forEach(l=>{
        const li = $('<li class="page-item"></li>')
        const btn = $(`<button class="page-link" type="button">${l.label}</button>`)

        if(l.active){
            li.addClass('active')
        }else if(l.url){
            const p = new URL(l.url).searchParams.get('page')
            btn.on('click',()=>fetchTruck(currentSearch,p))
        }else{
            li.addClass('disabled')
        }

        li.append(btn)
        ul.append(li)
    })

    c.append(ul)
}

function fetchTruck(q='',page=1){
    currentSearch = q

    $.get(`${baseUrl}?search_truck=${encodeURIComponent(q)}&page=${page}&ajax=1`,res=>{
        const t = res.trucks
        const data = t.data
        const tb = $('#truckTableBody')
        tb.empty()

        if(!data.length){
            tb.append('<tr><td colspan="5" class="text-muted py-3">Tidak ada data ditemukan</td></tr>')
            renderPaginationNumeric([])
            return
        }

        let no = t.from
        data.forEach((x,i)=>{
            tb.append(`
                <tr>
                    <td>${no+i}</td>
                    <td>${x.nama}</td>
                    <td>${formatDateTime(x.created_at)}</td>
                    <td>${formatDateTime(x.updated_at)}</td>
                    <td>
                        <button class="btn btn-sm editTruckBtn"
                            data-id="${x.id}"
                            data-nama="${x.nama}">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                    </td>
                </tr>
            `)
        })

        renderPaginationNumeric(t.links)
    }).fail(()=>{
        $('#truckTableBody').html('<tr><td colspan="5" class="text-danger py-3">Gagal memuat data</td></tr>')
        renderPaginationNumeric([])
    })
}

$('#openCreateForm').on('click',()=>{
    $('#formEditContainer').hide()
    $('#formCreateContainer').slideDown(300)
})

$(document).on('click','.editTruckBtn',function(){
    editingId = $(this).data('id')
    $('#formCreateContainer').hide()
    $('#formEditContainer').slideDown(300)
    $('#edit_nomorPolisi').val($(this).data('nama'))
    $('html,body').animate({scrollTop:$('#formEditContainer').offset().top-100},300)
})

$('#searchInput').on('input',function(){
    clearTimeout(searchTimer)
    searchTimer = setTimeout(()=>fetchTruck($(this).val()),400)
})

$(document).ready(()=>fetchTruck())
</script>

@endsection
