<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking {{ $vehicle->name }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2>Booking Vehicle: {{ $vehicle->name }}</h2>
            </div>
            <div class="card-body">
                {{-- Form action mengarah ke rute named 'bookings.store' --}}
                <form action="{{ route('bookings.store') }}" method="POST">
                    @csrf
                    
                    {{-- Input Hidden untuk ID Kendaraan --}}
                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

                    <div class="form-group">
                        <label>Customer Name</label>
                        <input type="text" name="customer_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Identity Card</label>
                        <input type="text" name="identity_card" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Confirm Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>