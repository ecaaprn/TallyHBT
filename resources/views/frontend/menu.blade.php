@extends('layouts.app')

@section('title', 'Menu Utama')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<style>
    :root {
        --dark-blue: #154D71;
        --medium-blue: #1C6EA4;
        --light-blue: #33A1E0;
    }

    #page-menu {
        padding: 0 1rem;
    }

    #page-menu h2,
    #page-menu .welcome-text {
        color: #fff;
    }

    #page-menu .welcome-text {
        font-weight: 600;
    }

    .btn-menu {
        font-size: 1rem;
        padding: .7rem 1.6rem;
        font-weight: 500;
        border-radius: .6rem;
        background: var(--dark-blue);
        transition: .2s;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 52px;
        gap: 10px;
        white-space: nowrap;
        color: #fff;
        border: none;
    }

    .btn-menu i,
    .btn-menu span {
        color: #fff;
    }

    .btn-menu:hover {
        background: #245270;
        transform: translateY(-2px);
        color: #fff;
    }

    .btn-logout {
        width: 26px;
        height: 26px;
        padding: 0;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .75rem;
    }

    #selection-popup .modal-content {
        background: var(--light-blue);
    }

    #selection-popup .modal-title,
    #selection-popup .form-label {
        color: #fff;
        font-weight: bold;
    }

    #selection-popup .modal-header,
    #selection-popup .modal-footer {
        border: 0;
    }

    #continue-btn {
        background: var(--dark-blue);
        color: #fff;
    }

    #continue-btn:hover {
        background: #0d3a58;
    }
</style>
@endpush

@section('content')

@php
    $user = Auth::user();
    $aksesMenu = [];
    if ($user) {
        $data = \App\Models\AksesMenu::where('user_id', $user->id)->first();
        if ($data && $data->akses_menu) {
            $aksesMenu = is_string($data->akses_menu)
                ? json_decode($data->akses_menu, true)
                : $data->akses_menu;
            if (!is_array($aksesMenu)) $aksesMenu = [];
        }
    }
@endphp

<div id="page-menu" class="d-flex flex-column align-items-center w-100">
    <div class="w-100 d-flex justify-content-center position-relative mb-2">
        <h2 class="fw-bold mb-0 text-uppercase">Menu Utama</h2>
        <a class="btn btn-danger shadow-sm position-absolute top-0 end-0 btn-logout"
           data-bs-toggle="modal" data-bs-target="#logoutModal" title="Keluar">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>
    </div>

    <div class="d-flex justify-content-center align-items-center gap-2 mb-4">
        <p class="fs-6 mb-0 welcome-text">Selamat Datang,</p>
        <p class="h5 fw-bold mb-0 welcome-text">{{ Auth::user()->nama }}</p>
    </div>

    <div class="container" style="max-width:900px;">
        <div class="row g-3 justify-content-center text-center">
            @if(in_array('Monitoring', $aksesMenu))
                <div class="col-auto">
                    <a href="{{ route('monitoring') }}" class="btn btn-menu shadow">
                        <i class="fa-solid fa-desktop"></i>
                        <span>View Data</span>
                    </a>
                </div>
            @endif

            @if(in_array('Input Data', $aksesMenu))
                <div class="col-auto">
                    <button id="btn-to-input" class="btn btn-menu shadow">
                        <i class="fa-solid fa-keyboard"></i>
                        <span>Input Data</span>
                    </button>
                </div>
            @endif

            @if(in_array('Master Data', $aksesMenu))
                <div class="col-auto">
                    <a href="{{ route('dashboard') }}" class="btn btn-menu shadow">
                        <i class="fa-solid fa-database"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="selection-popup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Tanggal & Shift</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="text" class="form-control" id="input-tanggal" placeholder="Masukkan Tanggal" autocomplete="off">
                </div>
                <div class="mb-2">
                    <label class="form-label">Shift</label>
                    <select class="form-select" id="shift-select">
                        <option value="">Pilih Shift</option>
                        <option value="1">Shift 1</option>
                        <option value="2">Shift 2</option>
                        <option value="3">Shift 3</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn" id="continue-btn">Lanjutkan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#input-tanggal').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });

        const selectionModal = new bootstrap.Modal(document.getElementById('selection-popup'));
        const btnInput = document.getElementById('btn-to-input');
        
        if (btnInput) {
            btnInput.addEventListener('click', () => selectionModal.show());
        }

        document.getElementById('continue-btn').addEventListener('click', () => {
            const date = document.getElementById('input-tanggal').value;
            const shift = document.getElementById('shift-select').value;

            if (!date || !shift) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops',
                    text: 'Tanggal dan Shift harus dipilih!'
                });
                return;
            }

            const url = "{{ route('job-order.input', ['date' => 'DATE', 'shift' => 'SHIFT']) }}"
                .replace('DATE', date)
                .replace('SHIFT', shift);
                
            window.location.href = url;
        });
    });
</script>
@endpush