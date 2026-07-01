@extends('layouts.app')

@section('title', 'Reset Kata Sandi')

@push('styles')
<style>
    :root {
        --dark-blue: #154D71;
        --medium-blue: #1C6EA4;
    }

    #page-reset .btn-primary {
        background-color: var(--dark-blue);
        border-color: var(--dark-blue);
    }

    #page-reset .btn-primary:hover {
        background-color: var(--medium-blue);
        border-color: var(--medium-blue);
    }

    .form-control:focus {
        border-color: var(--bs-border-color);
        box-shadow: none;
    }

    .password-wrapper {
        position: relative;
        width: 100%;
    }

    .password-toggle {
        position: absolute;
        top: 50%;
        right: 18px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        font-size: 18px;
    }

    .form-control::placeholder {
        font-size: 14px;
        color: #6c757d;
    }

    #password-rules, 
    #password-rules li {
        color: #000;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div id="page-reset" class="d-flex flex-column gap-3 align-items-center w-100 mx-auto" style="max-width:900px;">
        <div class="text-center mb-2">
            <h2 class="display-6 fw-bold mb-1 text-white">TALLY HBT</h2>
            <p class="fw-bold text-dark mb-0">Masukkan kata sandi baru Anda</p>
        </div>

        @if(session('status'))
            <div class="alert alert-success w-100 text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="w-100 d-flex flex-column gap-3">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <input type="text" value="{{ $user->username }}" readonly class="form-control form-control-lg w-100" style="background-color:#e9ecef; cursor:not-allowed;">
            
            <div>
                <p class="mb-1">Informasi Kata Sandi :</p>
                <ul class="small mb-2 ps-3" id="password-rules">
                    <li id="rule-length">Minimal 8 karakter</li>
                    <li id="rule-upper">Mengandung huruf besar (A–Z)</li>
                    <li id="rule-lower">Mengandung huruf kecil (a–z)</li>
                    <li id="rule-number">Mengandung angka (0–9)</li>
                </ul>
            </div>

            <div class="password-wrapper">
                <input type="password" name="password" id="password" placeholder="Masukkan kata sandi baru" class="form-control form-control-lg w-100" autocomplete="new-password" required>
                <i class="fa-solid fa-lock password-toggle" onclick="togglePassword('password', this)"></i>
            </div>

            <div class="password-wrapper mt-2">
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Konfirmasi kata sandi baru" class="form-control form-control-lg w-100" autocomplete="new-password" required>
                <i class="fa-solid fa-lock password-toggle" onclick="togglePassword('password_confirmation', this)"></i>
            </div>

            <div class="d-flex gap-3 mt-2 w-100">
                <button type="submit" class="btn btn-primary btn-lg w-50">Simpan</button>
                <a href="{{ route('login') }}" class="btn btn-light btn-lg w-50" style="border:1px solid #ccc;">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePassword(id, icon) {
        const input = document.getElementById(id);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-lock', 'fa-lock-open');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-lock-open', 'fa-lock');
        }
    }

    const passwordInput = document.getElementById('password');

    passwordInput.addEventListener('input', function() {
        const v = this.value;
        toggleRule('rule-length', v.length >= 8);
        toggleRule('rule-upper', /[A-Z]/.test(v));
        toggleRule('rule-lower', /[a-z]/.test(v));
        toggleRule('rule-number', /[0-9]/.test(v));
    });

    function toggleRule(id, valid) {
        const el = document.getElementById(id);
        if (el) {
            el.style.fontWeight = valid ? '700' : '400';
        }
    }
</script>
@endsection