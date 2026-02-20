@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            Return Vehicle: {{ $vehicle->name }}
        </div>

        <div class="card-body">

            <form action="{{ route('returns.store') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan motor ini?');">
                @csrf
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                <p><b>Customer:</b> {{ optional($booking->customer)->customer_name ?? 'N/A' }}</p>
                <p><b>Plate:</b> {{ $vehicle->plate_number }}</p>

                <div class="mb-3">
                    <label class="form-label">Vehicle Condition</label>
                    <select name="vehicle_condition" class="form-control">
                        <option value="Good">Good</option>
                        <option value="Minor Damage">Minor Damage</option>
                        <option value="Major Damage">Major Damage</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Process Return</button>
            </form>

        </div>
    </div>
</div>
@endsection