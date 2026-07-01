<div id="formEditContainer" class="p-3 mb-4 rounded shadow-sm"
    style="display:none; background-color:#EBF5FF; border:1px solid #B3D9FF;">
    <h5 class="fw-bold mb-3" style="color:#0158a4;">Edit Data Kapal</h5>
    <hr class="my-3">

    <form id="editKapalForm" class="row g-3">
        @csrf
        <input type="hidden" id="edit_kapalId">

        <div class="col-md-4">
            <label class="form-label fw-bold">Nama Kapal</label>
            <input type="text" class="form-control" id="edit_namaKapal" name="nama" required>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold">Tanggal</label>
            <input type="text" class="form-control" id="edit_tanggalKapal" name="tanggal">
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold">Status</label>
            <select class="form-select" id="edit_statusKapal" name="status" required>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">NonAktif</option>
            </select>
        </div>

        <div class="col-md-12 d-flex justify-content-end gap-2 mt-3">
            <button type="button" id="edit_cancelBtn" class="btn btn-secondary px-4">Batal</button>
            <button type="submit" class="btn btn-primary px-4" style="background-color:#0158a4;">Simpan</button>
        </div>
    </form>
</div>

<script>
    $('#edit_cancelBtn').click(function () {
        $('#editKapalForm')[0].reset();
        $('#formEditContainer').slideUp();
        editingId = null;
    });

    $('#editKapalForm').submit(function (e) {
        e.preventDefault();
        if (!editingId) {
            Swal.fire({
                position: "center",
                icon: "error",
                title: `<span style="color:#6c757d;">ID tidak ditemukan</span>`,
                showConfirmButton: false,
                timer: 2500
            });
            return;
        }
        const fd = {
            nama: $('#edit_namaKapal').val(),
            tanggal: $('#edit_tanggalKapal').val(),
            status: $('#edit_statusKapal').val(),
            _token: '{{ csrf_token() }}',
            _method: 'PUT'
        };
        $.post(`${baseUrl}/${editingId}`, fd, function () {
            loadKapal();
            $('#editKapalForm')[0].reset();
            $('#formEditContainer').slideUp();
            editingId = null;

            Swal.fire({
                position: "center",
                icon: "success",
                title: `<span style="color:#6c757d;">Data berhasil diperbarui</span>`,
                showConfirmButton: false,
                timer: 2500
            });
        }).fail(function (xhr) {
            Swal.fire({
                position: "center",
                icon: "error",
                title: `<span style="color:#6c757d;">${xhr.responseJSON?.message ?? 'Terjadi kesalahan'}</span>`,
                showConfirmButton: false,
                timer: 2500
            });
        });
    });
</script>