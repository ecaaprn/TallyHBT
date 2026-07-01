<div id="formContainerEdit" class="p-3 mb-4 rounded shadow-sm" style="display:none;background-color:#EBF5FF;border:1px solid #B3D9FF;">
    <h5 class="fw-bold mb-3" style="color:#0158a4;">Edit Akses Menu User</h5>
    <hr class="my-3">

    <form id="aksesFormEdit" method="POST" class="row g-3">
        @csrf
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" id="aksesIdEdit">
        <input type="hidden" id="userIdHiddenEdit" name="user_id">

        <div class="col-md-2">
            <label class="form-label">Nama User</label>
            <select id="namaUserEdit" class="form-select select2-searchable" disabled>
                <option></option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Cabang Asal</label>
            <input type="text" id="cabangUserEdit" class="form-control readonly-field" readonly disabled>
        </div>

        <div class="col-md-2">
            <label class="form-label">Role</label>
            <input type="text" id="roleUserEdit" class="form-control readonly-field" readonly disabled>
        </div>

        <div class="col-md-2">
            <label class="form-label">Status</label>
            <input type="text" id="statusUserEdit" class="form-control readonly-field" readonly disabled>
        </div>

        <div class="col-md-2">
            <label class="form-label">Akses Cabang</label>
            <select id="aksesCabangEdit" name="akses_cabang" class="form-select select2-searchable">
                <option></option>
                <option value="semua">Semua Cabang</option>
                <option value="spesifik">Cabang Tertentu</option>
            </select>
        </div>

        <div class="col-md-2 cabangSelectContainerEdit" style="display:none;">
            <label class="form-label">Cabang yang Diizinkan</label>
            <select id="cabangSelectEdit" name="cabang_id" class="form-select select2-searchable">
                <option></option>
                @foreach($cabangs as $c)
                    <option value="{{ $c->id }}">{{ $c->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-12 mt-4">
            <label class="form-label">Akses Menu</label>
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="menu-card">
                        <h6 class="menu-card-header">Menu Utama</h6>
                        <div class="form-check"><input class="form-check-input aksesMenuEdit" type="checkbox" value="Monitoring"><label class="form-check-label">Monitoring</label></div>
                        <div class="form-check"><input class="form-check-input aksesMenuEdit" type="checkbox" value="Input Data"><label class="form-check-label">Input Data</label></div>
                        <div class="form-check"><input class="form-check-input aksesMenuEdit" type="checkbox" value="Master Data"><label class="form-check-label">Master Data</label></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="menu-card">
                        <h6 class="menu-card-header">Menu Admin Sidebar</h6>
                        <div class="form-check"><input class="form-check-input aksesMenuEdit" type="checkbox" value="Beranda"><label class="form-check-label">Beranda</label></div>
                        <div class="form-check"><input class="form-check-input aksesMenuEdit" type="checkbox" value="Monitoring Data"><label class="form-check-label">Monitoring Data</label></div>
                        <div class="form-check"><input id="ManajemenDataParentEdit" class="form-check-input aksesMenuEdit" type="checkbox" value="Manajemen Data"><label class="form-check-label">Manajemen Data</label></div>
                        <div class="form-check"><input id="PengaturanParentEdit" class="form-check-input aksesMenuEdit" type="checkbox" value="Pengaturan"><label class="form-check-label">Pengaturan</label></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div id="child-ManajemenDataEdit" class="menu-card" style="display:none;">
                        <h6 class="menu-card-header">Sub-Menu Manajemen Data</h6>
                        <div class="form-check ms-2"><input class="form-check-input subMenuManajemenDataEdit aksesMenuEdit" type="checkbox" value="Booster"><label class="form-check-label">Booster</label></div>
                        <div class="form-check ms-2"><input class="form-check-input subMenuManajemenDataEdit aksesMenuEdit" type="checkbox" value="Cabang"><label class="form-check-label">Cabang</label></div>
                        <div class="form-check ms-2"><input class="form-check-input subMenuManajemenDataEdit aksesMenuEdit" type="checkbox" value="Hose"><label class="form-check-label">Hose</label></div>
                        <div class="form-check ms-2"><input class="form-check-input subMenuManajemenDataEdit aksesMenuEdit" type="checkbox" value="Kapal"><label class="form-check-label">Kapal</label></div>
                        <div class="form-check ms-2"><input class="form-check-input subMenuManajemenDataEdit aksesMenuEdit" type="checkbox" value="Palka"><label class="form-check-label">Palka</label></div>
                        <div class="form-check ms-2"><input class="form-check-input subMenuManajemenDataEdit aksesMenuEdit" type="checkbox" value="Truck"><label class="form-check-label">Truck</label></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div id="child-PengaturanEdit" class="menu-card" style="display:none;">
                        <h6 class="menu-card-header">Sub-Menu Pengaturan</h6>
                        <div class="form-check ms-2"><input class="form-check-input subMenuPengaturanEdit aksesMenuEdit" type="checkbox" value="Role User"><label class="form-check-label">Role User</label></div>
                        <div class="form-check ms-2"><input class="form-check-input subMenuPengaturanEdit aksesMenuEdit" type="checkbox" value="Akses Menu"><label class="form-check-label">Akses Menu</label></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-end gap-2 mt-3">
            <button type="button" id="cancelEditBtn" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn btn-primary" style="background:#0158a4;">Simpan</button>
        </div>
    </form>
</div>

<style>
.readonly-field {
    height: 42px;
    background: #f0f0f0;
    border-radius: 8px;
    padding-left: 15px;
}
.menu-card {
    background: #ffffff;
    border: 1px solid #cce5ff;
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    padding: 12px;
    height: 100%;
}
.menu-card-header {
    background: #e9f5ff;
    border-bottom: 1px solid #cce5ff;
    padding: 8px 12px;
    font-weight: 600;
}
.select2-container {
    width: 100% !important;
}
.select2-container--default .select2-selection--single {
    height: 42px !important;
    border-radius: 8px !important;
    border: 1px solid #ced4da !important;
    display: flex !important;
    align-items: center !important;
}
.select2-selection__rendered {
    line-height: 42px !important;
    color: #495057 !important;
}
.select2-selection__arrow {
    height: 42px !important;
}
.select2-selection__clear {
    display: none !important;
}
</style>

<script>
$(document).ready(function() {
    const baseUrl = window.baseUrl || '/akses-menu';

    $('#aksesFormEdit').on('submit', function(e) {
        e.preventDefault();

        const id = $('#aksesIdEdit').val();
        const aksesCabang = $('#aksesCabangEdit').val();
        const cabangSelect = $('#cabangSelectEdit').val();

        const aksesMenu = $('.aksesMenuEdit:checked:not(:disabled)').map(function () {
            return this.value;
        }).get();

        const payload = {
            akses_cabang: aksesCabang,
            cabang_id: (aksesCabang === 'semua') ? null : cabangSelect,
            _token: '{{ csrf_token() }}',
            _method: 'PUT'
        };

        aksesMenu.forEach((menu, index) => {
            payload[`akses_menu[${index}]`] = menu;
        });

        $.ajax({
            url: `${baseUrl}/${id}`,
            type: 'POST',
            data: payload,
            traditional: true,
            success: (response) => {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: `<span style="color:#6c757d;">${response.message || 'Data berhasil diperbarui'}</span>`,
                    showConfirmButton: false,
                    timer: 1000
                });
                setTimeout(function() {
                    location.reload();
                }, 1000);
            },
            error: (xhr) => {
                let errorMessage = xhr.responseJSON?.message ?? 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join(', ');
                }
                Swal.fire({
                    position: "center",
                    icon: "error",
                    title: `<span style="color:#6c757d;">${errorMessage}</span>`,
                    showConfirmButton: false,
                    timer: 2500
                });
            }
        });
    });
});
</script>

