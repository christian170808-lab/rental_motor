@extends('layouts.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.booking-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 60%, #1d4ed8 100%);
    border-radius: 14px 14px 0 0;
    padding: 20px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.booking-header h4 { color: white; font-weight: 700; margin: 0; font-size: 1.2rem; }
.booking-card { border: none; border-radius: 14px; box-shadow: 0 8px 32px rgba(37,99,235,0.15); overflow: hidden; max-width: 700px; margin: 0 auto; }
.booking-card .card-body { padding: 28px; background: #ffffff; }
.form-label { font-weight: 600; color: #1e3a8a; font-size: 0.875rem; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
.form-control, .form-select { border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 10px 14px; font-size: 14px; transition: all 0.2s; }
.form-control:focus, .form-select:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.15); }
.btn-confirm { background: linear-gradient(135deg, #1e3a8a, #2563eb); border: none; color: white; padding: 12px 32px; border-radius: 10px; font-weight: 700; font-size: 15px; transition: all 0.2s; box-shadow: 0 4px 16px rgba(37,99,235,0.35); }
.btn-confirm:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(37,99,235,0.45); color: white; }
.divider { border: none; border-top: 1.5px solid #f1f5f9; margin: 20px 0; }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <div class="booking-card card">

        <div class="booking-header">
            <h4><i class="fas fa-calendar-plus me-2"></i> Booking: {{ $vehicle->name }}</h4>
            <a href="{{ route('booking.index') }}" class="btn btn-sm"
               style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.4); border-radius: 8px;">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-4">
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
                        <option value="">-- Select Customer --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">
                                {{ $customer->customer_name }} ({{ $customer->customer_id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <hr class="divider">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">ID Card Photo</label>
                        <input type="file" name="identity_card" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Proof of Payment</label>
                        <input type="file" name="payment_proof" class="form-control" required>
                    </div>
                </div>

                <hr class="divider">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                </div>

                <hr class="divider">

                <button type="submit" class="btn btn-confirm w-100">
                    <i class="fas fa-check-circle me-2"></i> Confirm Booking
                </button>
            </form>

        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({ placeholder: 'Search customer...' });
    });
</script>
@endpush

@endsection