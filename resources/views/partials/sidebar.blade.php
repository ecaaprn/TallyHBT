<ul class="navbar-nav sidebar sidebar-dark" id="accordionSidebar">
    <div class="sidebar-brand d-flex flex-column align-items-center justify-content-center" style="padding: 0.75rem 0.75rem 0.25rem 0.75rem; min-height: 100px; position: relative;">
        <button id="sidebarToggle" class="sidebar-toggle-btn" style="background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer; padding: 0.5rem; transition: transform 0.3s; position: absolute; top: 0.5rem; right: 0.25rem; z-index: 10;">
            <i class="fas fa-bars"></i>
        </button>
        <img src="{{ asset('images/logo.png') }}" alt="Logo" id="logoToggle" class="sidebar-logo" style="background-color:transparent; width:75px; height:auto; transition: all 0.3s; cursor: pointer; display: block; margin-top: 0.5rem; margin-bottom: 0.5rem;">
        <div class="sidebar-brand-text" style="font-size:24px; font-weight:700; transition: all 0.3s; line-height: 1.2; text-align: center; color: #fff;">
            TALLY HBT
        </div>
    </div>

    <hr style="border-top:2px solid #808080; margin: 0.25rem 0.75rem;">

    @php
        $user = Auth::user();
        $aksesMenu = [];
        if ($user) {
            $aksesData = \App\Models\AksesMenu::where('user_id', $user->id)->first();
            if ($aksesData && $aksesData->akses_menu) {
                $raw = $aksesData->akses_menu;
                $aksesMenu = is_string($raw) ? json_decode($raw, true) : $raw;
                if (!is_array($aksesMenu)) $aksesMenu = [];
            }
        }
        $aksesNormalized = array_map(fn($v) => strtolower(trim($v)), $aksesMenu);
    @endphp

    @if(in_array('beranda', $aksesNormalized))
    <li class="nav-item mb-1">
        <a class="nav-link d-flex align-items-center px-3 py-2 rounded {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" title="Beranda">
            <i class="fas fa-fw fa-home me-2 nav-icon" style="color: #4CAF50;"></i>
            <span>Beranda</span>
        </a>
    </li>
    @endif

    @if(in_array('monitoring data', $aksesNormalized))
    <li class="nav-item mb-1">
        <a class="nav-link d-flex align-items-center px-3 py-2 rounded {{ request()->routeIs('monitoring-data') ? 'active' : '' }}" href="{{ route('monitoring-data') }}" title="Monitoring Data">
            <i class="fas fa-fw fa-chart-line me-2 nav-icon" style="color: #2196F3;"></i>
            <span>Monitoring Data</span>
        </a>
    </li>
    @endif

    @if(in_array('manajemen data', $aksesNormalized))
    <li class="nav-item mb-1">
        <a class="nav-link collapsed d-flex align-items-center px-3 py-2 rounded" href="#" data-bs-toggle="collapse" data-bs-target="#collapseManajemen" title="Master Data">
            <i class="fas fa-fw fa-database me-2 nav-icon" style="color: #FF9800;"></i>
            <span>Master Data</span>
            <i class="fas fa-chevron-down ms-auto collapse-arrow" style="font-size: 0.75rem; transition: transform 0.3s;"></i>
        </a>
        <div id="collapseManajemen" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                @if(in_array('booster', $aksesNormalized))
                <a class="collapse-item {{ request()->routeIs('master-booster.index') ? 'active' : '' }}" href="{{ route('master-booster.index') }}" title="Booster">
                    <i class="fas fa-fw fa-bolt" style="color: #E91E63; margin-right: 0.5rem;"></i> <span class="collapse-item-text">Booster</span>
                </a>
                @endif

                @if(in_array('cabang', $aksesNormalized))
                <a class="collapse-item {{ request()->routeIs('master-cabang.index') ? 'active' : '' }}" href="{{ route('master-cabang.index') }}" title="Cabang">
                    <i class="fas fa-fw fa-code-branch" style="color: #9C27B0; margin-right: 0.5rem;"></i> <span class="collapse-item-text">Cabang</span>
                </a>
                @endif

                @if(in_array('hose', $aksesNormalized))
                <a class="collapse-item {{ request()->routeIs('master-hose.index') ? 'active' : '' }}" href="{{ route('master-hose.index') }}" title="Hose">
                    <i class="fas fa-fw fa-water" style="color: #00BCD4; margin-right: 0.5rem;"></i> <span class="collapse-item-text">Hose</span>
                </a>
                @endif

                @if(in_array('kapal', $aksesNormalized))
                <a class="collapse-item {{ request()->routeIs('master-kapal.index') ? 'active' : '' }}" href="{{ route('master-kapal.index') }}" title="Kapal">
                    <i class="fas fa-fw fa-ship" style="color: #3F51B5; margin-right: 0.5rem;"></i> <span class="collapse-item-text">Kapal</span>
                </a>
                @endif

                @if(in_array('palka', $aksesNormalized))
                <a class="collapse-item {{ request()->routeIs('master-palka.index') ? 'active' : '' }}" href="{{ route('master-palka.index') }}" title="Palka">
                    <i class="fas fa-fw fa-box-open" style="color: #795548; margin-right: 0.5rem;"></i> <span class="collapse-item-text">Palka</span>
                </a>
                @endif

                @if(in_array('truck', $aksesNormalized))
                <a class="collapse-item {{ request()->routeIs('master-truck.index') ? 'active' : '' }}" href="{{ route('master-truck.index') }}" title="Truck">
                    <i class="fas fa-fw fa-truck" style="color: #F44336; margin-right: 0.5rem;"></i> <span class="collapse-item-text">Truck</span>
                </a>
                @endif
            </div>
        </div>
    </li>
    @endif

    @if(in_array('pengaturan', $aksesNormalized))
    <li class="nav-item mb-1">
        <a class="nav-link collapsed d-flex align-items-center px-3 py-2 rounded" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePengaturan" title="Pengaturan">
            <i class="fas fa-fw fa-cog me-2 nav-icon" style="color: #607D8B;"></i>
            <span>Pengaturan</span>
            <i class="fas fa-chevron-down ms-auto collapse-arrow" style="font-size: 0.75rem; transition: transform 0.3s;"></i>
        </a>
        <div id="collapsePengaturan" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
                @if(in_array('role user', $aksesNormalized))
                <a class="collapse-item {{ request()->routeIs('pengaturan-user.index') ? 'active' : '' }}" href="{{ route('pengaturan-user.index') }}" title="Role User">
                    <i class="fas fa-fw fa-users-cog" style="color: #E91E63; margin-right: 0.5rem;"></i> <span class="collapse-item-text">Role User</span>
                </a>
                @endif

                @if(in_array('akses menu', $aksesNormalized))
                <a class="collapse-item {{ request()->routeIs('pengaturan-akses.index') ? 'active' : '' }}" href="{{ route('pengaturan-akses.index') }}" title="Akses Menu">
                    <i class="fas fa-fw fa-list" style="color: #009688; margin-right: 0.5rem;"></i> <span class="collapse-item-text">Akses Menu</span>
                </a>
                @endif
            </div>
        </div>
    </li>
    @endif

    <hr style="border-top:2px solid #808080; margin: 0.5rem 0.75rem;">

    <li class="nav-item">
        <a class="nav-link d-flex align-items-center px-3 py-2 rounded" href="#" id="btn-logout" title="Keluar">
            <i class="fas fa-fw fa-sign-out-alt me-2 nav-icon" style="color: #F44336;"></i>
            <span>Keluar</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </li>
</ul>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('accordionSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const logoToggle = document.getElementById('logoToggle');
    const wfloMain = document.querySelector('.wflo-main');
    const toggleIcon = toggleBtn?.querySelector('i');

    if (sidebar) sidebar.classList.remove('collapsed');
    if (wfloMain) wfloMain.classList.remove('collapsed');

    if (toggleBtn && sidebar && wfloMain) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('collapsed');
            wfloMain.classList.toggle('collapsed');

            if (toggleIcon) {
                if (sidebar.classList.contains('collapsed')) {
                    toggleIcon.classList.remove('fa-bars');
                    toggleIcon.classList.add('fa-times');
                } else {
                    toggleIcon.classList.remove('fa-times');
                    toggleIcon.classList.add('fa-bars');
                }
            }
        });
    }

    if (logoToggle && sidebar && wfloMain) {
        logoToggle.addEventListener('click', function(e) {
            if (sidebar.classList.contains('collapsed')) {
                e.preventDefault();
                e.stopPropagation();
                sidebar.classList.remove('collapsed');
                wfloMain.classList.remove('collapsed');
                if (toggleIcon) {
                    toggleIcon.classList.remove('fa-times');
                    toggleIcon.classList.add('fa-bars');
                }
            }
        });
    }

    const collapseElements = document.querySelectorAll('[data-bs-toggle="collapse"]');
    collapseElements.forEach(element => {
        element.addEventListener('click', function(e) {
            const target = this.getAttribute('data-bs-target');
            const collapseElement = document.querySelector(target);
            const arrow = this.querySelector('.collapse-arrow');

            if (collapseElement) {
                const isCurrentlyOpen = collapseElement.classList.contains('show');

                document.querySelectorAll('.collapse.show').forEach(collapse => {
                    if (collapse !== collapseElement) {
                        collapse.classList.remove('show');
                        const otherArrow = collapse.previousElementSibling?.querySelector('.collapse-arrow');
                        if (otherArrow) {
                            otherArrow.style.transform = 'rotate(0deg)';
                        }
                    }
                });

                if (isCurrentlyOpen) {
                    collapseElement.classList.remove('show');
                    if (arrow) {
                        arrow.style.transform = 'rotate(0deg)';
                    }
                } else {
                    collapseElement.classList.add('show');
                    if (arrow) {
                        arrow.style.transform = 'rotate(180deg)';
                    }
                }
            }
        });
    });

    const logoutBtn = document.getElementById('btn-logout');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Keluar',
                text: 'Apakah Anda yakin ingin keluar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        });
    }
});
</script>

<style>
#accordionSidebar {
    background-color: #003366;
    width: 220px!important;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    overflow-y: auto;
    overflow-x: hidden;
    transition: width 0.3s ease;
    z-index: 1000;
}
#accordionSidebar.collapsed {
    width: 80px!important;
}
.sidebar-brand {
    min-height: 100px;
    padding: 1rem 0.75rem!important;
}
#accordionSidebar.collapsed .sidebar-brand {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0.5rem 0.2rem 0.25rem 0.2rem !important;
    min-height: 80px;
}
.sidebar-toggle-btn {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-shrink: 0;
    z-index: 10;
}
.sidebar-toggle-btn:hover {
    transform: scale(1.1);
}
.sidebar-toggle-btn:active {
    transform: scale(0.95);
}
#accordionSidebar.collapsed .sidebar-toggle-btn {
    position: absolute !important;
    top: 0.5rem !important;
    right: 0.25rem !important;
    font-size: 0.9rem !important;
}
.sidebar-logo {
    transition: all 0.3s ease;
    flex-shrink: 0;
}
#accordionSidebar.collapsed .sidebar-logo {
    width: 42px !important;
    margin: 0 auto !important;
    display: block !important;
}
.sidebar-title {
    width: 100%;
    justify-content: center;
    align-items: center;
}
#accordionSidebar.collapsed .sidebar-title {
    width: auto;
}
.sidebar-brand-text {
    transition: opacity 0.2s, visibility 0.2s;
    text-align: center;
    width: 100%;
}
#accordionSidebar.collapsed .sidebar-brand-text {
    display: none !important;
}
#accordionSidebar a span,
.collapse-item-text {
    transition: opacity 0.2s, visibility 0.2s;
    white-space: nowrap;
}
#accordionSidebar.collapsed a span,
#accordionSidebar.collapsed .collapse-item-text {
    opacity: 0;
    visibility: hidden;
    width: 0;
    overflow: hidden;
}
#accordionSidebar .nav-link {
    font-size: 17px;
    font-weight: 600;
    color: #adb5bd;
    transition: all 0.3s;
    position: relative;
    justify-content: flex-start;
}
#accordionSidebar.collapsed .nav-link {
    justify-content: center;
    padding: 0.75rem 0.5rem!important;
}
#accordionSidebar .nav-link i.nav-icon {
    font-size: 22px;
    min-width: 28px;
    text-align: center;
    transition: all 0.3s;
}
#accordionSidebar.collapsed .nav-link i.nav-icon {
    margin-right: 0!important;
}
#accordionSidebar .nav-link:hover,
#accordionSidebar .nav-link.active {
    background-color: rgba(255, 255, 255, 0.1);
    color: #ffffff;
}
#accordionSidebar .nav-link:hover i.nav-icon,
#accordionSidebar .nav-link.active i.nav-icon {
    transform: scale(1.1);
}
.collapse-arrow {
    transition: transform 0.3s ease;
}
#accordionSidebar.collapsed .collapse-arrow {
    display: none;
}
#accordionSidebar.collapsed .collapse {
    display: none;
}
#accordionSidebar.collapsed .collapse.show {
    display: block!important;
}
.collapse-inner {
    background-color: white!important;
    margin: 0 0.75rem;
    padding: 0.5rem;
    border-radius: 8px;
}
#accordionSidebar.collapsed .collapse-inner {
    margin: 0.25rem 0.5rem;
    padding: 0.5rem;
    background-color: rgba(255, 255, 255, 0.95)!important;
}
.collapse-inner a.collapse-item {
    display: flex;
    align-items: center;
    padding: 0.6rem 0.85rem;
    font-size: 15px;
    font-weight: 600;
    border-radius: 6px;
    text-decoration: none;
    color: #000!important;
    transition: all 0.3s;
    margin-bottom: 0.25rem;
}
.collapse-inner a.collapse-item:last-child {
    margin-bottom: 0;
}
#accordionSidebar.collapsed .collapse-inner a.collapse-item {
    justify-content: center;
    padding: 0.5rem!important;
}
.collapse-inner a.collapse-item:hover {
    background-color: #003366;
    color: #fff!important;
    transform: translateX(3px);
}
#accordionSidebar.collapsed .collapse-inner a.collapse-item:hover {
    transform: scale(1.1);
}
.collapse-inner a.collapse-item i {
    transition: all 0.3s;
    min-width: 22px;
    font-size: 18px;
}
#accordionSidebar.collapsed .collapse-inner a.collapse-item i {
    margin-right: 0!important;
}
.collapse-inner a.collapse-item:hover i {
    transform: scale(1.1);
}
.collapse-inner a.collapse-item.active {
    background-color: #003366;
    color: #fff!important;
}
.collapse-inner a.collapse-item.active i {
    color: #fff!important;
}
.wflo-main {
    margin-left: 220px;
    transition: margin-left 0.3s ease;
}
.wflo-main.collapsed {
    margin-left: 70px;
}
.swal2-container {
    z-index: 9999;
}
</style>
