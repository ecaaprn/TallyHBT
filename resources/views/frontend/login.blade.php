@extends('layouts.app')

@section('title', 'Login')

@push('styles')
<style>
:root{
    --dark-blue:#154D71;
    --medium-blue:#1C6EA4;
}
#page-login .btn-primary{
    background-color:var(--dark-blue);
    border-color:var(--dark-blue);
}
#page-login .btn-primary:hover{
    background-color:var(--medium-blue);
    border-color:var(--medium-blue);
}
.form-control:focus{
    border-color:var(--bs-border-color);
    box-shadow:none;
}
.form-control:-webkit-autofill,
.form-control:-webkit-autofill:hover,
.form-control:-webkit-autofill:focus,
.form-control:-webkit-autofill:active{
    -webkit-box-shadow:0 0 0 30px white inset!important;
    -webkit-text-fill-color:var(--bs-body-color)!important;
}
.login-subtitle{
    color:var(--dark-blue);
    font-weight:400;
}
.forgot-password{
    color:var(--dark-blue);
    font-weight:400;
    text-decoration:none;
    transition:color .2s ease-in-out;
}
.forgot-password:hover{
    color:var(--medium-blue);
}
.login-icon{
    width:70px;
    height:70px;
    background-color:#fff;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:0 auto 8px;
}
.login-icon i{
    font-size:32px;
    color:var(--dark-blue);
}
.input-group-text{
    background-color:#fff;
    border-right:0;
}
.input-group .form-control{
    border-left:0;
}
</style>
@endpush

@section('content')
<div class="container">
    <div id="page-login" class="d-flex flex-column gap-3 align-items-center w-100 mx-auto" style="max-width:400px;">
        <div class="text-center">
            <div class="login-icon">
                <i class="fa-solid fa-ship"></i>
            </div>
            <h2 class="display-6 fw-bold mb-1 text-white">TALLY HBT</h2>
            <p class="login-subtitle mb-0 text-white">Silakan masukkan username dan password</p>
        </div>

        @if(session('status'))
            <div class="alert alert-success w-100 text-center mt-3">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger w-100 text-center mt-3">
                @foreach($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="w-100 d-flex flex-column gap-3">
            @csrf

            <div class="input-group input-group-lg">
                <span class="input-group-text">
                    <i class="fa-solid fa-user text-secondary"></i>
                </span>
                <input type="text" name="username" placeholder="Username" class="form-control form-control-lg" required autofocus autocomplete="off">
            </div>

            <div class="input-group input-group-lg">
                <span class="input-group-text">
                    <i class="fa-solid fa-key text-secondary"></i>
                </span>
                <input type="password" name="password" id="password" placeholder="Password" class="form-control form-control-lg" required autocomplete="new-password">
                <button class="btn btn-light" type="button" id="toggle-password" style="border-left:0;border-color:var(--bs-border-color);">
                    <i class="fa-solid fa-lock text-secondary"></i>
                </button>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">LOGIN</button>

            <div class="text-center mt-1">
                <a href="{{ route('password.request') }}" class="forgot-password fs-6 text-white">
                    Lupa kata sandi?
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded',function(){
    const toggle=document.getElementById('toggle-password')
    const input=document.getElementById('password')
    const icon=toggle.querySelector('i')
    toggle.addEventListener('click',function(){
        const type=input.type==='password'?'text':'password'
        input.type=type
        icon.classList.toggle('fa-lock',type==='password')
        icon.classList.toggle('fa-lock-open',type==='text')
    })
})
</script>
@endpush
