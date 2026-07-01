<div id="formEditContainer" class="p-3 mb-4 rounded shadow-sm" style="display:none;background-color:#EBF5FF;border:1px solid #B3D9FF;">
    <h5 class="fw-bold mb-3" style="color:#0158a4;">Edit Data Truck</h5>
    <hr class="my-3">

    <form id="editTruckForm" class="row g-3">
        @csrf
        <input type="hidden" id="edit_truckId">

        <div class="col-md-12">
            <label class="form-label">No Truck</label>
            <input type="text" id="edit_nomorPolisi" name="nama" class="form-control" required style="height:42px;">
        </div>

        <div class="col-12 d-flex justify-content-end gap-2 mt-3">
            <button type="button" id="edit_cancelBtn" class="btn btn-secondary px-4">Batal</button>
            <button type="submit" class="btn btn-primary px-4" style="background-color:#0158a4;">Simpan</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function(){

    $('#edit_cancelBtn').on('click',function(){
        $('#editTruckForm')[0].reset();
        $('#formEditContainer').slideUp(300);
        editingId=null;
    });

    $('#editTruckForm').on('submit',function(e){
        e.preventDefault();

        if(!editingId){
            Swal.fire({
                position:"center",
                icon:"error",
                title:`<span style="color:#6c757d;">ID tidak ditemukan</span>`,
                showConfirmButton:false,
                timer:2500
            });
            return;
        }

        $.ajax({
            url:`${baseUrl}/${editingId}`,
            type:'POST',
            data:{
                nama:$('#edit_nomorPolisi').val(),
                _token:'{{ csrf_token() }}',
                _method:'PUT'
            },
            success:function(){
                fetchTruck();
                $('#editTruckForm')[0].reset();
                $('#formEditContainer').slideUp(300);
                editingId=null;

                Swal.fire({
                    position:"center",
                    icon:"success",
                    title:`<span style="color:#6c757d;">Data berhasil diperbarui</span>`,
                    showConfirmButton:false,
                    timer:2500
                });
            },
            error:function(xhr){
                Swal.fire({
                    position:"center",
                    icon:"error",
                    title:`<span style="color:#6c757d;">${xhr.responseJSON?.message ?? 'Terjadi kesalahan'}</span>`,
                    showConfirmButton:false,
                    timer:2500
                });
            }
        });
    });

});
</script>
