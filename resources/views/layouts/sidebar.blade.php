{{-- HAMBURGER BUTTON (mobile & tablet only) --}}
<button class="hamburger-btn d-lg-none" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

{{-- OVERLAY --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- SIDEBAR --}}
<div class="sidebar" id="sidebar">

    {{-- LOGO --}}
    <div class="sidebar-logo">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-motorcycle logo-icon"></i>
            <span class="logo-text">RentalMotor</span>
        </div>
        <button class="sidebar-close d-lg-none" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="sidebar-divider"></div>

    {{-- NAV --}}
    <ul class="nav flex-column gap-1">
        <li class="nav-item">
            <a class="nav-link sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               href="{{ route('dashboard') }}">
                <i class="fas fa-gauge-high"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link sidebar-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}"
               href="{{ route('vehicles.index') }}">
                <i class="fas fa-motorcycle"></i>
                <span>Vehicle Data</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link sidebar-link {{ request()->routeIs('booking.*') ? 'active' : '' }}"
               href="{{ route('booking.index') }}">
                <i class="fas fa-file-invoice"></i>
                <span>Rent</span>
            </a>
        </li>

        {{-- CUSTOMERS DROPDOWN --}}
        <li class="nav-item">
            <button class="sidebar-link dropdown-toggle-btn w-100
                {{ request()->routeIs('customers.*') || request()->routeIs('admin.*') ? 'active' : '' }}"
                id="customersDropdown"
                onclick="toggleDropdown('customersMenu', this)">
                <i class="fas fa-users"></i>
                <span>Customers</span>
                <i class="fas fa-chevron-down chevron-icon ms-auto"></i>
            </button>
            <ul class="submenu {{ request()->routeIs('customers.*') || request()->routeIs('admin.*') ? 'open' : '' }}"
                id="customersMenu">
                <li>
                    <a class="sidebar-sublink {{ request()->routeIs('customers.*') ? 'active' : '' }}"
                       href="{{ route('customers.index') }}">
                        <i class="fas fa-user"></i>
                        <span>Customer List</span>
                    </a>
                </li>
                <li>
                    <a class="sidebar-sublink {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                       href="{{ route('admin.index') }}">
                        <i class="fas fa-user-shield"></i>
                        <span>Admin List</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
               href="{{ route('reports.index') }}">
                <i class="fas fa-file-alt"></i>
                <span>Report</span>
            </a>
        </li>
    </ul>

    {{-- LOGOUT --}}
    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="sidebar-link logout-btn w-100">
                <i class="fas fa-right-from-bracket"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</div>

<style>
/* ══════════════════════════════
   SIDEBAR BASE
══════════════════════════════ */
.sidebar {
    width: 250px;
    background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
    height: 100vh;
    padding: 20px 14px;
    position: fixed;
    top: 0; left: 0;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    box-shadow: 4px 0 20px rgba(0,0,0,0.25);
    z-index: 1040;
    transition: transform 0.3s ease;
}

/* ── LOGO ── */
.sidebar-logo {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 4px 6px 8px;
}
.logo-icon {
    font-size: 1.6rem;
    color: #60a5fa;
}
.logo-text {
    font-size: 1.15rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: 0.5px;
}

/* ── DIVIDER ── */
.sidebar-divider {
    height: 1px;
    background: rgba(255,255,255,0.12);
    margin: 8px 0 14px;
}

/* ── NAV LINK ── */
.sidebar-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    border-radius: 10px;
    color: rgba(255,255,255,0.7);
    font-size: 0.92rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    background: none;
    border-left: 3px solid transparent;
    transition: all 0.2s ease;
    cursor: pointer;
    width: 100%;
    text-align: left;
}

.sidebar-link i:first-child {
    width: 18px;
    text-align: center;
    font-size: 0.95rem;
    flex-shrink: 0;
}

.sidebar-link:hover {
    background: rgba(255,255,255,0.1);
    color: #fff;
    border-left-color: #60a5fa;
}

.sidebar-link.active {
    background: rgba(255,255,255,0.15);
    color: #fff;
    border-left-color: #93c5fd;
}

/* ── DROPDOWN ── */
.chevron-icon {
    font-size: 10px;
    transition: transform 0.25s ease;
    opacity: 0.7;
}
.dropdown-toggle-btn.open .chevron-icon {
    transform: rotate(180deg);
}

.submenu {
    list-style: none;
    padding: 4px 0 2px 14px;
    margin: 2px 0 0;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}
.submenu.open {
    max-height: 200px;
}

.sidebar-sublink {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border-radius: 8px;
    color: rgba(147,197,253,0.85);
    font-size: 0.87rem;
    font-weight: 500;
    text-decoration: none;
    border-left: 2px solid transparent;
    transition: all 0.2s ease;
    margin-bottom: 2px;
}
.sidebar-sublink i {
    width: 16px;
    text-align: center;
    font-size: 0.85rem;
    flex-shrink: 0;
}
.sidebar-sublink:hover,
.sidebar-sublink.active {
    background: rgba(255,255,255,0.1);
    color: #fff;
    border-left-color: #60a5fa;
}

/* ── FOOTER / LOGOUT ── */
.sidebar-footer {
    margin-top: auto;
    padding-top: 14px;
    border-top: 1px solid rgba(255,255,255,0.1);
}
.logout-btn {
    color: #fca5a5 !important;
}
.logout-btn:hover {
    background: rgba(220,38,38,0.2) !important;
    border-left-color: #ef4444 !important;
    color: #fca5a5 !important;
}

/* ── HAMBURGER ── */
.hamburger-btn {
    position: fixed;
    top: 14px; left: 14px;
    z-index: 1050;
    background: #1e3a8a;
    border: none; color: white;
    width: 42px; height: 42px;
    border-radius: 10px;
    font-size: 1.1rem; cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    display: flex; align-items: center; justify-content: center;
    transition: background 0.2s;
}
.hamburger-btn:hover { background: #1d4ed8; }

/* ── CLOSE ── */
.sidebar-close {
    background: rgba(255,255,255,0.1);
    border: none; color: white;
    width: 30px; height: 30px;
    border-radius: 8px; font-size: 0.9rem;
    cursor: pointer; display: flex;
    align-items: center; justify-content: center;
    flex-shrink: 0; transition: background 0.2s;
}
.sidebar-close:hover { background: rgba(255,255,255,0.2); }

/* ── OVERLAY ── */
.sidebar-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.5); z-index: 1030;
    backdrop-filter: blur(2px);
}
.sidebar-overlay.active { display: block; }

/* ── RESPONSIVE ── */
@media (max-width: 991px) {
    .sidebar { transform: translateX(-100%); }
    .sidebar.open { transform: translateX(0); }
    .main-content { margin-left: 0 !important; padding-top: 65px !important; }
}
@media (min-width: 992px) {
    .sidebar { transform: translateX(0) !important; }
    .main-content { margin-left: 250px !important; }
}
</style>

<script>
    const sidebar   = document.getElementById('sidebar');
    const overlay   = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    const closeBtn  = document.getElementById('sidebarClose');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function toggleDropdown(menuId, btn) {
        const menu = document.getElementById(menuId);
        menu.classList.toggle('open');
        btn.classList.toggle('open', menu.classList.contains('open'));
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.submenu.open').forEach(menu => {
            const btn = document.getElementById(menu.id.replace('Menu', 'Dropdown'));
            if (btn) btn.classList.add('open');
        });
    });

    if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
    if (closeBtn)  closeBtn.addEventListener('click', closeSidebar);
    if (overlay)   overlay.addEventListener('click', closeSidebar);

    sidebar.querySelectorAll('a.sidebar-link, a.sidebar-sublink').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) closeSidebar();
        });
    });
</script>