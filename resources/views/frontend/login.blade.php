@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container">
<div id="page-login" class="d-flex flex-column gap-4 align-items-center w-100 mx-auto" style="max-width: 400px;">
    <h2 class="display-6 fw-bold">LOGIN</h2>

    @if ($errors->any())
        <div class="alert alert-danger w-100 text-center">
            Login gagal, silakan periksa kembali username dan password Anda.
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="w-100 d-flex flex-column gap-3">
        @csrf
        <input type="text" name="username" placeholder="Username" class="form-control form-control-lg" required autofocus value="{{ old('username') }}">

        <div class="input-group input-group-lg">
            <input type="password" name="password" id="password" placeholder="Password" class="form-control form-control-lg" required>
            <button class="btn btn-light" type="button" id="toggle-password" style="border-left: 0; border-color: var(--bs-border-color);">
                <i class="fa-solid fa-lock"></i>
            </button>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">Login</button>
    </form>
</div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        const lockIcon = togglePassword.querySelector('i');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            if (type === 'text') {
                lockIcon.classList.remove('fa-lock');
                lockIcon.classList.add('fa-lock-open');
            } else {
                lockIcon.classList.remove('fa-lock-open');
                lockIcon.classList.add('fa-lock');
            }
        });
    });
</script>
@endpush
