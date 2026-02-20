
<div class="sidebar" style="width: 250px; background-color: #f8f9fa; height: 100vh; padding: 20px; position: fixed; top: 0; left: 0; overflow-y: auto; box-shadow: 2px 0 8px rgba(0,0,0,0.08);">
    <h2 class="text-center mb-4 title-underline" style="color: black; font-weight: 800; letter-spacing: 1px;">RentalMotor</h2>
    <hr style="border-color: #dee2e6; margin: 1.5rem 0;">

    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a class="nav-link d-flex align-items-center sidebar-link" href="{{ route('dashboard') }}">
                <i class="fas fa-tachometer-alt me-3" style="font-size: 1.2rem;"></i>
                Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex align-items-center sidebar-link" href="{{ route('vehicles.index') }}">
                <i class="fas fa-motorcycle me-3" style="font-size: 1.2rem;"></i>
                Data Motor
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex align-items-center sidebar-link" href="{{ route('booking.index') }}">
                <i class="fas fa-calendar-check me-3" style="font-size: 1.2rem;"></i>
                Pesanan
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex align-items-center sidebar-link" href="{{ route('customers.index') }}">
                <i class="fas fa-users me-3" style="font-size: 1.2rem;"></i>
                Data Customer
            </a>
        </li>
    </ul>

    <!-- Logout di bawah (tetap merah agar kontras dan jelas sebagai aksi keluar) -->
    <div style="position: absolute; bottom: 30px; left: 20px; right: 20px;">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-link sidebar-link logout-btn" style="width: 100%; text-align: left; padding: 0.75rem 1rem; color: #cc0000; font-weight: bold; font-size: 1.1rem; text-decoration: none;">
                <i class="fas fa-sign-out-alt me-3"></i> Logout
            </button>
        </form>
    </div>
</div>

<style>
    .sidebar-link {
        color: #111827;               
        font-size: 1.125rem;          
        font-weight: 700;              
        padding: 0.75rem 1rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .sidebar-link:hover {
        background-color: #e5e7eb;     /* abu muda saat hover */
        border-left: 5px solid #111827; /* border kiri tebal hitam */
        padding-left: 0.75rem;
        color: #000000;                /* lebih hitam saat hover */
        box-shadow: 0 2px 8px rgba(0,0,0,0.10);
    }

    .logout-btn {
        color: #cc0000 !important;
        font-weight: 700;
    }

    .logout-btn:hover {
        background-color: #fee2e2;
        border-left: 5px solid #991b1b;
        color: #991b1b !important;
    }

    /* Garis bawah untuk judul RentalMotor */
    .title-underline {
        position: relative;
        display: inline-block;
        padding-bottom: 0.4rem;
    }

    .title-underline::after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: 0;
        width: 60%;
        height: 3px;
        background-color: #111827;     /* hitam pekat */
        transform: translateX(-50%);
        border-radius: 2px;
    }

    /* Optional: active state (untuk halaman yang sedang aktif) */
    .sidebar-link.active {
        background-color: #e5e7eb;
        border-left: 5px solid #111827;
        color: #000000;
    }
</style>