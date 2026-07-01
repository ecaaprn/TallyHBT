<div id="formContainerCreate" class="p-3 mb-4 rounded shadow-sm" style="display:none;background-color:#EBF5FF;border:1px solid #B3D9FF;">
    <h5 class="fw-bold mb-3" style="color:#0158a4;">Tambah Akses Menu Baru</h5>
    <hr class="my-3">

    <form id="aksesFormCreate" method="POST" class="row g-3">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="akses_id" id="aksesIdCreate">

        <div class="col-md-2">
            <label class="form-label">Nama User</label>
            <select id="namaUserCreate" name="user_id" class="form-select select2-searchable" required>
                <option></option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" data-cabang="{{ $u->cabang }}" data-role="{{ $u->role }}" data-status="{{ $u->status }}">{{ $u->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Cabang Asal</label>
            <input type="text" id="cabangUserCreate" class="form-control" readonly disabled style="height:42px;background:#f0f0f0;border-radius:8px;padding-left:15px;">
        </div>

        <div class="col-md-2">
            <label class="form-label">Role</label>
            <input type="text" id="roleUserCreate" class="form-control" readonly disabled style="height:42px;background:#f0f0f0;border-radius:8px;padding-left:15px;">
        </div>

        <div class="col-md-2">
            <label class="form-label">Status</label>
            <input type="text" id="statusUserCreate" class="form-control" readonly disabled style="height:42px;background:#f0f0f0;border-radius:8px;padding-left:15px;">
        </div>

        <div class="col-md-2">
            <label class="form-label">Akses Cabang</label>
            <select id="aksesCabangCreate" name="akses_cabang" class="form-select select2-searchable" required>
                <option></option>
                <option value="semua">Semua Cabang</option>
                <option value="spesifik">Cabang Tertentu</option>
            </select>
        </div>

        <div class="col-md-2 cabangSelectContainerCreate" style="display:none;">
            <label class="form-label">Cabang yang Diizinkan</label>
            <select id="cabangSelectCreate" name="cabang_id" class="form-select select2-searchable">
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
                    <div class="p-3 rounded menu-card">
                        <h6 class="fw-bold menu-card-header">Menu Utama</h6>
                        <div class="mt-2">
                            <div class="form-check"><input class="form-check-input aksesMenu" type="checkbox" value="Monitoring"><label class="form-check-label">Monitoring</label></div>
                            <div class="form-check"><input class="form-check-input aksesMenu" type="checkbox" value="Input Data"><label class="form-check-label">Input Data</label></div>
                            <div class="form-check"><input class="form-check-input aksesMenu" type="checkbox" value="Master Data"><label class="form-check-label">Master Data</label></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 rounded menu-card">
                        <h6 class="fw-bold menu-card-header">Menu Admin Sidebar</h6>
                        <div class="mt-2">
                            <div class="form-check"><input class="form-check-input aksesMenu" type="checkbox" value="Beranda"><label class="form-check-label">Beranda</label></div>
                            <div class="form-check"><input class="form-check-input aksesMenu" type="checkbox" value="Monitoring Data"><label class="form-check-label">Monitoring Data</label></div>
                            <div class="form-check"><input id="ManajemenDataParent" class="form-check-input aksesMenu" type="checkbox" value="Manajemen Data"><label class="form-check-label">Manajemen Data</label></div>
                            <div class="form-check"><input id="PengaturanParent" class="form-check-input aksesMenu" type="checkbox" value="Pengaturan"><label class="form-check-label">Pengaturan</label></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div id="child-ManajemenData" class="p-3 rounded menu-card" style="display:none;">
                        <h6 class="fw-bold menu-card-header">Sub-Menu Manajemen Data</h6>
                        <div class="mt-2">
                            <div class="form-check ms-2"><input class="form-check-input subMenuManajemenData aksesMenu" type="checkbox" value="Booster"><label class="form-check-label">Booster</label></div>
                            <div class="form-check ms-2"><input class="form-check-input subMenuManajemenData aksesMenu" type="checkbox" value="Cabang"><label class="form-check-label">Cabang</label></div>
                            <div class="form-check ms-2"><input class="form-check-input subMenuManajemenData aksesMenu" type="checkbox" value="Hose"><label class="form-check-label">Hose</label></div>
                            <div class="form-check ms-2"><input class="form-check-input subMenuManajemenData aksesMenu" type="checkbox" value="Kapal"><label class="form-check-label">Kapal</label></div>
                            <div class="form-check ms-2"><input class="form-check-input subMenuManajemenData aksesMenu" type="checkbox" value="Palka"><label class="form-check-label">Palka</label></div>
                            <div class="form-check ms-2"><input class="form-check-input subMenuManajemenData aksesMenu" type="checkbox" value="Truck"><label class="form-check-label">Truck</label></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div id="child-Pengaturan" class="p-3 rounded menu-card" style="display:none;">
                        <h6 class="fw-bold menu-card-header">Sub-Menu Pengaturan</h6>
                        <div class="mt-2">
                            <div class="form-check ms-2"><input class="form-check-input subMenuPengaturan aksesMenu" type="checkbox" value="Role User"><label class="form-check-label">Role User</label></div>
                            <div class="form-check ms-2"><input class="form-check-input subMenuPengaturan aksesMenu" type="checkbox" value="Akses Menu"><label class="form-check-label">Akses Menu</label></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-end gap-2 mt-3">
            <button type="button" id="cancelCreateBtn" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn btn-primary" style="background:#0158a4;">Simpan</button>
        </div>
    </form>
</div>

<style>
.menu-card{
    background:#fff;
    border:1px solid #cce5ff;
    box-shadow:0 .125rem .25rem rgba(0,0,0,.075);
    height:100%;
}
.menu-card-header{
    background:#e9f5ff;
    border-bottom:1px solid #cce5ff;
    padding:.5rem 1rem;
}
.select2-container{
    width:100%!important;
}
.select2-container--default .select2-selection--single{
    height:42px!important;
    border-radius:8px!important;
    border:1px solid #ced4da!important;
    display:flex!important;
    align-items:center!important;
    background-color: #fff!important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 42px!important;
    color: #495057 !important;
    padding-left: 2px!important;
    padding-right: 20px!important;
    text-align: left !important;
}
.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #adb5bd !important;
    font-size: 15px;
    text-align: left !important;
}
.select2-selection__arrow{
    height:42px!important;
}
.select2-selection__clear {
    display: none !important;
}
</style>

<script>
function capitalize(s){return typeof s==='string'?s.charAt(0).toUpperCase()+s.slice(1).toLowerCase():''}

$(document).ready(function(){
    $('.select2-searchable').select2({width:'100%',placeholder:'Pilih',allowClear:false});

    $('#namaUserCreate').on('change',function(){
        const o=this.options[this.selectedIndex];
        if(o&&o.value!==""){
            $('#cabangUserCreate').val(capitalize(o.dataset.cabang));
            $('#roleUserCreate').val(capitalize(o.dataset.role));
            $('#statusUserCreate').val(capitalize(o.dataset.status));
        }else{
            $('#cabangUserCreate,#roleUserCreate,#statusUserCreate').val('');
        }
    });

    $('#aksesCabangCreate').on('change',function(){
        if(this.value==='spesifik') $('.cabangSelectContainerCreate').slideDown(200);
        else{
            $('.cabangSelectContainerCreate').slideUp(200);
            $('#cabangSelectCreate').val(null).trigger('change');
        }
    });

    $('#ManajemenDataParent').on('change',function(){
        if(this.checked) $('#child-ManajemenData').slideDown(200);
        else{
            $('#child-ManajemenData').slideUp(200);
            $('.subMenuManajemenData').prop('checked',false);
        }
    });

    $('#PengaturanParent').on('change',function(){
        if(this.checked) $('#child-Pengaturan').slideDown(200);
        else{
            $('#child-Pengaturan').slideUp(200);
            $('.subMenuPengaturan').prop('checked',false);
        }
    });

    $('#cancelCreateBtn').on('click',function(){
        $('#aksesFormCreate')[0].reset();
        $('.select2-searchable').val(null).trigger('change');
        $('#child-ManajemenData,#child-Pengaturan').hide();
        $('#formContainerCreate').slideUp(300);
    });

    const baseUrl = window.baseUrl || '/akses-menu';

    $('#aksesFormCreate').on('submit', function(e) {
        e.preventDefault();

        const userId = $('#namaUserCreate').val();
        const aksesCabang = $('#aksesCabangCreate').val();
        const cabangSelect = $('#cabangSelectCreate').val();

        const aksesMenu = $('.aksesMenu:checked:not(:disabled)').map(function () {
            return this.value;
        }).get();

        const payload = {
            user_id: userId,
            akses_cabang: aksesCabang,
            cabang_id: (aksesCabang === 'semua') ? null : cabangSelect,
            _token: '{{ csrf_token() }}'
        };

        aksesMenu.forEach((menu, index) => {
            payload[`akses_menu[${index}]`] = menu;
        });

        $.ajax({
            url: baseUrl,
            type: 'POST',
            data: payload,
            traditional: true,
            success: (response) => {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: `<span style="color:#6c757d;">${response.message || 'Data berhasil ditambahkan'}</span>`,
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
 