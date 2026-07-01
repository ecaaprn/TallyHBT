<div id="formCreateContainer" class="p-3 mb-4 rounded shadow-sm"
    style="display:none; background-color:#EBF5FF; border:1px solid #B3D9FF;">
    <h5 class="fw-bold mb-3" style="color:#0158a4;">Tambah Data Kapal</h5>
    <hr class="my-3">

    <form id="createKapalForm" class="row g-3">
        @csrf

        <div class="col-md-4">
            <label class="form-label fw-bold">Nama Kapal</label>
            <input type="text" class="form-control" id="create_namaKapal" name="nama" required>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold">Tanggal</label>
            <input type="text" class="form-control" id="create_tanggalKapal" name="tanggal"
                placeholder="Contoh: 27/10/2025">
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold">Status</label>
            <select class="form-select" id="create_statusKapal" name="status" required>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">NonAktif</option>
            </select>
        </div>

        <div class="col-md-12 d-flex justify-content-end gap-2 mt-3">
            <button type="button" id="create_cancelBtn" class="btn btn-secondary px-4">Batal</button>
            <button type="submit" class="btn btn-primary px-4" style="background-color:#0158a4;">Simpan</button>
        </div>
    </form>
</div>

<script>
    $('#create_cancelBtn').click(function () {
        $('#createKapalForm')[0].reset();
        $('#formCreateContainer').slideUp();
    });

    $('#createKapalForm').submit(function (e) {
        e.preventDefault();
        const fd = {
            nama: $('#create_namaKapal').val(),
            tanggal: $('#create_tanggalKapal').val(),
            status: $('#create_statusKapal').val(),
            _token: '{{ csrf_token() }}'
        };
        $.post(baseUrl, fd, function () {
            loadKapal();
            $('#createKapalForm')[0].reset();
            $('#formCreateContainer').slideUp();

            Swal.fire({
                position: "center",
                icon: "success",
                title: `<span style="color:#6c757d;">Data berhasil ditambahkan</span>`,
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