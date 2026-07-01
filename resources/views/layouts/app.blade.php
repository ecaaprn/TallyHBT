<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tally HBT')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <style>
        body {
            background-color: #f0f2f5;
        }
        .container-main {
            background-color: #33A1E0;
            transition: all 0.3s ease-in-out;
            max-width: auto;
            width: auto;
            margin: 1.5rem auto;
            padding: clamp(1rem, 4vw, 2rem);
        }
        .table-container-scrollable {
            max-height: auto;
            overflow-y: auto;
        }
        .btn-dark-blue {
            background-color: #1C6EA4;
            border-color: #154D71;
            color: #fff;
        }
        .btn-dark-blue:hover {
            background-color: #154D71;
            border-color: #1C6EA4;
            color: #fff;
        }
        .bg-gray-readonly {
            background-color: #dee2e6 !important;
        }
        .btn-menu {
            width: 220px;
        }
        .modal {
            z-index: 1055;
        }
        .modal-backdrop {
            z-index: 1050;
        }
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
        .time-picker-btn:hover {
            color: #1C6EA4;
        }
    </style>

    @stack('styles')
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="container-main container-fluid rounded-4 shadow-lg p-4 p-lg-4 position-relative">
        @yield('content')

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
                            <button type="submit" class="btn btn-danger rounded-pill px-4">Ya</button>
                        </form>
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
