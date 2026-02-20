@extends('layouts.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4>Booking Vehicle: {{ $vehicle->name }}</h4>
            <a href="{{ route('booking.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>

        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('bookings.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

                <div class="mb-3">
                    <label class="form-label">Customer Name</label>
                    <select name="customer_id" class="form-select select2" required>
                        <option value="">-- Pilih Customer --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">
                                {{ $customer->customer_name }} ({{ $customer->customer_id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">ID Card Photo</label>
                    <input type="file" name="identity_card" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Proof of Payment</label>
                    <input type="file" name="payment_proof" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Confirm Booking</button>
            </form>

        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({ placeholder: 'Cari customer...' });
    });
</script>
@endpush

@endsection