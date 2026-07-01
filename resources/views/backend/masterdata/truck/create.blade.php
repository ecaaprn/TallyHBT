<div id="formCreateContainer" class="p-3 mb-4 rounded shadow-sm" style="display:none;background-color:#EBF5FF;border:1px solid #B3D9FF;">
    <h5 class="fw-bold mb-3" style="color:#0158a4;">Tambah Data Truck</h5>
    <hr class="my-3">

    <form id="createTruckForm" method="POST" class="row g-3">
        @csrf

        <div class="col-md-10">
            <label class="form-label fw-bold">No Truck</label>
            <input type="text" class="form-control" id="create_nomorPolisi" name="nama" required placeholder="Contoh: B1234XYZ">
        </div>

        <div class="col-md-2 d-flex align-items-end justify-content-end gap-2">
            <button type="button" id="create_cancelBtn" class="btn btn-secondary w-50">Batal</button>
            <button type="submit" class="btn btn-primary w-50" style="background-color:#0158a4;">Simpan</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function(){

    $('#create_cancelBtn').on('click',function(){
        $('#createTruckForm')[0].reset();
        $('#formCreateContainer').slideUp(300);
    });

    $('#createTruckForm').on('submit',function(e){
        e.preventDefault();

        $.ajax({
            url:baseUrl,
            type:'POST',
            data:{
                nama:$('#create_nomorPolisi').val(),
                _token:'{{ csrf_token() }}'
            },
            success:function(){
                fetchTruck();
                $('#createTruckForm')[0].reset();
                $('#formCreateContainer').slideUp(300);

                Swal.fire({
                    position:"center",
                    icon:"success",
                    title:`<span style="color:#6c757d;">Data berhasil ditambahkan</span>`,
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
