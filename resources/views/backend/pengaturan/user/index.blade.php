@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content')
<div class="card shadow rounded">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center" style="border-bottom: none !important;">
        <h4 class="mb-2 mb-md-0 fw-bold text-uppercase" style="color: #000;">DATA USER</h4>
        <div class="d-flex align-items-center gap-2">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari Nama, NIP, atau Email" style="border-radius: 8px; width: 300px;">
            <button id="toggleFormBtn" class="btn btn-primary-custom">
                <i class="fa-solid fa-square-plus me-2"></i>Tambah
            </button>
        </div>
    </div>

    <div class="card-body">
        <div id="formContainerWrapper">
            @include('backend.pengaturan.user.create')
            @include('backend.pengaturan.user.edit')
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-hover text-center" style="min-width: 1300px; font-size: .95rem;">
                <thead>
                    <tr>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">No</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Group</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Nama</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">NIP</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Username</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Email</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Role</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Cabang</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Status</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Dibuat</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px; width: 140px;">Diperbarui</th>
                        <th style="background-color: #0158a4; color: #fff; white-space: nowrap; padding: 12px 8px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <tr>
                        <td colspan="12" class="text-center text-muted py-3">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="paginationContainer" class="d-flex justify-content-center mt-4"></div>
    </div>
</div>

<style>
    .btn-primary-custom {
        border-radius: 8px;
        background-color: #0158a4;
        color: #fff;
        border: none;
        font-weight: 600;
        transition: all .2s ease;
    }
    .btn-primary-custom:hover {
        background-color: #003366;
        color: #fff;
        transform: scale(1.03);
    }
    .btn-primary-custom:active {
        transform: scale(0.97);
    }
    .editUserBtn {
        background: #ffc107;
        border: none;
        border-radius: 6px;
        padding: 5px 7px;
        transition: 0.2s;
    }
    .editUserBtn:hover {
        background: #e0a800;
    }
    .page-item .page-link {
        border: none;
        padding: .5rem .75rem;
        margin: 0 5px;
        color: #495057;
        background: transparent;
        font-weight: 600;
        border-radius: 8px;
    }
    .page-item .page-link:hover {
        color: #003366;
        background: #e9ecef;
    }
    .page-item.active .page-link {
        background: #0158a4 !important;
        color: #fff !important;
        pointer-events: none;
    }
    .select2-container--default .select2-selection--single {
        height: 42px;
        border-radius: 8px;
        border: 1px solid #ced4da;
        display: flex;
        align-items: center;
    }
    .select2-selection__rendered {
        line-height: 42px;
        font-size: 15px;
        color: #212529;
        padding-left: 15px !important;
    }
    .select2-selection__arrow {
        height: 42px;
    }
    .swal-error-text {
        color: #6c757d;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    const baseUrl = "{{ url('admin/pengaturan/user') }}";
    let editingId = null;
    let searchTimer = null;
    let currentSearch = '';

    const ui = {
        swalError: (msg) => {
            Swal.fire({
                position: "center",
                icon: "error",
                title: `<span class="swal-error-text">${msg}</span>`,
                showConfirmButton: false,
                timer: 2500
            });
        },
        formatDateTime: (v) => {
            if (!v) return '-';
            const d = new Date(typeof v === 'string' ? v.replace(" ", "T") : v);
            if (isNaN(d.getTime())) return '-';
            const pad = (n) => String(n).padStart(2, '0');
            return `${pad(d.getDate())}-${pad(d.getMonth() + 1)}-${d.getFullYear()} ${pad(d.getHours())}.${pad(d.getMinutes())}.${pad(d.getSeconds())}`;
        }
    };

    function renderPaginationNumeric(links) {
        const $container = $('#paginationContainer').empty();
        const filtered = links.filter(x => /^\d+$/.test(x.label) || x.active || x.label.includes('...'));
        if (filtered.length <= 3 && filtered.every(l => !l.url)) return;

        const $ul = $('<ul class="pagination mb-0"></ul>');
        filtered.forEach(link => {
            const $li = $('<li class="page-item"></li>');
            const $btn = $(`<button class="page-link" type="button">${link.label}</button>`);

            if (link.active) {
                $li.addClass('active');
                $btn.prop('disabled', true);
            } else if (link.url) {
                const page = new URL(link.url).searchParams.get('page');
                $btn.on('click', () => fetchUserData(currentSearch, page));
            } else {
                $li.addClass('disabled').prop('disabled', true);
            }
            $ul.append($li.append($btn));
        });
        $container.append($ul);
    }

    function fetchUserData(q = '', page = 1) {
        currentSearch = q;
        $.get(baseUrl, { search_user: q, page: page, ajax: 1 }, (res) => {
            const { data, links, from } = res.users;
            const $tb = $('#userTableBody').empty();

            if (!data.length) {
                $tb.append('<tr><td colspan="12" class="text-center text-muted py-3">Tidak ada data ditemukan</td></tr>');
                renderPaginationNumeric([]);
                return;
            }

            data.forEach((u, i) => {
                $tb.append(`
                    <tr>
                        <td class="text-nowrap">${from + i}</td>
                        <td class="text-center text-nowrap">${u.group ?? '-'}</td>
                        <td class="text-start text-nowrap">${u.nama}</td>
                        <td>${u.nip ?? '-'}</td>
                        <td>${u.username}</td>
                        <td class="text-start">${u.email}</td>
                        <td class="text-capitalize">${u.role}</td>
                        <td>${u.cabang ?? '-'}</td>
                        <td><span class="badge ${u.status === 'aktif' ? 'bg-success' : 'bg-danger'} text-capitalize">${u.status}</span></td>
                        <td class="text-nowrap">${ui.formatDateTime(u.created_at)}</td>
                        <td class="text-nowrap">${ui.formatDateTime(u.updated_at)}</td>
                        <td>
                            <button class="btn btn-sm editUserBtn" data-id="${u.id}" data-nama="${u.nama}" data-nip="${u.nip ?? ''}" data-username="${u.username}" data-email="${u.email}" data-group="${u.group ?? ''}" data-role="${u.role}" data-status="${u.status}" data-cabang="${u.cabang ?? ''}">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>`);
            });
            renderPaginationNumeric(links);
        }).fail(() => ui.swalError('Gagal memuat data user'));
    }

    $(document).ready(() => {
        fetchUserData();

        $('#toggleFormBtn').on('click', () => {
            $('#formContainerEdit').hide();
            const $create = $('#formContainerCreate');
            if (!$create.is(':visible')) {
                $create.slideDown(300);
                $('#userFormCreate')[0].reset();
                $('.select2-init-create, .select2-init-cabang-create').val('').trigger('change');
            } else {
                $create.slideUp(300);
            }
        });

        $(document).on('click', '.editUserBtn', function () {
            const d = $(this).data();
            $('#formContainerCreate').hide();
            editingId = d.id;

            $('#namaUserEdit').val(d.nama);
            $('#nipUserEdit').val(d.nip);
            $('#usernameUserEdit').val(d.username);
            $('#emailUserEdit').val(d.email);
            $('#groupUserEdit').val(d.group).trigger('change');
            $('#roleUserEdit').val(d.role).trigger('change');
            $('#statusUserEdit').val(d.status).trigger('change');
            $('#cabangUserEdit').val(d.cabang).trigger('change');
            
            $('#passwordUserEdit').val('').attr('type', 'password');
            $('.toggle-password-edit i').removeClass('fa-unlock text-secondary').addClass('fa-lock text-primary').css('color', '#0158a4');

            $('#formContainerEdit').slideDown(300, () => {
                $('html,body').animate({ scrollTop: $('#formContainerEdit').offset().top - 100 }, 400);
            });
        });

        $('#searchInput').on('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => fetchUserData($(this).val(), 1), 400);
        });
    });
</script>

@endsection