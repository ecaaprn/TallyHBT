<div id="formCreateContainer" class="p-3 mb-4 rounded shadow-sm" style="display:none; background-color:#EBF5FF; border:1px solid #B3D9FF;">
    <h5 class="fw-bold mb-3" style="color:#0158a4;">Tambah Data Cabang</h5>
    <hr class="my-3">

    <form id="createCabangForm" class="row g-3">
        @csrf

        <div class="col-md-10">
            <label class="form-label fw-bold">Cabang</label>
            <input type="text" class="form-control" id="create_namaCabang" name="nama" required placeholder="Contoh: Cabang Jakarta Pusat">
        </div>

        <div class="col-md-2 d-flex align-items-end justify-content-end gap-2">
            <button type="button" id="create_cancelBtn" class="btn btn-secondary w-50">Batal</button>
            <button type="submit" class="btn btn-primary w-50" style="background-color:#0158a4;">Simpan</button>
        </div>
    </form>
</div>

<script>
$('#create_cancelBtn').click(function(){
    $('#createCabangForm')[0].reset();
    $('#formCreateContainer').slideUp();
});

$('#createCabangForm').submit(function(e){
    e.preventDefault();
    const fd={
        nama:$('#create_namaCabang').val(),
        _token:'{{ csrf_token() }}'
    };
    $.post(baseUrl,fd,function(){
        fetchCabangData();
        $('#createCabangForm')[0].reset();
        $('#formCreateContainer').slideUp();

        Swal.fire({
            position: "center",
            icon: "success",
            title: '<span style="color: #6c757d;">Data berhasil ditambahkan</span>',
            showConfirmButton: false,
            timer: 2500
        });
    }).fail(function(xhr){
        Swal.fire('Gagal',xhr.responseJSON?.message ?? 'Terjadi kesalahan','error');
    });
});
</script>
