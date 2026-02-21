@extends('layouts.app')

@push('styles')
<style>
.return-header { background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 60%, #1d4ed8 100%); border-radius: 14px 14px 0 0; padding: 20px 24px; }
.return-header h4 { color: white; font-weight: 700; margin: 0; font-size: 1.2rem; }
.return-card { border: none; border-radius: 14px; box-shadow: 0 8px 32px rgba(37,99,235,0.15); overflow: hidden; max-width: 600px; margin: 0 auto; }
.return-card .card-body { padding: 28px; background: #ffffff; }
.info-box { background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 10px; padding: 16px 20px; margin-bottom: 20px; }
.info-box p { margin: 0; color: #1e40af; font-size: 14px; line-height: 1.8; }
.info-box p b { color: #1e3a8a; font-weight: 700; }
.form-label { font-weight: 600; color: #1e3a8a; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
.form-select { border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 10px 14px; font-size: 14px; transition: all 0.2s; }
.form-select:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.15); }
.btn-process { background: linear-gradient(135deg, #1e3a8a, #2563eb); border: none; color: white; padding: 12px 32px; border-radius: 10px; font-weight: 700; font-size: 15px; transition: all 0.2s; box-shadow: 0 4px 16px rgba(37,99,235,0.35); width: 100%; }
.btn-process:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(37,99,235,0.45); color: white; }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <div class="return-card card">

        <div class="return-header">
            <h4><i class="fas fa-undo me-2"></i> Return Vehicle: {{ $vehicle->name }}</h4>
        </div>

        <div class="card-body">

            <div class="info-box">
                <p><b>Customer:</b> {{ optional($booking->customer)->customer_name ?? 'N/A' }}</p>
                <p><b>Plate:</b> {{ $vehicle->plate_number }}</p>
                <p><b>Booking ID:</b> #{{ $booking->id }}</p>
                <p><b>End Date:</b> {{ \Carbon\Carbon::parse($booking->end_date)->format('d M Y') }}</p>
            </div>

            <form action="{{ route('returns.store') }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to return this vehicle?');">
                @csrf
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                <div class="mb-4">
                    <label class="form-label">Vehicle Condition</label>
                    <select name="vehicle_condition" class="form-select">
                        <option value="Good">✅ Good</option>
                        <option value="Minor Damage">⚠️ Minor Damage</option>
                        <option value="Major Damage">🚨 Major Damage</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-process">
                    <i class="fas fa-check-circle me-2"></i> Process Return
                </button>
            </form>

        </div>
    </div>
</div>
@endsection