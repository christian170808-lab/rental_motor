<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Customer - RentalMotor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { margin: 0; font-family: sans-serif; }
        .sidebar { width: 250px; background-color: #f8f9fa; height: 100vh; padding: 20px; position: fixed; border-right: 1px solid #ddd; }
        .main-content { margin-left: 250px; padding: 20px; }
        .nav-link { color: purple; font-size: 18px; text-decoration: none; display: block; padding: 10px 0; }
        .nav-link:hover { color: #800080; }
        .table-container { 
            background-color: white;
            border: 1px solid #ddd; 
            border-radius: 10px; 
            padding: 20px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.05); 
        }
        th { background-color: #f8f9fa; color: black; font-weight: bold; }
        .btn-outline-primary { color: #007bff; border-color: #007bff; }
        .btn-outline-danger { color: #dc3545; border-color: #dc3545; }
    </style>
</head>
<body>

<div class="sidebar">
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
                <i class="fas fa-arrow-right-from-bracket"></i> Logout
            </button>
        </form>
    </div>
</div>

<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4" style="font-weight: bold;">Data Customer</h2>
        
        <div class="table-container">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Nama Customer</th>
                        <th>ID</th>
                        <th>Email</th>
                        <th>No.Telp</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->code }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-pencil-alt"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash-alt"></i> Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data customer.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>