<div id="formContainerEdit" class="p-3 mb-4 rounded shadow-sm"
    style="display:none;background-color:#EBF5FF;border:1px solid #B3D9FF;">
    <h5 class="fw-bold mb-3" style="color:#0158a4;">Edit User</h5>
    <hr class="my-3">

    <form id="userFormEdit" class="row g-3" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="col-md-3">
            <label class="form-label fw-semibold"
                style="font-size: 14px; color: #333; margin-bottom: 4px;">Group</label>
            <select id="groupUserEdit" class="form-select select2-init-edit">
                <option value="">Pilih</option>
                <option value="Tidak Ada">Tidak Ada</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold" style="font-size: 14px; color: #333; margin-bottom: 4px;">Nama</label>
            <input type="text" id="namaUserEdit" class="form-control" required
                style="height: 42px; border-radius: 8px; font-size: 15px; padding-left: 15px;">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold" style="font-size: 14px; color: #333; margin-bottom: 4px;">NIP</label>
            <input type="text" id="nipUserEdit" class="form-control"
                style="height: 42px; border-radius: 8px; font-size: 15px; padding-left: 15px;">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold"
                style="font-size: 14px; color: #333; margin-bottom: 4px;">Username</label>
            <input type="text" id="usernameUserEdit" name="username" class="form-control no-autofill" required
                style="height: 42px; border-radius: 8px; font-size: 15px; padding-left: 15px;">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold"
                style="font-size: 14px; color: #333; margin-bottom: 4px;">Email</label>
            <input type="email" id="emailUserEdit" class="form-control" required
                style="height: 42px; border-radius: 8px; font-size: 15px; padding-left: 15px;">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold" style="font-size: 14px; color: #333; margin-bottom: 4px;">Role</label>
            <select id="roleUserEdit" class="form-select select2-init-edit" required>
                <option value="superadmin">Superadmin</option>
                <option value="admin">Admin</option>
                <option value="petugas">Petugas</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold"
                style="font-size: 14px; color: #333; margin-bottom: 4px;">Cabang</label>
            <select id="cabangUserEdit" class="form-select select2-init-cabang-edit" required>
                @foreach($masterCabang as $c)
                    <option value="{{ $c->nama }}">{{ $c->display }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold" style="font-size: 14px; color: #333; margin-bottom: 4px;">Status</label>
            <select id="statusUserEdit" class="form-select select2-init-edit" required>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">NonAktif</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold"
                style="font-size: 14px; color: #333; margin-bottom: 4px;">Password</label>
            <div class="position-relative">
                <input type="password" id="passwordUserEdit" name="password" class="form-control pe-5 no-autofill"
                    placeholder="Kosongkan jika tidak diperbarui"
                    style="height: 42px; border-radius: 8px; font-size: 15px; padding-left: 15px; background-color: #fff !important;">
                <span class="toggle-password-edit position-absolute top-50 end-0 translate-middle-y me-3"
                    style="cursor:pointer; z-index: 10;">
                    <i class="fa-solid fa-lock" style="font-size: 18px; color: #6c757d !important;"></i>
                </span>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-end gap-2 mt-3">
            <button type="button" id="cancelEditBtn" class="btn btn-secondary px-4">Batal</button>
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

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px !important;
        padding-left: 15px !important;
        padding-right: 20px !important;
        font-size: 15px !important;
        color: #495057 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
    }

    input:-webkit-autofill,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:focus,
    input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px white inset !important;
        -webkit-text-fill-color: #495057 !important;
    }

    #passwordUserEdit:focus {
        border-color: #ced4da !important;
        outline: none !important;
        box-shadow: 0 0 0 0.25rem rgba(108, 117, 125, 0.2) !important;
    }

    .toggle-password-edit i {
        color: #6c757d !important;
    }
</style>

<script>
    $(function () {
        $('.select2-init-edit').select2({
            width: '100%',
            placeholder: 'Pilih',
            minimumResultsForSearch: Infinity,
            dropdownParent: $('#formContainerEdit')
        });

        $('.select2-init-cabang-edit').select2({
            width: '100%',
            placeholder: 'Pilih',
            dropdownParent: $('#formContainerEdit')
        });

        function resetPasswordToggle() {
            $('#passwordUserEdit').attr('type', 'password').val('');
            $('.toggle-password-edit i')
                .removeClass('fa-unlock')
                .addClass('fa-lock');
        }

        $('#cancelEditBtn').on('click', function () {
            $('#userFormEdit')[0].reset();
            $('#formContainerEdit').slideUp(300);
            editingId = null;
            $('.select2-init-edit,.select2-init-cabang-edit').val(null).trigger('change');
            resetPasswordToggle();
        });

        $(document).on('click', '.toggle-password-edit', function () {
            const input = $('#passwordUserEdit');
            const icon = $(this).find('i');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-lock').addClass('fa-unlock');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-unlock').addClass('fa-lock');
            }
        });

        $('#userFormEdit').on('submit', function (e) {
            e.preventDefault();
            if (!editingId) return;

            const data = {
                nama: $('#namaUserEdit').val(),
                nip: $('#nipUserEdit').val(),
                username: $('#usernameUserEdit').val(),
                email: $('#emailUserEdit').val(),
                group: $('#groupUserEdit').val(),
                role: $('#roleUserEdit').val(),
                status: $('#statusUserEdit').val(),
                cabang: $('#cabangUserEdit').val(),
                password: $('#passwordUserEdit').val(),
                _token: '{{ csrf_token() }}',
                _method: 'PUT'
            };

            $.post(`${baseUrl}/${editingId}`, data)
                .done(function () {
                    if (typeof fetchUserData === 'function') fetchUserData('', 1);
                    $('#formContainerEdit').slideUp(300);
                    editingId = null;
                    resetPasswordToggle();
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: `<span style="color:#6c757d;">Data berhasil diperbarui</span>`,
                        showConfirmButton: false,
                        timer: 2500
                    });
                })
                .fail(function (xhr) {
                    Swal.fire({
                        position: "center",
                        icon: "error",
                        title: `<span style="color:#6c757d;">${xhr.responseJSON?.message || 'Terjadi kesalahan'}</span>`,
                        showConfirmButton: false,
                        timer: 2500
                    });
                });
        });
    });
</script>