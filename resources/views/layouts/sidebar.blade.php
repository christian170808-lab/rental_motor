{{-- HAMBURGER BUTTON (mobile & tablet only) --}}
<button class="hamburger-btn d-lg-none" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

{{-- OVERLAY --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- SIDEBAR --}}
<div class="sidebar" id="sidebar">
    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
        <div class="d-flex align-items-center gap-2">
            <h2 class="mb-0" style="color:#fff; font-weight:800; font-size:1.3rem; letter-spacing:1px;">RentalMotor</h2>
            <i class="fas fa-motorcycle" style="font-size:1.8rem; color:#60a5fa;"></i>
        </div>
        <button class="sidebar-close d-lg-none" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <hr style="border-color:#4b5563; margin:1.2rem 0;">

    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a class="nav-link sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               href="{{ route('dashboard') }}">
                <i class="fas fa-gauge-high me-3"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link sidebar-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}"
               href="{{ route('vehicles.index') }}">
                <i class="fas fa-motorcycle me-3"></i> Vehicle Data
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link sidebar-link {{ request()->routeIs('booking.*') ? 'active' : '' }}"
               href="{{ route('booking.index') }}">
                <i class="fas fa-file-invoice me-3"></i> Rent
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}"
               href="{{ route('customers.index') }}">
                <i class="fas fa-users me-3"></i> Customers
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link sidebar-link {{ request()->routeIs('payments.*') ? 'active' : '' }}"
               href="{{ route('payments.index') }}">
                <i class="fas fa-credit-card me-3"></i> Payment
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
               href="{{ route('reports.index') }}">
                <i class="fas fa-file-alt me-3"></i> Report
            </a>
        </li>
    </ul>

    <div style="position:absolute; bottom:30px; left:20px; right:20px;">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-link sidebar-link logout-btn d-flex align-items-center w-100">
                <i class="fas fa-right-from-bracket me-3"></i> Logout
            </button>
        </form>
    </div>
</div>

<style>
/* ── SIDEBAR ── */
.sidebar {
    width: 250px;
    background: linear-gradient(180deg, #1e3a8a, #1e40af);
    height: 100vh;
    padding: 20px;
    position: fixed;
    top: 0; left: 0;
    overflow-y: auto;
    box-shadow: 2px 0 12px rgba(0,0,0,0.3);
    z-index: 1040;
    transition: transform 0.3s ease;
}

.sidebar-link {
    color: #d1d5db;
    font-size: 1rem;
    font-weight: 600;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    transition: all 0.2s ease;
    text-decoration: none;
    border-left: 4px solid transparent;
    display: block;
}

.sidebar-link:hover,
.sidebar-link.active {
    background: rgba(255,255,255,0.15);
    border-left-color: #93c5fd;
    color: #fff;
}

.logout-btn {
    color: #fca5a5 !important;
    font-weight: 700;
    background: none;
    border: none;
}

.logout-btn:hover {
    background: rgba(220,38,38,0.2) !important;
    border-left-color: #ef4444 !important;
    color: #fca5a5 !important;
}

/* ── HAMBURGER ── */
.hamburger-btn {
    position: fixed;
    top: 14px;
    left: 14px;
    z-index: 1050;
    background: #1e3a8a;
    border: none;
    color: white;
    width: 42px; height: 42px;
    border-radius: 10px;
    font-size: 1.1rem;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.hamburger-btn:hover { background: #1d4ed8; }

/* ── CLOSE BUTTON ── */
.sidebar-close {
    background: rgba(255,255,255,0.1);
    border: none;
    color: white;
    width: 32px; height: 32px;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background 0.2s;
}

.sidebar-close:hover { background: rgba(255,255,255,0.2); }

/* ── OVERLAY ── */
.sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1030;
    backdrop-filter: blur(2px);
}

.sidebar-overlay.active { display: block; }

/* ── MOBILE & TABLET (< 992px): sidebar hidden ── */
@media (max-width: 991px) {
    .sidebar {
        transform: translateX(-100%);
    }

    .sidebar.open {
        transform: translateX(0);
    }

    .main-content {
        margin-left: 0 !important;
        padding-top: 65px !important;
    }
}

/* ── DESKTOP (≥ 992px): sidebar always visible ── */
@media (min-width: 992px) {
    .sidebar {
        transform: translateX(0) !important;
    }

    .main-content {
        margin-left: 250px !important;
    }
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

    if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
    if (closeBtn)  closeBtn.addEventListener('click', closeSidebar);
    if (overlay)   overlay.addEventListener('click', closeSidebar);

    // Tutup sidebar otomatis saat klik menu link
    sidebar.querySelectorAll('.sidebar-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) closeSidebar();
        });
    });
</script>