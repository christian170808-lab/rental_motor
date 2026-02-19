@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4>Booking Vehicle: {{ $vehicle->name }}</h4>
            <a href="{{ route('booking.index') }}" class="btn btn-secondary btn-sm">
                Back
            </a>
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

            <form action="{{ route('bookings.store') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

 <div class="mb-3"> <label>Customer Name</label> <input type="text" name="customer_name" class="form-control" required> </div>
 <div class="mb-3"> <label>Customer ID</label> <input type="text" name="customer_id" class="form-control" required> </div>

                <div class="mb-3">
                    <label>ID Card Photo</label>
                    <input type="file" name="identity_card" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Proof of Payment</label>
                    <input type="file" name="payment_proof" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    Confirm Booking
                </button>

            </form>
        </div>
    </div>
</div>

@endsection
