<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WFLO App')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<style>
body { background-color: #ffffff; }
.container-main {
    background-image: linear-gradient(to bottom right, #83c9f8, #ffffff);
    transition: all 0.3s ease-in-out;

    width: auto;
    max-width: auto;
    margin-left: auto;
    margin-right: auto;

    padding: auto;
}
.table-container-scrollable { max-height: auto; overflow-y: auto; }
.btn-dark-blue {
    background-color: #0d64d5; border-color: #0c5abf; color: #fff;
}
.btn-dark-blue:hover {
    background-color: #0b53b0; border-color: #0a4aa5; color: #fff;
}
.bg-gray-readonly { background-color: #dee2e6 !important; }
.btn-menu { width: 220px; }
.modal { z-index: 1055; }
.modal-backdrop { z-index: 1050; }
.badge-status {
    color: #fff;
    font-size: 0.7rem;
    padding: .35rem .6rem;
    border-radius: 6px;
    display: inline-block;
    min-width: 60px;
    text-align: center;
}
.separator-line {
    margin: 6px 0;
    border: 0;
    border-top: 1px solid #dee2e6;
}
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: .2em;
}
.time-picker-btn {
    cursor: pointer;
    color: #6c757d;
    transition: color 0.2s;
    background: none;
    border: none;
    padding: 0 .5rem;
    line-height: 1;
}
.time-picker-btn:hover { color: #0d6efd; }
</style>


</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 p-3">
    <div class="container-main container-fluid rounded-4 shadow-lg p-4 p-lg-4 position-relative">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
