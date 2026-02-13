<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kendaraan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Daftar Kendaraan</h2>

        {{-- Notifikasi Sukses --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Notifikasi Error --}}
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        <br>

        @if($vehicles->isEmpty())
            <p class="alert alert-warning">Data kendaraan tidak ditemukan.</p>
        @else
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Plate</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehicles as $v)
                        <tr>
                            <td class="text-center">
                                <img src="{{ asset('image/' . ($v->image ?? 'default.png')) }}" alt="{{ $v->name }}" style="max-width: 100px; height: auto;">
                            </td>
                            <td><strong>{{ $v->name }}</strong></td>
                            <td class="text-center">
                                <span class="badge badge-secondary">{{ $v->type }}</span>
                            </td>
                            <td><strong>{{ $v->plate_number }}</strong></td>
                            <td class="text-center">
                                @if($v->status == 'available')
                                    <span class="badge badge-success">Available</span>
                                @else
                                    <span class="badge badge-danger">Rented</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center">
                                    {{-- Tombol Check/Rent --}}
                                    <a href="{{ route('returns.create', $v->id) }}" class="btn btn-sm btn-info mr-2">Check</a>
                                    
                                    @if($v->status == 'available')
                                        <a href="{{ route('bookings.create', $v->id) }}" class="btn btn-sm btn-success mr-2">Rent</a>
                                    @else
                                        <button disabled class="btn btn-sm btn-secondary mr-2" style="cursor: not-allowed;">Unavailable</button>
                                    @endif

                                    {{-- Tombol Download PDF - Diperbarui --}}
                                    @if($v->status == 'rented')
                                        {{-- Cari booking yang sedang aktif untuk kendaraan ini --}}
                                        @php
                                            // Asumsi: status 'rented' di kendaraan sama dengan status 'pending' di booking
                                            $activeBooking = \App\Models\Booking::where('vehicle_id', $v->id)
                                                                ->where('payment_status', 'pending') 
                                                                ->latest()
                                                                ->first();
                                        @endphp

                                        @if($activeBooking)
                                            <a href="{{ route('booking.pdf', $activeBooking->id) }}" class="btn btn-sm btn-danger">Download PDF</a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>