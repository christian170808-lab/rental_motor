<div class="sidebar" style="width: 250px; background-color: #f8f9fa; height: 100vh; padding: 20px; position: fixed;">
    <h2 class="text-center" style="color: black;">RentalMotor</h2>
    <hr>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}" style="color: purple; font-size: 18px;">
                Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('vehicles.index') }}" style="color: purple; font-size: 18px;">
                Data Motor
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('booking.index') }}" style="color: purple; font-size: 18px;">
                Pesanan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('customers.index') }}" style="color: purple; font-size: 18px;">
                Data Costomer
            </a>
        </li>
    </ul>

    <div style="position: absolute; bottom: 20px; left: 20px; width: calc(100% - 40px);">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" style="width: 100%; text-align: left; background: none; border: none; color: #cc0000; cursor: pointer; font-weight: bold; font-size: 18px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</div>