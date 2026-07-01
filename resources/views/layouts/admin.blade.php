<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - TALY HBT</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --wflo-blue:#154D71;
            --wflo-bg:#f4f7f6;
        }
        body {
            background-color:var(--wflo-bg);
            font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen,Ubuntu,Cantarell,'Open Sans','Helvetica Neue',sans-serif;
            display:flex;
            height:100vh;
            overflow:hidden;
        }
        .wflo-main {
            flex-grow:1;
            display:flex;
            flex-direction:column;
            overflow-y:auto;
        }
        .wflo-content-area {
            padding:1.5rem;
            flex-grow:1;
        }
        .wflo-footer {
            text-align:center;
            padding:0.5rem 1.5rem;
            color:#888;
            font-size:0.85rem;
        }
        .datepicker-dropdown {
            z-index:2000 !important;
        }
    </style>
</head>
<body>
    @include('partials.sidebar')

    <div class="wflo-main">
        <header style="background-color:#ffffff;padding:0.75rem 1.5rem;border-bottom:1px solid #dee2e6;display:flex;justify-content:space-between;align-items:center;box-shadow:0 0.15rem 1.75rem 0 rgba(58,59,69,0.05);">
            <div style="text-align:left;">
                <div id="currentDateTime" style="font-weight:600;color:#000;font-size:0.95rem;"></div>
                @if (Auth::user()->cabang)
                    <div style="font-size:0.85rem;color:#6c757d;">
                       Cabang {{ Auth::user()->cabang }}
                    </div>
                @endif
            </div>
            <div style="text-align:right;">
                <div style="font-weight:700;">
                    Selamat datang, {{ Auth::user()->nama ?? 'Pengguna' }}
                </div>
                <div style="font-size:0.85rem;color:#6c757d;">
                    {{ ucwords(Auth::user()->role ?? '-') }}
                    @if(!empty(Auth::user()->nip))
                        | {{ Auth::user()->nip }}
                    @endif
                </div>
            </div>
        </header>

        <main class="wflo-content-area">
            @yield('content')
        </main>

        <footer class="wflo-footer">&copy; TALLY HBT <span id="year"></span></footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @stack('scripts')

    <script>
        function updateDateTime() {
            const now = new Date();
            const options = { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit', second:'2-digit', hour12:false, timeZone:'Asia/Jakarta' };
            let formatted = now.toLocaleString('id-ID', options);
            formatted = formatted.replace('pukul ', '');
            document.getElementById('currentDateTime').textContent = formatted;
            document.getElementById('year').textContent = now.getFullYear();
        }
        updateDateTime();
        setInterval(updateDateTime,1000);
    </script>
</body>
</html>
