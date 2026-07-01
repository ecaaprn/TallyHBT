@extends('layouts.admin')

@section('title', 'Akses Menu')

@section('content')
<div class="card shadow rounded">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center" style="border-bottom: none !important;">
        <h4 class="fw-bold text-uppercase mb-2 mb-md-0" style="color: #000;">AKSES MENU USER</h4>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari Nama, User, NIP" style="border-radius: 8px; width: 280px;">
            <button id="toggleFormBtn" class="btn" style="border-radius: 8px; background-color: #0158a4; color: #fff; border: none; font-weight: 600; transition: all .2s ease;">
                <i class="fa-solid fa-square-plus me-2"></i>Tambah
            </button>
        </div>
    </div>

    <div class="card-body">
        <div id="formContainerWrapper">
            @include('backend.pengaturan.akses-menu.create')
            @include('backend.pengaturan.akses-menu.edit')
        </div>

        <div class="table-responsive">
            <table class="table align-middle text-center table-hover table-nowrap" style="min-width: 1600px; font-size: 0.95rem;">
                <thead>
                    <tr>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">No</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Group</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Nama User</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">NIP</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Username</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Email</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Role</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Menu</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Cabang Akses</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Status</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Dibuat</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px; width: 140px;">Diperbarui</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="aksesMenuTableBody">
                    @forelse($akses as $i => $row)
                        @php
                            $menus = is_string($row->akses_menu) ? json_decode($row->akses_menu, true) : $row->akses_menu;
                            $menusClean = array_map(fn($m) => trim($m, '[]'), (array)($menus ?? []));
                            $status = strtolower($row->user?->status ?? '-');
                            $cabangAksesObj = $cabangs->firstWhere('id', $row->cabang_id);
                            $aksesCabangDisplay = $row->akses_cabang === 'semua' ? 'Semua Cabang' : ($cabangAksesObj?->nama ?? '-');
                        @endphp
                        <tr data-id="{{ $row->id }}">
                            <td style="text-align: center; white-space: nowrap;">{{ $i + 1 }}</td>
                            <td style="text-align: center; white-space: nowrap;">{{ $row->user?->group ?? '-' }}</td>
                            <td style="text-align: start; white-space: nowrap;">{{ $row->user?->nama ?? '-' }}</td>
                            <td style="text-align: center; white-space: nowrap;">{{ $row->user?->nip ?? '-' }}</td>
                            <td style="text-align: center; white-space: nowrap;">{{ $row->user?->username ?? '-' }}</td>
                            <td style="text-align: start; white-space: nowrap;">{{ $row->user?->email ?? '-' }}</td>
                            <td style="text-align: center; white-space: nowrap; text-transform: capitalize;">{{ $row->user?->role ?? '-' }}</td>
                            <td style="text-align: start; white-space: nowrap;">
                                @if(!empty($menusClean))
                                    <div style="display: flex; flex-wrap: wrap; gap: 4px; justify-content: center;">
                                        @foreach($menusClean as $menu)
                                            <span class="badge" style="font-size: 0.75rem; padding: 4px 8px; color: #fff; background-color: #495057;">
                                                {{ $menu }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td style="text-align: center; white-space: nowrap;">{{ $aksesCabangDisplay }}</td>
                            <td style="text-align: center; white-space: nowrap;">
                                <span class="badge {{ $status === 'aktif' ? 'bg-success' : 'bg-danger' }}" style="text-transform: capitalize;">
                                    {{ $status }}
                                </span>
                            </td>
                            <td style="text-align: center; white-space: nowrap;">{{ \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H.i') }}</td>
                            <td style="text-align: center; white-space: nowrap;">{{ \Carbon\Carbon::parse($row->updated_at)->format('d-m-Y H.i') }}</td>
                            <td style="text-align: center; white-space: nowrap;">
                                <button class="btn editBtn btn-warning" 
                                    data-id="{{ $row->id }}"
                                    data-user-id="{{ $row->user_id }}" 
                                    data-akses-cabang="{{ $row->akses_cabang }}"
                                    data-cabang-id="{{ $row->cabang_id }}" 
                                    data-akses-menu="{{ json_encode($menusClean) }}"
                                    title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center text-muted py-3">Belum ada data akses menu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div id="paginationContainer" class="d-flex justify-content-center mt-4"></div>
    </div>
</div>

<style>
    .nowrap {
        white-space: nowrap;
    }

    .capitalize {
        text-transform: capitalize;
    }

    .badge {
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 6px;
    }

    .editBtn {
        background-color: #ffc107;
        border: none;
        border-radius: 6px;
        padding: 5px 7px;
        transition: background-color 0.2s;
    }

    .editBtn:hover {
        background-color: #e0a800;
    }

    .editBtn i {
        color: #000;
        font-size: 1rem;
    }

    .disabled-menu {
        color: #adb5bd !important;
        cursor: not-allowed !important;
    }

    .select2-container--default .select2-selection--single {
        border-radius: 8px !important;
        height: 42px !important;
        display: flex !important;
        align-items: center !important;
        border: 1px solid #ced4da !important;
        padding: 0 8px !important;
    }

    .select2-selection__rendered {
        font-size: 15px !important;
        line-height: 42px !important;
        color: #212529 !important;
    }

    .select2-selection__arrow {
        height: 42px !important;
        top: 0 !important;
    }

    .pagination {
        border-radius: 8px;
    }

    .page-item .page-link {
        border: none !important;
        padding: .5rem .75rem;
        margin: 0 5px;
        color: #495057;
        background: transparent !important;
        font-weight: 600;
        border-radius: 8px !important;
    }

    .page-item .page-link:hover {
        color: #003366;
        background: #e9ecef !important;
    }

    .page-item.active .page-link {
        background: #0158a4 !important;
        color: white !important;
        pointer-events: none;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    const baseUrl = "{{ url('admin/pengaturan/akses-menu') }}";
    window.baseUrl = baseUrl;
    const usersBootstrap = @json($users);
    let usersMap = {};
    usersBootstrap.forEach(u => usersMap[u.id] = u);

    let currentSearch = '';
    let searchTimer = null;

    function capitalize(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function formatDateTime(v) {
        if (!v) return '-';
        const d = new Date(v);
        if (isNaN(d.getTime())) return '-';
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();
        const h = String(d.getHours()).padStart(2, '0');
        const m = String(d.getMinutes()).padStart(2, '0');
        return `${day}-${month}-${year} ${h}.${m}`;
    }

    function renderPaginationNumeric(links) {
        const c = $('#paginationContainer');
        c.empty();
        if (!links || links.length <= 3) return;

        const ul = $('<ul class="pagination mb-0"></ul>');
        links.forEach(l => {
            if (/^\d+$/.test(l.label) || l.active || l.label.includes('...')) {
                const li = $('<li class="page-item"></li>');
                const btn = $(`<button class="page-link" type="button">${l.label}</button>`);

                if (l.active) {
                    li.addClass('active');
                } else if (l.url) {
                    const p = new URL(l.url).searchParams.get('page');
                    btn.on('click', () => fetchAksesMenu(currentSearch, p));
                } else {
                    li.addClass('disabled');
                }
                li.append(btn);
                ul.append(li);
            }
        });
        c.append(ul);
    }

    function fetchAksesMenu(q = '', page = 1) {
        currentSearch = q;
        const tb = $('#aksesMenuTableBody');

        $.get(`${window.baseUrl}?search=${encodeURIComponent(q)}&page=${page}&ajax=1`, res => {
            const t = res.akses_menu;
            const data = t.data;
            tb.empty();

            if (!data.length) {
                tb.append('<tr><td colspan="13" class="text-center text-muted py-3">Tidak ada data ditemukan</td></tr>');
                renderPaginationNumeric([]);
                return;
            }

            data.forEach((item, index) => {
                const rowNumber = t.from + index;
                renderRowContent(item, rowNumber);
            });

            renderPaginationNumeric(t.links);
        }).fail(() => {
            tb.html('<tr><td colspan="13" class="text-danger py-3 text-center">Gagal memuat data</td></tr>');
            renderPaginationNumeric([]);
        });
    }

    function renderRowContent(data, rowNumber) {
        const status = (data.user?.status || '-').toLowerCase();
        const cabangAkses = data.akses_cabang === 'semua' ? 'Semua Cabang' : (data.cabang?.nama || '-');
        const menus = Array.isArray(data.akses_menu) ? data.akses_menu : JSON.parse(data.akses_menu || '[]');
        const menusClean = menus.map(m => m.trim().replace(/^\[|\]$/g, ''));
        
        const menusHtml = menusClean.length > 0
            ? `<div style="display: flex; flex-wrap: wrap; gap: 4px; justify-content: center;">${menusClean.map(m => `<span class="badge" style="font-size: 0.75rem; padding: 4px 8px; color: #fff; background-color: #495057;">${m}</span>`).join('')}</div>`
            : '<span class="text-muted">-</span>';

        const html = `
            <tr data-id="${data.id}">
                <td style="text-align: center; white-space: nowrap;">${rowNumber}</td>
                <td style="text-align: center; white-space: nowrap;">${data.user?.group || '-'}</td>
                <td style="text-align: start; white-space: nowrap;">${data.user?.nama || '-'}</td>
                <td style="text-align: center; white-space: nowrap;">${data.user?.nip || '-'}</td>
                <td style="text-align: center; white-space: nowrap;">${data.user?.username || '-'}</td>
                <td style="text-align: start; white-space: nowrap;">${data.user?.email || '-'}</td>
                <td style="text-align: center; white-space: nowrap; text-transform: capitalize;">${data.user?.role || '-'}</td>
                <td style="text-align: start; white-space: nowrap;">${menusHtml}</td>
                <td style="text-align: center; white-space: nowrap;">${cabangAkses}</td>
                <td style="text-align: center; white-space: nowrap;"><span class="badge ${status === 'aktif' ? 'bg-success' : 'bg-danger'}" style="text-transform: capitalize;">${status}</span></td>
                <td style="text-align: center; white-space: nowrap;">${formatDateTime(data.created_at)}</td>
                <td style="text-align: center; white-space: nowrap;">${formatDateTime(data.updated_at)}</td>
                <td style="text-align: center; white-space: nowrap;">
                    <button class="btn editBtn btn-warning" data-id="${data.id}" data-user-id="${data.user_id}" data-akses-cabang="${data.akses_cabang}" data-cabang-id="${data.cabang_id}" data-akses-menu='${JSON.stringify(menusClean)}' title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                </td>
            </tr>`;
        $('#aksesMenuTableBody').append(html);
    }

    function initSelect2(parentId) {
        let dropdownParent = parentId ? $(parentId) : $('body');
        $('.namaUser, .aksesCabang, .cabangSelect, .select2-searchable').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });
        $('.select2-user').select2({ dropdownParent: dropdownParent.find('.select2-user').length ? dropdownParent : $('#formContainerCreate') });
        $('.select2-cabang').select2({ dropdownParent: dropdownParent.find('.select2-cabang').length ? dropdownParent : $('#formContainerCreate') });
        $(parentId + ' .select2-searchable').select2({
            width: '100%',
            placeholder: 'Pilih',
            allowClear: false,
            dropdownParent: dropdownParent
        });
    }

    $(document).ready(function () {
        fetchAksesMenu();

        const parentChildrenMap = {
            '#ManajemenDataParent': { childSelector: '.subMenuManajemenData', childContainer: '#child-ManajemenData' },
            '#PengaturanParent': { childSelector: '.subMenuPengaturan', childContainer: '#child-Pengaturan' },
            '#ManajemenDataParentEdit': { childSelector: '.subMenuManajemenDataEdit', childContainer: '#child-ManajemenDataEdit' },
            '#PengaturanParentEdit': { childSelector: '.subMenuPengaturanEdit', childContainer: '#child-PengaturanEdit' }
        };

        function updateChildVisibilityAndState(parentId) {
            const mapKey = parentId.replace('Create', '').replace('Edit', '');
            const map = parentChildrenMap[parentId] || parentChildrenMap[mapKey];
            if (!map) return;
            const { childSelector, childContainer } = map;
            const isChecked = $(parentId).is(':checked');

            if (isChecked) {
                $(childContainer).slideDown(200);
                $(childSelector).prop('disabled', false).closest('label').removeClass('disabled-menu');
            } else {
                $(childContainer).slideUp(200);
                if (!$(parentId).data('loading-edit')) {
                    $(childSelector).prop('checked', false);
                }
                $(childSelector).prop('disabled', true).closest('label').addClass('disabled-menu');
            }
        }

        function synchronizeMasterData(formType) {
            const masterDataFrontendId = formType === 'create' ? '#MasterDataFrontend' : '#MasterDataFrontendEdit';
            const manajemenDataParentId = formType === 'create' ? '#ManajemenDataParent' : '#ManajemenDataParentEdit';
            const masterDataFrontend = $(masterDataFrontendId);
            const manajemenDataParent = $(manajemenDataParentId);

            if (masterDataFrontend.is(':checked')) {
                manajemenDataParent.prop('checked', true).trigger('change', ['internal']);
            }
            if (manajemenDataParent.is(':checked')) {
                masterDataFrontend.prop('checked', true);
            } else if (!manajemenDataParent.is(':checked')) {
                const subMenuSelector = formType === 'create' ? '.subMenuManajemenData' : '.subMenuManajemenDataEdit';
                if ($(subMenuSelector + ':checked').length === 0) {
                    masterDataFrontend.prop('checked', false);
                }
            }
        }

        $('#ManajemenDataParent, #PengaturanParent, #ManajemenDataParentEdit, #PengaturanParentEdit').on('change', function (e, triggerType) {
            updateChildVisibilityAndState('#' + this.id);
            if (this.id.includes('ManajemenDataParent') && triggerType !== 'internal') {
                synchronizeMasterData(this.id.includes('Edit') ? 'edit' : 'create');
            }
        });

        $('#MasterDataFrontend, #MasterDataFrontendEdit').on('change', function () {
            synchronizeMasterData(this.id.includes('Edit') ? 'edit' : 'create');
        });

        $('.subMenuManajemenData, .subMenuManajemenDataEdit, .subMenuPengaturan, .subMenuPengaturanEdit').on('change', function () {
            const isEdit = $(this).attr('class').includes('Edit');
            const type = $(this).attr('class').includes('Manajemen') ? 'ManajemenData' : 'Pengaturan';
            const parentId = `#${type}Parent${isEdit ? 'Edit' : ''}`;
            const subMenuSelector = isEdit ? `.subMenu${type}Edit` : `.subMenu${type}`;
            $(parentId).prop('checked', $(subMenuSelector + ':checked').length > 0);
            if (type === 'ManajemenData') synchronizeMasterData(isEdit ? 'edit' : 'create');
            updateChildVisibilityAndState(parentId);
        });

        function resetForm(formType) {
            let formId = formType === 'create' ? '#aksesFormCreate' : '#aksesFormEdit';
            $(formId)[0]?.reset();
            $(formId).find('.select2-user, .aksesCabang, .select2-cabang').val('').trigger('change');
            $(formId).find('.aksesId').val('');
            $('.cabangSelectContainerCreate, .cabangSelectContainerEdit').hide();
            $('#cabangUserCreate, #roleUserCreate, #statusUserCreate, #cabangUserEdit, #roleUserEdit, #statusUserEdit').val('');
            $('.aksesMenu').prop('checked', false).prop('disabled', false).closest('label').removeClass('disabled-menu');
            $('#ManajemenDataParent, #PengaturanParent, #ManajemenDataParentEdit, #PengaturanParentEdit').data('loading-edit', false);
            $('#child-ManajemenData, #child-Pengaturan, #child-ManajemenDataEdit, #child-PengaturanEdit').hide();
        }

        $(document).on('click', '#toggleFormBtn', function (e) {
            e.preventDefault();
            const isCreateVisible = $('#formContainerCreate').is(':visible');
            resetForm('create'); resetForm('edit');
            if (isCreateVisible) {
                $('#formContainerCreate').slideUp();
            } else {
                $('#formContainerCreate').slideDown(() => initSelect2('#formContainerCreate'));
                $('#formContainerEdit').slideUp();
                $('html, body').animate({ scrollTop: $('#formContainerCreate').offset().top - 80 }, 300);
            }
        });

        $('#cancelCreateBtn').on('click', () => { resetForm('create'); $('#formContainerCreate').slideUp(); });
        $('#cancelEditBtn').on('click', () => { resetForm('edit'); $('#formContainerEdit').slideUp(); });

        $('#namaUserCreate, #namaUserEdit').on('change', function () {
            const isEdit = this.id.includes('Edit');
            const u = usersMap[$(this).val()] || null;
            const suffix = isEdit ? 'Edit' : 'Create';
            $(`#cabangUser${suffix}`).val(u ? capitalize(u.cabang || '-') : '');
            $(`#roleUser${suffix}`).val(u ? capitalize(u.role || '-') : '');
            $(`#statusUser${suffix}`).val(u ? capitalize(u.status || '-') : '');
            if (isEdit) $('#userIdHiddenEdit').val($(this).val());
        });

        $('#aksesCabangCreate, #aksesCabangEdit').on('change', function () {
            const isEdit = this.id.includes('Edit');
            const suffix = isEdit ? 'Edit' : 'Create';
            const container = `.cabangSelectContainer${suffix}`;
            if ($(this).val() === 'spesifik') $(container).slideDown(200);
            else { $(container).slideUp(200); $(`#cabangSelect${suffix}`).val('').trigger('change'); }
        });

        $(document).on('click', '.editBtn', function () {
            $('#formContainerCreate').slideUp();
            resetForm('edit');
            const d = $(this).data();
            $('#aksesIdEdit').val(d.id);
            $('#userIdHiddenEdit').val(d.userId);
            $('#namaUserEdit').val(d.userId).trigger('change');
            $('#aksesCabangEdit').val(d.aksesCabang).trigger('change');
            if (d.aksesCabang === 'spesifik') $('#cabangSelectEdit').val(d.cabangId).trigger('change');
            if (Array.isArray(d.aksesMenu)) {
                d.aksesMenu.forEach(v => { $('.aksesMenuEdit[value="' + v + '"]').prop('checked', true); });
            }
            $('#ManajemenDataParentEdit').prop('checked', $('.subMenuManajemenDataEdit:checked').length > 0 || d.aksesMenu.includes('Manajemen Data')).trigger('change');
            $('#PengaturanParentEdit').prop('checked', $('.subMenuPengaturanEdit:checked').length > 0 || d.aksesMenu.includes('Pengaturan')).trigger('change');
            $('#MasterDataFrontendEdit').prop('checked', d.aksesMenu.includes('Master Data'));
            updateChildVisibilityAndState('#ManajemenDataParentEdit');
            updateChildVisibilityAndState('#PengaturanParentEdit');
            initSelect2('#formContainerEdit');
            $('#formContainerEdit').slideDown();
            $('html, body').animate({ scrollTop: $('#formContainerEdit').offset().top - 80 }, 300);
        });

        $('#searchInput').on('input', function () {
            clearTimeout(searchTimer);
            const q = $(this).val();
            searchTimer = setTimeout(() => fetchAksesMenu(q, 1), 400);
        });
    });
</script>

@endsection