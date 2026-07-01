<div id="formCreateContainer" class="p-3 mb-4 rounded shadow-sm" style="display:none; background-color:#EBF5FF; border:1px solid #B3D9FF;">
    <h5 class="fw-bold mb-3" style="color:#0158a4;">Tambah Data Hose</h5>
    <hr class="my-3">

    <form id="createHoseForm" class="row g-3">
        @csrf

        <div class="col-md-10">
            <label class="form-label fw-bold">Nama Hose</label>
            <input type="text" class="form-control" id="create_namaHose" name="nama" required placeholder="Contoh: Hose-1">
        </div>

        <div class="col-md-2 d-flex align-items-end justify-content-end gap-2">
            <button type="button" id="create_cancelBtn" class="btn btn-secondary w-50">Batal</button>
            <button type="submit" class="btn btn-primary w-50" style="background-color:#0158a4;">Simpan</button>
        </div>
    </form>
</div>

<script>
$('#create_cancelBtn').click(function(){
    $('#createHoseForm')[0].reset();
    $('#formCreateContainer').slideUp();
});

$('#createHoseForm').submit(function(e){
    e.preventDefault();
    const fd={
        nama:$('#create_namaHose').val(),
        _token:'{{ csrf_token() }}'
    };
    $.post(baseUrl,fd,function(){
        fetchHoseData();
        $('#createHoseForm')[0].reset();
        $('#formCreateContainer').slideUp();

        Swal.fire({
            position:"center",
            icon:"success",
            title:`<span style="color:#6c757d;">Data berhasil ditambahkan</span>`,
            showConfirmButton:false,
            timer:2500
        });
    }).fail(function(xhr){
        Swal.fire({
            position:"center",
            icon:"error",
            title:`<span style="color:#6c757d;">${xhr.responseJSON?.message ?? 'Terjadi kesalahan'}</span>`,
            showConfirmButton:false,
            timer:2500
        });
    });
});
</script>
