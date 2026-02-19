@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Vehicle List</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Plate</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicles as $v)
                    <tr>
                        <td>{{ $v->name }}</td>
                        <td>{{ $v->plate_number }}</td>
                        <td>
                            @if(strtolower($v->status) == 'available')
                                <span class="badge bg-success">Available</span>
                            @else
                                <span class="badge bg-danger">{{ ucfirst($v->status) }}</span>
                            @endif
                        </td>
                        <td>
                            @php 
                                $lastBooking = $v->bookings->last(); 
                            @endphp

                            @if($lastBooking && strtolower($v->status) == 'rented')
                                <a href="{{ route('booking.pdf', $lastBooking->id) }}" class="btn btn-danger btn-sm">
                                    <i class="fa fa-file-pdf"></i> PDF (ID: {{ $lastBooking->id }})
                                </a>
                            @else
                                <small class="text-muted">ID Motor: {{ $v->id }} | Tidak ada booking aktif</small>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- RETURN DATA --}}
    <h2 class="mb-3 mt-5">Return Data</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Booking ID</th>
                    <th>Motor</th>
                    <th>Return Date</th>
                    <th>Late (hari)</th>
                    <th>Penalty</th>
                    <th>Condition</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returns as $return)
                    <tr>
                        <td>{{ $return->booking_id }}</td>
                        <td>{{ optional($return->booking->vehicle)->name ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($return->return_date)->format('d-m-Y') }}</td>
                        <td>{{ $return->late_days }}</td>
                        <td>Rp {{ number_format($return->penalty, 0, ',', '.') }}</td>
                        <td>
                            @if($return->vehicle_condition == 'Good')
                                <span class="badge bg-success">Good</span>
                            @elseif($return->vehicle_condition == 'Minor Damage')
                                <span class="badge bg-warning text-dark">Minor Damage</span>
                            @else
                                <span class="badge bg-danger">Major Damage</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada data return.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection