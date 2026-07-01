<div id="formEditContainer" class="p-3 mb-4 rounded shadow-sm" style="display:none; background-color:#EBF5FF; border:1px solid #B3D9FF;">
    <h5 class="fw-bold mb-3" style="color:#0158a4;">Edit Data Booster</h5>
    <hr class="my-3">

    <form id="editBoosterForm" class="row g-3">
        @csrf
        <input type="hidden" id="edit_boosterId">

        <div class="col-md-10">
            <label class="form-label fw-bold">Nama Booster</label>
            <input type="text" class="form-control" id="edit_namaBooster" name="nama" required>
        </div>

        <div class="col-md-2 d-flex align-items-end justify-content-end gap-2">
            <button type="button" id="edit_cancelBtn" class="btn btn-secondary w-50">Batal</button>
            <button type="submit" class="btn btn-primary w-50" style="background-color:#0158a4;">Simpan</button>
        </div>
    </form>
</div>

<script>
$(document).on('click','.editBoosterBtn',function(){
    const id=$(this).data('id');
    const nama=$(this).data('nama');
    $('#edit_boosterId').val(id);
    $('#edit_namaBooster').val(nama);
    $('#formEditContainer').slideDown();
    $('html,body').animate({scrollTop:$('#formEditContainer').offset().top-100},300);
});

$('#edit_cancelBtn').click(function(){
    $('#editBoosterForm')[0].reset();
    $('#formEditContainer').slideUp();
});

$('#editBoosterForm').submit(function(e){
    e.preventDefault();
    const id=$('#edit_boosterId').val();
    const fd={
        nama:$('#edit_namaBooster').val(),
        _token:'{{ csrf_token() }}',
        _method:'PUT'
    };
    $.post(baseUrl+'/'+id,fd,function(){
        fetchBoosterData();
        $('#editBoosterForm')[0].reset();
        $('#formEditContainer').slideUp();

        Swal.fire({
            position:"center",
            icon:"success",
            title:`<span style="color:#6c757d;">Data berhasil diperbarui</span>`,
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
