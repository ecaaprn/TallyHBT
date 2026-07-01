@extends('layouts.app')

@section('title', 'Lupa Kata Sandi')

@push('styles')
<style>
    :root {
        --dark-blue: #154D71;
        --medium-blue: #1C6EA4;
    }
    #page-forgot .btn-primary {
        background-color: var(--dark-blue);
        border-color: var(--dark-blue);
    }
    #page-forgot .btn-primary:hover {
        background-color: var(--medium-blue);
        border-color: var(--medium-blue);
    }
    .form-control {
        height: 48px;
        font-size: 15px;
        border-radius: 8px;
        padding-left: 15px;
    }
    .form-control:focus {
        border-color: var(--bs-border-color);
        box-shadow: none;
    }
    #page-forgot h2 {
        margin-top: 10px;
        margin-bottom: 0;
    }
    #page-forgot p {
        margin-top: 5px;
        margin-bottom: 5px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div id="page-forgot" class="d-flex flex-column align-items-center w-100 mx-auto" style="max-width:400px;">

        <h2 class="display-6 fw-bold text-white text-center">TALLY HBT</h2>

        <p class="fw-bold text-dark">Masukkan email untuk mengatur ulang sandi</p>

        @if (session('status'))
            <div class="alert alert-success text-center w-100">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger text-center w-100">Email tidak ditemukan atau gagal dikirim.</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="w-100 d-flex flex-column gap-2">
            @csrf
            <input type="email" name="email" placeholder="masukkan email anda" class="form-control" required autofocus>
            <button type="submit" class="btn btn-primary btn-lg w-100 py-2">Kirim</button>

            <a href="{{ route('login') }}" class="btn btn-light w-100 py-2 mt-1" style="border-radius:8px;">
                Kembali
            </a>
        </form>

    </div>
</div>
@endsection
