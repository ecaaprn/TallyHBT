<div id="formContainerCreate" class="p-3 mb-4 rounded shadow-sm"
    style="display:none;background-color:#EBF5FF;border:1px solid #B3D9FF;">
    <h5 id="formTitleCreate" class="fw-bold mb-3" style="color:#0158a4;">Tambah User</h5>
    <hr class="my-3">

    <form id="userFormCreate" class="row g-3">
        @csrf

        <div class="col-md-3">
            <label class="form-label fw-semibold"
                style="font-size: 14px; color: #333; margin-bottom: 4px;">Group</label>
            <select class="form-select select2-init-create" id="groupUserCreate" name="group" required>
                <option value="Tidak Ada">Tidak Ada</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold" style="font-size: 14px; color: #333; margin-bottom: 4px;">Nama</label>
            <input type="text" class="form-control" id="namaUserCreate" name="nama" placeholder="Masukkan Nama"required 
                style="height: 42px; border-radius: 8px; font-size: 15px; padding-left: 15px;">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold" style="font-size: 14px; color: #333; margin-bottom: 4px;">NIP</label>
            <input type="text" class="form-control" id="nipUserCreate" name="nip" placeholder="Masukkan NIP" required 
                style="height: 42px; border-radius: 8px; font-size: 15px; padding-left: 15px;">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold"
                style="font-size: 14px; color: #333; margin-bottom: 4px;">Username</label>
            <input type="text" class="form-control" id="usernameUserCreate" name="username" placeholder="Masukkan Username" required
                style="height: 42px; border-radius: 8px; font-size: 15px; padding-left: 15px;">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold"
                style="font-size: 14px; color: #333; margin-bottom: 4px;">Email</label>
            <input type="email" class="form-control" id="emailUserCreate" name="email" placeholder="Masukkan Email" required
                style="height: 42px; border-radius: 8px; font-size: 15px; padding-left: 15px;">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold" style="font-size: 14px; color: #333; margin-bottom: 4px;">Role</label>
            <select class="form-select select2-init-create" id="roleUserCreate" name="role" required>
                <option value="superadmin">Superadmin</option>
                <option value="admin">Admin</option>
                <option value="petugas">Petugas</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold"
                style="font-size: 14px; color: #333; margin-bottom: 4px;">Cabang</label>
            <select id="cabangUserCreate" name="cabang" class="form-select select2-init-cabang-create" required>
                @isset($masterCabang)
                    @foreach($masterCabang as $c)
                        <option value="{{ $c->nama }}">{{ $c->display }}</option>
                    @endforeach
                @endisset
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold"
                style="font-size: 14px; color: #333; margin-bottom: 4px;">Status</label>
            <select class="form-select select2-init-create" id="statusUserCreate" name="status" required>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">NonAktif</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold"
                style="font-size: 14px; color: #333; margin-bottom: 4px;">Password</label>
            <div class="position-relative">
                <input type="password" class="form-control pe-5" id="passwordUserCreate" name="password" placeholder="Masukkan Password" required
                    style="height: 42px; border-radius: 8px; font-size: 15px; padding-left: 15px;">
                <span class="toggle-password-create position-absolute top-50 end-0 translate-middle-y me-3"
                    style="cursor:pointer; z-index: 10;">
                    <i class="fa-solid fa-lock" style="font-size: 18px; color: #6c757d !important;"></i>
                </span>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-end gap-2 mt-3">
            <button type="button" id="cancelCreateBtn" class="btn btn-secondary px-4">Batal</button>
            <button type="submit" class="btn btn-primary px-4" style="background-color:#0158a4;">Simpan</button>
        </div>
    </form>
</div>

<style>
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border-radius: 8px !important;
        border: 1px solid #ced4da !important;
        display: flex !important;
        align-items: center !important;
    }

    .select2-selection__rendered {
        line-height: 42px !important;
        padding-left: 15px !important;
        padding-right: 20px !important;
        font-size: 15px !important;
        color: #495057 !important;
    }

    .select2-selection__placeholder {
        color: #adb5bd !important;
    }

    .select2-selection__arrow {
        height: 42px !important;
    }

    .toggle-password-create i {
        transition: color .2s;
    }
</style>

<script>
    $(document).ready(function () {
        $('.select2-init-create').select2({
            width: '100%',
            placeholder: 'Pilih',
            minimumResultsForSearch: Infinity,
            dropdownParent: $('#formContainerCreate')
        });

        $('.select2-init-cabang-create').select2({
            width: '100%',
            placeholder: 'Pilih',
            dropdownParent: $('#formContainerCreate')
        });

        $('#cancelCreateBtn').on('click', function () {
            $('#userFormCreate')[0].reset();
            $('#formContainerCreate').slideUp(300);
            $('.select2-init-create,.select2-init-cabang-create').val(null).trigger('change');
            $('#passwordUserCreate').attr('type', 'password');
            $('.toggle-password-create i').removeClass('fa-unlock text-primary text-secondary').addClass('fa-lock').attr('style', 'font-size: 18px; color: #6c757d !important;');
        });

        $(document).on('click', '.toggle-password-create', function () {
            const input = $('#passwordUserCreate');
            const icon = $(this).find('i');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-lock text-primary text-secondary').addClass('fa-unlock').attr('style', 'font-size: 18px; color: #6c757d !important;');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-unlock text-primary text-secondary').addClass('fa-lock').attr('style', 'font-size: 18px; color: #6c757d !important;');
            }
        });

        $('#userFormCreate').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: baseUrl,
                type: 'POST',
                data: $(this).serialize(),
                success: function () {
                    if (typeof fetchUserData === 'function') { fetchUserData('', 1); }
                    $('#cancelCreateBtn').click();
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: `<span style="color:#6c757d;">Data berhasil ditambahkan</span>`,
                        showConfirmButton: false,
                        timer: 2500
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        position: "center",
                        icon: "error",
                        title: `<span style="color:#6c757d;">${xhr.responseJSON?.message ?? 'Terjadi kesalahan'}</span>`,
                        showConfirmButton: false,
                        timer: 2500
                    });
                }
            });
        });
    });
</script>