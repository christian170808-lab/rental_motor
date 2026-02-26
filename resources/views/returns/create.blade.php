@extends('layouts.app')

@push('styles')
<style>
.return-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 60%, #1d4ed8 100%);
    border-radius: 14px 14px 0 0;
    padding: 18px 24px;
}
.return-header h4 { color: #fff; font-weight: 700; margin: 0; font-size: 1.15rem; }
.return-card {
    border: none;
    border-radius: 14px;
    box-shadow: 0 8px 32px rgba(37,99,235,0.15);
    overflow: hidden;
    max-width: 600px;
    margin: 0 auto;
}
.return-card .card-body { padding: 28px; background: #fff; }
.info-box {
    background: #eff6ff;
    border: 1.5px solid #bfdbfe;
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 22px;
}
.info-box p { margin: 0; color: #1e40af; font-size: 14px; line-height: 1.9; }
.info-box p b { color: #1e3a8a; }
.form-label { font-weight: 600; color: #1e3a8a; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
.form-select {
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 14px;
    transition: all 0.2s;
}
.form-select:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.15); }
.btn-process {
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    border: none;
    color: #fff;
    padding: 12px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 15px;
    width: 100%;
    transition: all 0.2s;
    box-shadow: 0 4px 16px rgba(37,99,235,0.3);
}
.btn-process:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(37,99,235,0.4); color: #fff; }
</style>
@endpush

@section('content')
<div class="container mt-5">
    <div class="card return-card">

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

            <form action="{{ route('returns.store') }}" method="POST" id="returnForm">
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

                <button type="button" class="btn btn-process" data-bs-toggle="modal" data-bs-target="#confirmModal">
                    <i class="fas fa-check-circle me-2"></i> Process Return
                </button>
            </form>

        </div>
    </div>
</div>

{{-- Confirm Modal --}}
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px; overflow:hidden; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <div style="background:linear-gradient(135deg,#1e3a8a,#1d4ed8); padding:18px 24px;">
                <h5 class="text-white fw-bold mb-0"><i class="fas fa-undo me-2"></i> Confirm Return</h5>
            </div>
            <div class="modal-body p-4 text-center">
                <div style="width:64px;height:64px;background:#eff6ff;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="fas fa-motorcycle" style="font-size:28px;color:#1e40af;"></i>
                </div>
                <p class="fw-semibold mb-1" style="font-size:16px;">Are you sure?</p>
                <p class="text-muted mb-4" style="font-size:14px;">You are about to process the return for <strong>{{ $vehicle->name }}</strong>.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn fw-bold text-white px-4"
                        style="background:linear-gradient(135deg,#1e3a8a,#2563eb);border:none;border-radius:10px;padding:10px 24px;"
                        onclick="document.getElementById('returnForm').submit()">
                        <i class="fas fa-check me-2"></i> Yes, Process
                    </button>
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius:10px;">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection