@extends('layouts.app')

@section('title', 'Menu Utama')

@section('content')
<div class="container">
<div id="page-menu" class="d-flex flex-column align-items-center gap-3">
    <div class="w-100 d-flex justify-content-center position-relative">
        <h2 class="h2 fw-bold text-dark mb-0">Menu</h2>
        <a href="#" class="btn btn-danger btn-sm shadow-sm position-absolute top-0 end-0 me-1" title="Logout" data-bs-toggle="modal" data-bs-target="#logoutModal">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>
    </div>

    @auth
        <div class="d-flex justify-content-center align-items-baseline gap-2">
            <p class="fs-5 mb-0">Selamat Datang,</p>
            <p class="h4 fw-bold mb-0">{{ Auth::user()->nama }}</p>
        </div>
    @endauth

    <div class="d-flex flex-column gap-3 w-100">
        <a href="{{ route('monitoring') }}" class="btn btn-dark py-3 fs-5">Monitoring</a>
        <button id="btn-to-input" class="btn btn-primary py-3 fs-5 text-nowrap">Tambah Data</button>
    </div>
</div>

<div class="modal fade" id="selection-popup" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Tanggal & Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="input-tanggal" class="form-label fw-bold">Tanggal</label>
                    <input type="date" class="form-control" id="input-tanggal">
                </div>
                <label class="form-label fw-bold">Shift</label>
                <div class="d-grid gap-2">
                    <button class="shift-btn btn btn-outline-primary py-2" data-shift="1">Shift 1</button>
                    <button class="shift-btn btn btn-outline-primary py-2" data-shift="2">Shift 2</button>
                    <button class="shift-btn btn btn-outline-primary py-2" data-shift="3">Shift 3</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Konfirmasi Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                Apakah Anda yakin ingin keluar dari akun?
            </div>
            <div class="modal-footer d-flex justify-content-center gap-3 border-0 pt-0">
                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Ya, Keluar</button>
                </form>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectionModal = new bootstrap.Modal(document.getElementById('selection-popup'));
        const dateInput = document.getElementById('input-tanggal');

        document.getElementById('btn-to-input').addEventListener('click', () => {
            dateInput.value = new Date().toISOString().slice(0, 10);
            selectionModal.show();
        });

        document.querySelectorAll('.shift-btn').forEach(btn => {
            btn.onclick = () => {
                const shift = btn.dataset.shift;
                const selectedDate = dateInput.value;

                if (!selectedDate) {
                    alert('Silakan pilih tanggal terlebih dahulu!');
                    return;
                }

                window.location.href = `{{ url('/job-order') }}/${selectedDate}/${shift}`;
            };
        });
    });
</script>
@endpush
