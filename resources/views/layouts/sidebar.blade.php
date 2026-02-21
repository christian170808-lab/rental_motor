<div class="sidebar" style="width: 250px; background: linear-gradient(180deg, #1e3a8a, #1e40af); height: 100vh; padding: 20px; position: fixed; top: 0; left: 0; overflow-y: auto; box-shadow: 2px 0 12px rgba(0,0,0,0.3);">
    
    {{-- LOGO + TITLE --}}
    <div class="d-flex align-items-center justify-content-center mb-2 gap-2">
        <h2 class="mb-0 title-underline" style="color: #ffffff; font-weight: 800; letter-spacing: 1px; font-size: 1.3rem;">RentalMotor</h2>
        <i class="fas fa-motorcycle" style="font-size: 1.8rem; color: #60a5fa;"></i>
    </div>
    <hr style="border-color: #4b5563; margin: 1.2rem 0;">

    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a class="nav-link d-flex align-items-center sidebar-link" href="{{ route('dashboard') }}">
                <i class="fas fa-gauge-high me-3" style="font-size: 1.1rem; width: 20px; text-align: center;"></i>
                Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex align-items-center sidebar-link" href="{{ route('vehicles.index') }}">
                <i class="fas fa-motorcycle me-3" style="font-size: 1.1rem; width: 20px; text-align: center;"></i>
                Vehicle Data
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex align-items-center sidebar-link" href="{{ route('booking.index') }}">
                <i class="fas fa-file-invoice me-3" style="font-size: 1.1rem; width: 20px; text-align: center;"></i>
                Orders
            </a>
        </li>
        <li class="nav-item mb-2">
            <a class="nav-link d-flex align-items-center sidebar-link" href="{{ route('customers.index') }}">
                <i class="fas fa-users me-3" style="font-size: 1.1rem; width: 20px; text-align: center;"></i>
                Customer Data
            </a>
        </li>
    </ul>

    <div style="position: absolute; bottom: 30px; left: 20px; right: 20px;">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-link sidebar-link logout-btn d-flex align-items-center" style="width: 100%; text-align: left; padding: 0.75rem 1rem; text-decoration: none;">
                <i class="fas fa-right-from-bracket me-3" style="font-size: 1.1rem; width: 20px; text-align: center;"></i> Logout
            </button>
        </form>
    </div>
</div>

<style>
    .sidebar-link {
        color: #d1d5db;
        font-size: 1.05rem;
        font-weight: 600;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .sidebar-link:hover {
        background-color: rgba(255,255,255,0.15);
        border-left: 4px solid #93c5fd;
        padding-left: 0.75rem;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    .sidebar-link.active {
        background-color: rgba(255,255,255,0.2);
        border-left: 4px solid #93c5fd;
        color: #ffffff;
    }

    .logout-btn {
        color: #fca5a5 !important;
        font-weight: 700;
    }

    .logout-btn:hover {
        background-color: rgba(220,38,38,0.2) !important;
        border-left: 4px solid #ef4444 !important;
        color: #fca5a5 !important;
    }

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
        background-color: #60a5fa;
        transform: translateX(-50%);
        border-radius: 2px;
    }
</style>