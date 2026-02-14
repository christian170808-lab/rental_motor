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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>Booking Vehicle: {{ $vehicle->name }}</h2>
                <a href="{{ url('/booking') }}" class="btn btn-secondary">Back to Home</a>
                </div>
            <div class="card-body">
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('bookings.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

                    <div class="form-group">
                        <label>Customer Name</label>
                        <input type="text" name="customer_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Upload ID Card Photo</label>
                        <input type="file" name="identity_card" class="form-control" accept="image/*" required>
                        <small class="text-muted">The ID card photo is used for rental data.</small>
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