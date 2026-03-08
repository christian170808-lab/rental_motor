@extends('layouts.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>

/* ── ANIMATIONS ── */
@keyframes popIn {
    0%   { opacity: 0; transform: scale(.85) translateY(-20px); }
    70%  { transform: scale(1.03) translateY(2px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-30px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    20%      { transform: translateX(-6px); }
    40%      { transform: translateX(6px); }
    60%      { transform: translateX(-4px); }
    80%      { transform: translateX(4px); }
}
@keyframes toastIn     { from { opacity: 0; transform: translateX(80px); } to { opacity: 1; transform: translateX(0); } }
@keyframes toastOut    { from { opacity: 1; transform: translateX(0); }    to { opacity: 0; transform: translateX(80px); } }
@keyframes toastBar    { from { width: 100%; } to { width: 0%; } }
@keyframes fadeOverlay { to { opacity: 1; } }
@keyframes pulseWarn   { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.12); } }

.modal.show .modal-dialog { animation: popIn .32s cubic-bezier(.34, 1.56, .64, 1) both; }
.modal.show .modal-header { animation: slideDown .28s ease both .05s; }
.modal-content             { border: none; border-radius: 16px !important; box-shadow: 0 24px 70px rgba(0,0,0,.22); overflow: hidden; }
.field-shake               { animation: shake .4s ease; border-color: #dc2626 !important; }

/* ── TOAST ── */
#toastBox {
    position: fixed; top: 20px; right: 20px; z-index: 99999;
    display: flex; flex-direction: column; gap: 10px; pointer-events: none;
}
.toast-item {
    min-width: 280px; max-width: 400px; padding: 11px 14px;
    border-radius: 10px; font-size: 13.5px; font-weight: 500;
    display: flex; align-items: center; gap: 9px;
    box-shadow: 0 2px 12px rgba(0,0,0,.10);
    pointer-events: auto; position: relative; overflow: hidden;
    animation: toastIn .3s cubic-bezier(.34, 1.56, .64, 1) both;
    border: 1px solid transparent;
}
.toast-item.hide { animation: toastOut .25s ease forwards; }
.toast-success { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }
.toast-error   { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
.toast-info    { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
.toast-warning { background: #fffbeb; color: #92400e; border-color: #fde68a; }
.toast-item i  { font-size: 1rem; flex-shrink: 0; }
.toast-close-btn {
    margin-left: auto; background: none; border: none; cursor: pointer;
    font-size: 14px; line-height: 1; padding: 0 2px;
    opacity: .45; color: inherit; flex-shrink: 0; transition: opacity .15s;
}
.toast-close-btn:hover { opacity: 1; }
.toast-bar {
    position: absolute; bottom: 0; left: 0; height: 2.5px;
    border-radius: 0 0 10px 10px; background: currentColor; opacity: .2;
    animation: toastBar linear forwards;
}

/* ── CONFIRM OVERLAY ── */
.konfirm-overlay {
    position: fixed; inset: 0; z-index: 99998;
    background: rgba(0,0,0,.55); backdrop-filter: blur(3px);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; animation: fadeOverlay .2s ease forwards;
}
.konfirm-box {
    background: #fff; border-radius: 18px; padding: 32px 28px;
    max-width: 420px; width: 90%; text-align: center;
    box-shadow: 0 30px 80px rgba(0,0,0,.25);
    animation: popIn .32s cubic-bezier(.34, 1.56, .64, 1) both;
}
.konfirm-icon    { font-size: 3rem; margin-bottom: 12px; }
.konfirm-title   { font-size: 18px; font-weight: 800; color: #111827; margin-bottom: 6px; }
.konfirm-sub     { font-size: 14px; color: #6b7280; margin-bottom: 22px; line-height: 1.6; }
.konfirm-actions { display: flex; gap: 10px; }
.konfirm-actions .btn { flex: 1; border-radius: 10px; font-weight: 700; padding: 10px; font-size: 14px; }

/* ── HEADER ── */
.bk-header {
    background: linear-gradient(135deg, #1a237e 0%, #1565c0 60%, #1976d2 100%);
    border-radius: 14px; padding: 20px 26px; margin-bottom: 22px;
    box-shadow: 0 6px 24px rgba(21,101,192,.35);
    display: flex; align-items: center; gap: 14px;
}
.bk-header .bk-icon {
    width: 46px; height: 46px; background: rgba(255,255,255,.15);
    border-radius: 10px; display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; color: #fff; flex-shrink: 0;
}
.bk-header h2 { color: #fff; font-weight: 700; margin: 0; font-size: 1.25rem; }
.bk-header p  { color: rgba(255,255,255,.72); margin: 3px 0 0; font-size: .83rem; }

/* ── FILTER ── */
.bk-filter {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
    padding: 12px 14px; margin-bottom: 18px; box-shadow: 0 2px 8px rgba(0,0,0,.05);
}
.bk-filter .form-control,
.bk-filter .form-select {
    border: 1.5px solid #e5e7eb; border-radius: 8px;
    font-size: 13.5px; padding: 8px 12px; transition: border-color .18s;
}
.bk-filter .form-control:focus,
.bk-filter .form-select:focus { border-color: #1565c0; box-shadow: none; }

/* ── BUTTONS ── */
.btn-search {
    background: linear-gradient(135deg, #1565c0, #1976d2);
    color: #fff; border: none; border-radius: 8px;
    font-weight: 600; padding: 8px 22px; font-size: 13.5px; transition: all .18s;
}
.btn-search:hover { box-shadow: 0 4px 12px rgba(21,101,192,.35); transform: translateY(-1px); color: #fff; }

.btn-add-rent {
    background: linear-gradient(135deg, #16a34a, #15803d);
    color: #fff; border: none; border-radius: 8px; font-weight: 700;
    padding: 8px 18px; font-size: 13.5px; transition: all .18s;
    white-space: nowrap; display: inline-flex; align-items: center; gap: 6px;
}
.btn-add-rent:hover { box-shadow: 0 4px 12px rgba(22,163,74,.35); transform: translateY(-1px); color: #fff; }

/* ── TABLE ── */
.bk-table-wrap {
    background: #fff; border-radius: 12px; border: 1px solid #e5e7eb;
    overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.06); margin-bottom: 20px;
}
.bk-table { width: 100%; margin: 0; border-collapse: collapse; }
.bk-table thead th {
    background: linear-gradient(90deg, #1a237e 0%, #1565c0 100%);
    color: #fff; font-size: 12px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .6px; padding: 13px 16px; border: none;
    text-align: center; white-space: nowrap;
}
.bk-table tbody td {
    padding: 14px 16px; vertical-align: middle; font-size: 14px;
    border-bottom: 1px solid #f1f5f9; text-align: center;
    white-space: nowrap; color: #1f2937;
}
.bk-table tbody tr:last-child td { border-bottom: none; }
.bk-table tbody tr:hover          { background: #f0f7ff; }

/* ── BADGES & CHIPS ── */
.plate-mono {
    color: #1e40af; font-family: monospace; font-weight: 700;
    background: #eff6ff; padding: 3px 10px; border-radius: 5px;
    font-size: 13px; border: 1px solid #dbeafe; letter-spacing: .5px;
}
.badge-paid {
    background: linear-gradient(135deg, #16a34a, #15803d); color: #fff;
    font-size: 12px; font-weight: 700; padding: 5px 14px; border-radius: 20px;
    display: inline-flex; align-items: center; gap: 5px;
    box-shadow: 0 2px 8px rgba(22,163,74,.25); white-space: nowrap;
}
.badge-dp {
    background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff;
    font-size: 12px; font-weight: 700; padding: 5px 14px; border-radius: 20px;
    display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;
}
.chip-available { background: #dcfce7; color: #15803d; font-size: 12px; font-weight: 700; padding: 4px 13px; border-radius: 20px; display: inline-block; }
.chip-rented    { background: #fee2e2; color: #dc2626; font-size: 12px; font-weight: 700; padding: 4px 13px; border-radius: 20px; display: inline-block; }

/* ── ACTION BUTTONS ── */
.btn-act {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 13px; font-weight: 600; padding: 6px 13px;
    border-radius: 7px; border: none; cursor: pointer;
    transition: all .15s; white-space: nowrap; line-height: 1.4;
}
.btn-act:hover    { transform: translateY(-1px); filter: brightness(1.1); }
.btn-act.view     { background: #06b6d4; color: #fff; box-shadow: 0 2px 6px rgba(6,182,212,.3); }
.btn-act.edit     { background: #f59e0b; color: #fff; box-shadow: 0 2px 6px rgba(245,158,11,.3); }
.btn-act.ret      { background: #3b82f6; color: #fff; box-shadow: 0 2px 6px rgba(59,130,246,.3); }
.btn-act.payoff   { background: #10b981; color: #fff; box-shadow: 0 2px 6px rgba(16,185,129,.3); }
.btn-act.del      { background: #ef4444; color: #fff; box-shadow: 0 2px 6px rgba(239,68,68,.3); }

/* ── MISC ── */
.vehicle-thumb { width: 68px; height: 50px; object-fit: cover; border-radius: 7px; border: 1px solid #e5e7eb; }
.bk-empty {
    background: #fff; border-radius: 12px; border: 1px solid #e5e7eb;
    padding: 60px 20px; text-align: center; box-shadow: 0 2px 12px rgba(0,0,0,.05);
}
.bk-empty i { font-size: 3rem; color: #d1d5db; margin-bottom: 14px; display: block; }

/* ── MODAL HEADERS ── */
.modal-header-blue { background: linear-gradient(135deg, #1a237e, #1565c0); border: none; }
.modal-header-blue .modal-title,
.modal-header-blue h5 { color: #fff; font-weight: 700; margin: 0; font-size: 1.05rem; }
.modal-header-red { background: linear-gradient(135deg, #991b1b, #dc2626); border: none; }

/* ── FIELDS ── */
.field-label { font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 5px; display: block; }
.field-value { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 9px 13px; font-size: 14px; color: #111827; min-height: 40px; }

/* ── INFO & PENALTY BOX ── */
.info-box { background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 10px; padding: 13px 16px; margin-bottom: 18px; }
.info-box p   { margin: 0 0 2px; color: #1e40af; font-size: 13.5px; }
.info-box p b { color: #1e3a8a; }

.penalty-box   { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 9px; padding: 13px 15px; margin-bottom: 14px; }
.penalty-title { font-size: 11.5px; font-weight: 700; color: #ea580c; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 9px; }
.penalty-row   { display: flex; justify-content: space-between; font-size: 13px; color: #92400e; padding: 2px 0; }
.penalty-row.total { border-top: 1px dashed #fed7aa; margin-top: 7px; padding-top: 7px; font-weight: 700; color: #ea580c; font-size: 14px; }

.grand-total-box           { background: linear-gradient(135deg, #fef2f2, #fee2e2); border: 1.5px solid #fca5a5; border-radius: 9px; padding: 13px 16px; }
.grand-total-box .gt-label { font-size: 11.5px; font-weight: 700; color: #dc2626; text-transform: uppercase; letter-spacing: .5px; }
.grand-total-box .gt-value { font-size: 21px; font-weight: 800; color: #b91c1c; margin-top: 2px; }

/* ── CANCEL WARNING ── */
.cancel-warning {
    background: linear-gradient(135deg, #fef2f2, #fee2e2); border: 1px solid #fca5a5;
    border-radius: 11px; padding: 18px; text-align: center; margin-bottom: 16px;
}
.cancel-warning .dw-icon { font-size: 2.5rem; color: #dc2626; margin-bottom: 8px; animation: pulseWarn 1.6s ease infinite; }
.cancel-warning .dw-text { font-size: 14.5px; color: #7f1d1d; font-weight: 600; }
.cancel-warning .dw-name { font-size: 17px; font-weight: 800; color: #991b1b; margin-top: 5px; }
.cancel-warning .dw-note { font-size: 12px; color: #b91c1c; margin-top: 7px; }

/* ── PAYMENT TYPE ── */
.pay-type-group { display: flex; gap: 10px; }
.pay-type-card {
    flex: 1; border: 2px solid #e5e7eb; border-radius: 10px;
    padding: 11px 12px; cursor: pointer; text-align: center;
    background: #f9fafb; transition: border-color .18s, background .18s;
}
.pay-type-card input[type="radio"] { display: none; }
.pay-type-card:hover    { border-color: #bfdbfe; background: #eff6ff; }
.pay-type-card.sel-full { border-color: #16a34a; background: #f0fdf4; }
.pay-type-card.sel-dp   { border-color: #1d4ed8; background: #eff6ff; }
.pay-type-card .pt-icon  { font-size: 1.3rem; }
.pay-type-card .pt-title { font-weight: 700; font-size: 13.5px; color: #1f2937; margin-top: 4px; }
.pay-type-card .pt-sub   { font-size: 11px; color: #6b7280; }

.cost-box      { border-radius: 10px; padding: 13px 15px; margin-top: 13px; font-size: 13.5px; }
.cost-box.full { background: #f0fdf4; border: 1.5px solid #86efac; }
.cost-box.dp   { background: #eff6ff; border: 1.5px solid #bfdbfe; }
.cost-row      { display: flex; justify-content: space-between; padding: 3px 0; }
.cost-row .cl  { color: #374151; }
.cost-row .cv  { font-weight: 700; color: #111827; }
.cost-row.hi    .cl, .cost-row.hi    .cv { color: #16a34a; font-size: 15px; font-weight: 800; }
.cost-row.hi-dp .cl, .cost-row.hi-dp .cv { color: #1d4ed8; font-size: 15px; font-weight: 800; }
.cost-divider  { border-top: 1px dashed #d1d5db; margin: 6px 0; }

/* ── MODAL VEHICLE TABLE ── */
.modal-veh-table { width: 100%; margin: 0; border-collapse: collapse; }
.modal-veh-table thead th {
    background: linear-gradient(90deg, #1a237e, #1565c0); color: #fff;
    font-size: 12px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .5px; padding: 12px 14px; border: none; text-align: center;
}
.modal-veh-table td {
    padding: 11px 14px; vertical-align: middle;
    font-size: 13.5px; border-bottom: 1px solid #f1f5f9; text-align: center;
}
.modal-veh-table tbody tr:hover { background: #f0f7ff; }

/* ── CANCEL REASON INPUT ── */
.cancel-reason-input {
    border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 13.5px;
    padding: 9px 12px; transition: border-color .18s; width: 100%;
}
.cancel-reason-input:focus { border-color: #dc2626; box-shadow: none; outline: none; }

/* ── PAGINATION ── */
.page-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 36px; height: 36px; border-radius: 8px;
    border: 1px solid #e5e7eb; background: #fff;
    color: #374151; font-size: 14px; font-weight: 500;
    text-decoration: none; cursor: pointer; transition: all 0.2s;
}
.page-btn:hover:not([disabled]) { border-color: #3b82f6; color: #3b82f6; }
.page-btn.active    { background: #3b82f6; border-color: #3b82f6; color: #fff; }
.page-btn[disabled] { opacity: 0.4; cursor: not-allowed; }

</style>
@endpush

{{-- TOAST CONTAINER --}}
<div id="toastBox"></div>

@section('content')
<div class="container-fluid" style="max-width: 1400px;">

    {{-- HEADER --}}
    <div class="bk-header">
        <div class="bk-icon"><i class="fas fa-motorcycle"></i></div>
        <div>
            <h2>Vehicle Management</h2>
            <p>Manage all active rentals and vehicle returns</p>
        </div>
    </div>

    {{-- SESSION NOTIFICATIONS --}}
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                showToast('success', '<i class="fas fa-check-circle"></i>', '{{ addslashes(session('success')) }}');
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                showToast('error', '<i class="fas fa-times-circle"></i>', '{{ addslashes(session('error')) }}');
            });
        </script>
    @endif

    {{-- FILTER --}}
    <form action="{{ route('booking.index') }}" method="GET" class="bk-filter">
        <div class="d-flex gap-2 align-items-center">
            <div class="input-group flex-grow-1">
                <input type="text" name="search" id="searchInput" class="form-control"
                       placeholder="Search by vehicle name or plate…"
                       value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('booking.index', request()->except('search')) }}"
                       class="btn btn-outline-secondary"
                       style="border-color:#e5e7eb;background:#fff;color:#6b7280;padding:8px 12px;">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
                <button type="submit" class="btn-search">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </div>
            <select name="type" class="form-select" onchange="this.form.submit()" style="max-width:140px;">
                <option value="">All Types</option>
                @foreach($vehicleTypes as $vt)
                <option value="{{ $vt->name }}" {{ request('type') == $vt->name ? 'selected' : '' }}>
                    {{ $vt->label }}
                </option>
                @endforeach
            </select>
            <select name="status" class="form-select" onchange="this.form.submit()" style="max-width:150px;">
                <option value="">All Status</option>
                <option value="dp"   {{ request('status') == 'dp'   ? 'selected' : '' }}>Down Payment</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
            <button type="button" class="btn-add-rent" data-bs-toggle="modal" data-bs-target="#addRentModal">
                <i class="fas fa-plus"></i> Add Rental
            </button>
        </div>
    </form>

    {{-- ACTIVE RENTALS TABLE --}}
    @if($rentedVehicles->isEmpty())
        <div class="bk-empty">
            <i class="fas fa-motorcycle opacity-25"></i>
            <p class="fw-semibold mb-1 text-muted">No active rentals yet</p>
            <small class="text-muted">Click <strong>+ Add Rental</strong> to start a new rental.</small>
        </div>
    @else
        <div class="bk-table-wrap">
            <div class="table-responsive">
                <table class="bk-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Renter</th>
                            <th>Phone</th>
                            <th>Type</th>
                            <th>Plate</th>
                            <th>Duration</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rentedVehicles as $v)
                        @php
                            $ab          = $v->bookings->where('payment_status', 'paid')
                                             ->whereIn('payment_type', ['dp', 'full'])
                                             ->sortByDesc('id')->first();
                            $customer    = $ab ? $ab->customer : null;
                            $dp          = $ab ? $ab->total_cost * .5 : null;
                            $sisa        = $dp;
                            $durasi      = $ab ? \Carbon\Carbon::parse($ab->start_date)->diffInDays(\Carbon\Carbon::parse($ab->end_date)) + 1 : null;
                            $cName       = optional($customer)->customer_name ?? '';
                            $cPhone      = optional($customer)->phone_number  ?? '';
                            $isFull      = $ab && $ab->payment_type === 'full';
                            $lateDays    = 0;
                            $latePenalty = 0;
                            if ($ab) {
                                $ed = \Carbon\Carbon::parse($ab->end_date)->startOfDay();
                                $td = \Carbon\Carbon::now()->startOfDay();
                                if ($td->gt($ed)) {
                                    $lateDays    = $ed->diffInDays($td);
                                    $latePenalty = $lateDays * 50000;
                                }
                            }
                        @endphp

                        <tr>
                            <td class="fw-semibold">{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $cName ?: '—' }}</td>
                            <td>{{ $cPhone ?: '—' }}</td>
                            <td>
                                @php $vType = $vehicleTypes->firstWhere('name', $v->type); @endphp
                                {{ $vType ? $vType->label : ($v->type ? ucfirst($v->type) : '—') }}
                            </td>
                            <td><span class="plate-mono">{{ $ab ? $v->plate_number : '—' }}</span></td>
                            <td>
                                @if($durasi)
                                    <span style="display:inline-flex;align-items:center;gap:5px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:20px;padding:5px 13px;font-size:13px;font-weight:600;color:#475569;">
                                        <i class="fas fa-clock" style="color:#94a3b8;font-size:12px;"></i> {{ $durasi }} days
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($ab)
                                    @if($isFull)
                                        <span class="badge-paid"><i class="fas fa-check-circle"></i> Paid</span>
                                    @else
                                        <span class="badge-dp"><i class="fas fa-clock"></i> DP 50%</span>
                                        <div class="mt-1" style="font-size:12px;color:#1d4ed8;font-weight:600;">
                                            Rp {{ number_format($dp, 0, ',', '.') }}
                                        </div>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    @if($ab && !$isFull)
                                        <button type="button" class="btn-act payoff"
                                            data-bs-toggle="modal" data-bs-target="#payoffModal{{ $v->id }}">
                                            <i class="fas fa-money-bill-wave"></i> Pay Off
                                        </button>
                                    @endif

                                    <button type="button" class="btn-act view"
                                        data-bs-toggle="modal" data-bs-target="#detailModal{{ $v->id }}">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>

                                    <button type="button" class="btn-act edit"
                                        data-bs-toggle="modal" data-bs-target="#editModal{{ $v->id }}">
                                        <i class="fas fa-pen"></i> Edit
                                    </button>

                                    @if($ab)
                                        @if($isFull)
                                            <button type="button" class="btn-act ret"
                                                data-bs-toggle="modal" data-bs-target="#returnModal"
                                                data-vehicle-id="{{ $v->id }}"
                                                data-vehicle-name="{{ $v->name }}"
                                                data-plate="{{ $v->plate_number }}"
                                                data-booking-id="{{ $ab->id }}"
                                                data-customer="{{ $cName }}"
                                                data-end-date="{{ \Carbon\Carbon::parse($ab->end_date)->format('d M Y') }}">
                                                <i class="fas fa-undo"></i> Return
                                            </button>
                                        @else
                                            <button type="button" class="btn-act ret"
                                                style="opacity:.45;cursor:not-allowed;pointer-events:none;" disabled>
                                                <i class="fas fa-lock"></i> Return
                                            </button>
                                        @endif
                                    @endif

                                    <button type="button" class="btn-act del"
                                    onclick="bukaModalBatal({{ $ab ? $ab->id : 0 }})">
                                        <i class="fas fa-times-circle"></i> Cancel
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- DETAIL MODAL --}}
                        <div class="modal fade" id="detailModal{{ $v->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header modal-header-blue">
                                        <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i> Booking Detail</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="field-label">Customer</label>
                                                <div class="field-value">{{ $cName ?: '—' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label">Phone Number</label>
                                                <div class="field-value">{{ $cPhone ?: '—' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label">Vehicle</label>
                                                <div class="field-value">{{ $v->name }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label">Plate Number</label>
                                                <div class="field-value">{{ $v->plate_number }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label">Rental Period</label>
                                                <div class="field-value">
                                                    @if($ab)
                                                        {{ \Carbon\Carbon::parse($ab->start_date)->format('d M Y') }} —
                                                        {{ \Carbon\Carbon::parse($ab->end_date)->format('d M Y') }}
                                                    @else —
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label">Duration</label>
                                                <div class="field-value">{{ $durasi ? $durasi . ' days' : '—' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label">Total Cost</label>
                                                <div class="field-value fw-bold text-primary">
                                                    {{ $ab ? 'Rp ' . number_format($ab->total_cost, 0, ',', '.') : '—' }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="field-label">Payment Status</label>
                                                <div class="field-value">
                                                    @if($isFull)
                                                        <span class="badge-paid"><i class="fas fa-check-circle"></i> Paid</span>
                                                    @else
                                                        <span class="badge-dp"><i class="fas fa-clock"></i> DP 50% — Rp {{ number_format($dp, 0, ',', '.') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if(!$isFull && $sisa)
                                            <div class="col-md-6">
                                                <label class="field-label">Remaining Bill</label>
                                                <div class="field-value fw-bold text-danger">Rp {{ number_format($sisa, 0, ',', '.') }}</div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- EDIT MODAL --}}
                        <div class="modal fade" id="editModal{{ $v->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header modal-header-blue">
                                        <h5 class="modal-title"><i class="fas fa-pen me-2"></i> Edit Booking</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        @if($ab)
                                        <form action="{{ route('booking.update', $ab->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="mb-3">
                                                <label class="field-label">Phone Number</label>
                                                <input type="text" name="phone_number" class="form-control" value="{{ $cPhone }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="field-label">Renter Name</label>
                                                <input type="text" name="customer_name" class="form-control" value="{{ $cName }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="field-label">Vehicle Type</label>
                                                <div class="field-value">{{ ucfirst($v->type) }}</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="field-label">Plate Number</label>
                                                <div class="field-value">{{ $v->plate_number }}</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="field-label">Start Date</label>
                                                <input type="date" name="start_date" class="form-control" value="{{ $ab->start_date }}" required>
                                            </div>
                                            <div class="mb-4">
                                                <label class="field-label">End Date</label>
                                                <input type="date" name="end_date" class="form-control" value="{{ $ab->end_date }}" required>
                                            </div>
                                            <button type="submit" class="btn w-100 fw-bold text-white py-2"
                                                style="background:linear-gradient(135deg,#1a237e,#1565c0);border:none;border-radius:9px;">
                                                <i class="fas fa-save me-2"></i> Save Changes
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CANCEL MODAL --}}
                        <div class="modal fade" id="cancelModal{{ $ab->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
                                <div class="modal-content">
                                    <div class="modal-header modal-header-red">
                                        <h5 class="modal-title text-white fw-bold mb-0">
                                            <i class="fas fa-times-circle me-2"></i> Cancel Rental
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="cancel-warning">
                                            <div class="dw-icon"><i class="fas fa-exclamation-triangle"></i></div>
                                            <div class="dw-text">Are you sure you want to cancel this rental?</div>
                                            <div class="dw-name">{{ $v->name }} — {{ $v->plate_number }}</div>
                                            @if($ab)
                                                <div class="dw-note">
                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                    Currently rented by <strong>{{ $cName }}</strong>
                                                </div>
                                            @endif
                                            <div class="dw-note">This action cannot be undone.</div>
                                        </div>

                                        <form action="{{ route('booking.destroy', $ab->id) }}" method="POST" id="cancelForm{{ $ab->id }}">
                                            @csrf @method('DELETE')

                                            <div class="mb-3">
                                                <label class="field-label">
                                                    <i class="fas fa-comment-alt me-1"></i>
                                                    Cancellation Reason <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    name="cancel_reason"
                                                    id="cancelReasonValue{{ $ab->id }}"
                                                    class="cancel-reason-input"
                                                    placeholder="Write the cancellation reason in detail…"
                                                    required>
                                            </div>

                                            <div class="d-flex gap-2 mt-3">
                                                <button
                                                    type="button"
                                                    class="btn btn-danger fw-bold flex-fill py-2"
                                                    style="border-radius:9px;"
                                                    onclick="konfirmasiBatal({{ $ab->id }}, '{{ addslashes($v->name) }}', '{{ addslashes($cName) }}')">
                                                    <i class="fas fa-times-circle me-1"></i> Yes, Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PAY OFF MODAL --}}
                        @if($ab && !$isFull)
                        <div class="modal fade" id="payoffModal{{ $v->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
                                <div class="modal-content">
                                    <div class="modal-header modal-header-blue">
                                        <h5 class="modal-title"><i class="fas fa-money-bill-wave me-2"></i> Rental Payoff</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="info-box">
                                            <div class="row g-1">
                                                <div class="col-6"><p><b>Customer:</b> {{ $cName }}</p></div>
                                                <div class="col-6"><p><b>Vehicle:</b> {{ $v->name }} ({{ ucfirst($v->type) }})</p></div>
                                                <div class="col-6"><p><b>Period:</b> {{ \Carbon\Carbon::parse($ab->start_date)->format('d M Y') }} — {{ \Carbon\Carbon::parse($ab->end_date)->format('d M Y') }}</p></div>
                                                <div class="col-6"><p><b>Duration:</b> {{ $durasi }} days</p></div>
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-3">
                                            <div class="col-6">
                                                <label class="field-label">Total Rental Cost</label>
                                                <div class="field-value fw-bold">Rp {{ number_format($ab->total_cost, 0, ',', '.') }}</div>
                                            </div>
                                            <div class="col-6">
                                                <label class="field-label">Already Paid (DP 50%)</label>
                                                <div class="field-value fw-bold text-success">Rp {{ number_format($dp, 0, ',', '.') }}</div>
                                            </div>
                                        </div>
                                        <div class="penalty-box">
                                            <div class="penalty-title"><i class="fas fa-exclamation-triangle me-1"></i> Penalty Details</div>
                                            <div class="penalty-row">
                                                <span>Late Return
                                                    <span id="lateDaysLabel{{ $v->id }}">
                                                        {{ $lateDays > 0 ? '(' . $lateDays . ' days x Rp 50.000)' : '(on time)' }}
                                                    </span>
                                                </span>
                                                <span id="latePenaltyLabel{{ $v->id }}">Rp {{ number_format($latePenalty, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="penalty-row total">
                                                <span>Total Penalty</span>
                                                <span id="totalPenaltyLabel{{ $v->id }}">Rp {{ number_format($latePenalty, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                        <div class="grand-total-box">
                                            <div class="gt-label"><i class="fas fa-wallet me-1"></i> Total Amount Due</div>
                                            <div class="gt-value" id="grandTotal{{ $v->id }}">Rp {{ number_format($sisa + $latePenalty, 0, ',', '.') }}</div>
                                            <div style="font-size:11.5px;color:#dc2626;margin-top:4px;">Remaining bill + penalty</div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <form action="{{ route('booking.return', $ab->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="kondisi" id="conditionHidden{{ $v->id }}" value="Good">
                                            <button type="submit" class="btn btn-success fw-bold px-4">
                                                <i class="fas fa-check-circle me-2"></i> Confirm Payment
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @endforeach
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                @if($rentedVehicles->hasPages())
                <div class="p-3 border-top d-flex justify-content-between align-items-center" style="background:#fff;">
                    <span class="text-muted" style="font-size:14px;">
                        Showing {{ $rentedVehicles->firstItem() }} to {{ $rentedVehicles->lastItem() }} of {{ $rentedVehicles->total() }} entries
                    </span>
                    <div class="d-flex gap-1">
                        @if($rentedVehicles->onFirstPage())
                            <button class="page-btn" disabled>&lsaquo;</button>
                        @else
                            <a href="{{ $rentedVehicles->previousPageUrl() }}" class="page-btn">&lsaquo;</a>
                        @endif

                        @for($page = 1; $page <= $rentedVehicles->lastPage(); $page++)
                            @if($page == $rentedVehicles->currentPage())
                                <button class="page-btn active">{{ $page }}</button>
                            @else
                                <a href="{{ $rentedVehicles->url($page) }}" class="page-btn">{{ $page }}</a>
                            @endif
                        @endfor

                        @if($rentedVehicles->hasMorePages())
                            <a href="{{ $rentedVehicles->nextPageUrl() }}" class="page-btn">&rsaquo;</a>
                        @else
                            <button class="page-btn" disabled>&rsaquo;</button>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    @endif

</div>

{{-- SELECT VEHICLE MODAL --}}
<div class="modal fade" id="addRentModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header modal-header-blue">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i> Select Vehicle to Rent</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="px-4 py-3 border-bottom bg-light">
                <div class="row g-2">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="modalSearch" class="form-control border-start-0"
                                   placeholder="Search by name or plate number…" onkeyup="filterModal()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="modalTypeFilter" class="form-select" onchange="filterModal()">
                            <option value="">All Types</option>
                            @foreach($vehicleTypes as $vt)
                            <option value="{{ $vt->name }}">{{ $vt->label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="modalStatusFilter" class="form-select" onchange="filterModal()">
                            <option value="">All Status</option>
                            <option value="tersedia">Available</option>
                            <option value="disewa">Currently Rented</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="modal-veh-table" id="modalVehicleTable">
                        <thead>
                            <tr>
                                <th>No</th><th>Photo</th><th>Vehicle Name</th><th>Type</th>
                                <th>Plate</th><th>Price/Day</th><th>Status</th><th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="modalTableBody">
                            <tr><td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-spinner fa-spin me-2"></i> Loading data…
                            </td></tr>
                        </tbody>
                    </table>
                </div>
                <div id="noModalResult" class="text-center py-5 text-muted fst-italic d-none">
                    <i class="fas fa-search fa-2x d-block mb-3 opacity-25"></i> Vehicle not found.
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light flex-wrap gap-2">
                <span id="modal-pagination-info" class="text-muted" style="font-size:13px;"></span>
                <div id="modal-pagination-buttons" class="d-flex gap-1 flex-wrap"></div>
            </div>
        </div>
    </div>
</div>

{{-- BOOKING FORM MODAL --}}
<div class="modal fade" id="rentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-blue">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-plus me-2"></i> Booking: <span id="rent-vehicle-name"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('bookings.store') }}" method="POST" enctype="multipart/form-data" id="rentForm">
                    @csrf
                    <input type="hidden" name="vehicle_id"    id="rent-vehicle-id">
                    <input type="hidden" name="payment_type"  id="rent-payment-type" value="full">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="field-label">Customer</label>
                            <select name="customer_id" class="form-select select2-rent" required>
                                <option value="">— Select Customer —</option>
                                @foreach(\App\Models\Customer::all() as $cust)
                                    <option value="{{ $cust->id }}">{{ $cust->customer_name }} ({{ $cust->customer_id }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="field-label">Address</label>
                            <input type="text" name="address" class="form-control" placeholder="Customer address…" required>
                        </div>
                        <div class="col-12">
                            <label class="field-label">Phone Number</label>
                            <input type="text" name="phone_number" class="form-control" placeholder="Phone number…" required>
                        </div>
                        <div class="col-12">
                            <label class="field-label">ID Card Photo</label>
                            <div id="rent-ktp-preview-box" style="display:none;margin-bottom:8px;border:2px dashed #bfdbfe;border-radius:10px;overflow:hidden;">
                                <div style="background:#eff6ff;padding:6px 10px;font-size:11px;color:#1e40af;font-weight:600;">
                                    <i class="fas fa-id-card me-1"></i> ID card loaded automatically from customer data. Upload new to replace.
                                </div>
                                <img id="rent-ktp-preview" src="" style="width:100%;max-height:130px;object-fit:cover;">
                            </div>
                            <input type="hidden" name="identity_card_base64" id="rent-ktp-base64">
                            <div id="rent-ktp-upload-box">
                                <input type="file" name="identity_card" id="rent-ktp-file" class="form-control"
                                       accept="image/png,image/jpg,image/jpeg,image/webp">
                                <small class="text-muted">PNG, JPG, JPEG, WEBP</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="field-label">Payment Proof</label>
                            <input type="file" name="payment_proof" class="form-control"
                                   accept="image/png,image/jpg,image/jpeg,image/webp" required>
                            <small class="text-muted">PNG, JPG, JPEG, WEBP</small>
                        </div>
                        <div class="col-12">
                            <label class="field-label">Start Date</label>
                            <input type="date" name="start_date" id="rent-start-date" class="form-control"
                                   min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="field-label">End Date</label>
                            <input type="date" name="end_date" id="rent-end-date" class="form-control"
                                   min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="field-label">Payment Method</label>
                            <div class="pay-type-group">
                                <label class="pay-type-card sel-full" id="card-full" for="pay-full">
                                    <input type="radio" id="pay-full" name="_payment_type_ui" value="full" checked>
                                    <div class="pt-icon">💳</div>
                                    <div class="pt-title">Full Payment</div>
                                    <div class="pt-sub">Pay in full upfront</div>
                                </label>
                                <label class="pay-type-card" id="card-dp" for="pay-dp">
                                    <input type="radio" id="pay-dp" name="_payment_type_ui" value="dp">
                                    <div class="pt-icon">💰</div>
                                    <div class="pt-title">Down Payment 50%</div>
                                    <div class="pt-sub">Pay half, rest upon return</div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="rent-cost-summary" class="cost-box full">
                        <div class="cost-row">
                            <span class="cl">Duration</span>
                            <span class="cv" id="cs-durasi">— days</span>
                        </div>
                        <div class="cost-row">
                            <span class="cl">Price / Day</span>
                            <span class="cv" id="cs-harga">Rp 0</span>
                        </div>
                        <div class="cost-row">
                            <span class="cl">Total Cost</span>
                            <span class="cv" id="cs-total">Rp 0</span>
                        </div>
                        <div class="cost-divider"></div>
                        <div class="cost-row hi" id="cs-hi-row">
                            <span class="cl" id="cs-bayar-label">Amount Paid Now</span>
                            <span class="cv" id="cs-bayar-value">Rp 0</span>
                        </div>
                        <div class="cost-row d-none" id="cs-sisa-row">
                            <span class="cl" style="color:#6b7280;">Remaining upon return</span>
                            <span class="cv" style="color:#6b7280;" id="cs-sisa-value">Rp 0</span>
                        </div>
                    </div>
                    <button type="submit" class="btn w-100 fw-bold text-white py-2 mt-4"
                        style="background:linear-gradient(135deg,#1a237e,#1565c0);border:none;border-radius:9px;font-size:15px;">
                        <i class="fas fa-check-circle me-2"></i> Confirm Booking
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- RETURN VEHICLE MODAL --}}
<div class="modal fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-blue">
                <h5 class="modal-title">
                    <i class="fas fa-undo me-2"></i> Return: <span id="return-vehicle-name"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="info-box mb-4">
                    <p><b>Customer:</b> <span id="return-customer"></span></p>
                    <p><b>Plate:</b> <span id="return-plate"></span></p>
                    <p><b>Booking ID:</b> <span id="return-booking-id-text"></span></p>
                    <p class="mb-0"><b>End Date:</b> <span id="return-enddate"></span></p>
                </div>
                <form action="{{ route('returns.store') }}" method="POST" id="returnFormModal">
                    @csrf
                    <input type="hidden" name="booking_id" id="return-booking-id">
                    <div class="mb-4">
                        <label class="field-label">Vehicle Condition</label>
                        <select name="vehicle_condition" class="form-select mt-1">
                            <option value="Good">Good</option>
                            <option value="Minor Damage">Minor Damage</option>
                            <option value="Major Damage">Major Damage</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn flex-fill fw-bold text-white py-2"
                            style="background:linear-gradient(135deg,#1a237e,#1565c0);border:none;border-radius:9px;">
                            <i class="fas fa-check-circle me-2"></i> Process Return
                        </button>
                        <button type="button" class="btn btn-outline-secondary flex-fill"
                            data-bs-dismiss="modal" style="border-radius:9px;">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

// ═══════════════════════════════════════════════════════════
//  🔔  TOAST NOTIFICATIONS
// ═══════════════════════════════════════════════════════════
/* BARU - ganti dengan ini */
function showToast(type, icon, pesan, durasi) {
    durasi = durasi || 4000;
    var box  = document.getElementById('toastBox');
    var item = document.createElement('div');
    item.className = 'toast-item toast-' + type;
    item.innerHTML = icon
        + '<span style="flex:1">' + pesan + '</span>'
        + '<button class="toast-close-btn" onclick="tutupToast(this.parentElement)">&#x2715;</button>'
        + '<div class="toast-bar" style="animation-duration:' + durasi + 'ms"></div>';
    box.appendChild(item);
    setTimeout(function () { tutupToast(item); }, durasi);
}

function tutupToast(el) {
    el.classList.add('hide');
    setTimeout(function () { el.remove(); }, 320);
}

// ═══════════════════════════════════════════════════════════
//  🚫  CANCEL MODAL
// ═══════════════════════════════════════════════════════════
function bukaModalBatal(id) {
    var input = document.getElementById('cancelReasonValue' + id);
    if (input) {
        input.value = '';
        input.style.borderColor = '#e5e7eb';
    }
    var modal = new bootstrap.Modal(document.getElementById('cancelModal' + id));
    modal.show();
}

function konfirmasiBatal(id, namaKendaraan, namaPelanggan) {
    var hidden = document.getElementById('cancelReasonValue' + id);

    if (!hidden.value.trim()) {
        hidden.classList.add('field-shake');
        hidden.style.borderColor = '#dc2626';
        hidden.focus();
        setTimeout(function () { hidden.classList.remove('field-shake'); }, 400);
        showToast('error', '<i class="fas fa-exclamation-circle"></i>', 'Please fill in the cancellation reason first!');
        return;
    }

    // ✅ Sembunyikan modal DULU sebelum tampilkan overlay
    var bsModal = bootstrap.Modal.getInstance(document.getElementById('cancelModal' + id));
    if (bsModal) bsModal.hide();

    var overlay = document.createElement('div');
    overlay.className = 'konfirm-overlay';
    var infoTambahan = namaPelanggan ? ' by <strong>' + namaPelanggan + '</strong>' : '';
    overlay.innerHTML =
        '<div class="konfirm-box">'
      +   '<div class="konfirm-icon">⚠️</div>'
      +   '<div class="konfirm-title">Confirm Cancellation</div>'
      +   '<div class="konfirm-sub">'
      +     'You are about to cancel the rental for<br>'
      +     '<strong>' + namaKendaraan + '</strong>' + infoTambahan + '.'
      +     '<br><br>'
      +     '<span style="color:#dc2626;font-weight:600;">Reason: ' + hidden.value + '</span>'
      +     '<br><br>'
      +     'This action <strong>cannot be undone</strong>. Continue?'
      +   '</div>'
      +   '<div class="konfirm-actions">'
      +     '<button class="btn btn-outline-secondary" id="konfirmNo">No, Go Back</button>'
      +     '<button class="btn btn-danger" id="konfirmYes"><i class="fas fa-times-circle me-1"></i> Yes, Cancel</button>'
      +   '</div>'
      + '</div>';
    document.body.appendChild(overlay);

    document.getElementById('konfirmNo').addEventListener('click', function () {
        overlay.remove();
        // ✅ Buka lagi modal cancel kalau user pilih "No, Go Back"
        if (bsModal) bsModal.show();
    });
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) {
            overlay.remove();
            if (bsModal) bsModal.show(); // ✅ Buka lagi kalau klik di luar
        }
    });

    document.getElementById('konfirmYes').addEventListener('click', function () {
        overlay.remove();
        showToast('info', '<i class="fas fa-spinner fa-spin"></i>', 'Processing cancellation…');
        var redirectInput   = document.createElement('input');
        redirectInput.type  = 'hidden';
        redirectInput.name  = 'redirect_to';
        redirectInput.value = 'cancel_history';
        document.getElementById('cancelForm' + id).appendChild(redirectInput);
        document.getElementById('cancelForm' + id).submit();
    });
}

document.querySelectorAll('[id^="cancelReasonValue"]').forEach(function (el) {
    el.addEventListener('input', function () { this.style.borderColor = '#e5e7eb'; });
});

// ═══════════════════════════════════════════════════════════
//  🔄  RETURN MODAL
// ═══════════════════════════════════════════════════════════
$('#returnModal').on('show.bs.modal', function (e) {
    var b = e.relatedTarget;
    document.getElementById('return-vehicle-name').textContent    = b.dataset.vehicleName;
    document.getElementById('return-customer').textContent        = b.dataset.customer;
    document.getElementById('return-plate').textContent           = b.dataset.plate;
    document.getElementById('return-booking-id-text').textContent = '#' + b.dataset.bookingId;
    document.getElementById('return-booking-id').value            = b.dataset.bookingId;
    document.getElementById('return-enddate').textContent         = b.dataset.endDate;
});

// ═══════════════════════════════════════════════════════════
//  🏍️  BOOKING MODAL
// ═══════════════════════════════════════════════════════════
var rentPrice = 0;

$('.select2-rent').select2({
    placeholder: 'Search customer…',
    dropdownParent: $('#rentModal')
});

$('#rentModal').on('show.bs.modal', function (e) {
    var b = e.relatedTarget;
    document.getElementById('rent-vehicle-id').value         = b.dataset.vehicleId   || '';
    document.getElementById('rent-vehicle-name').textContent = b.dataset.vehicleName || '';
    rentPrice = parseFloat(b.dataset.pricePerDay || 0);
    document.getElementById('rent-start-date').value           = '';
    document.getElementById('rent-end-date').value             = '';
    document.getElementById('pay-full').checked                = true;
    document.getElementById('rent-payment-type').value         = 'full';
    document.getElementById('card-full').className             = 'pay-type-card sel-full';
    document.getElementById('card-dp').className               = 'pay-type-card';
    document.getElementById('rent-cost-summary').className     = 'cost-box full';
    document.getElementById('rent-ktp-preview-box').style.display = 'none';
    document.getElementById('rent-ktp-preview').src                = '';
    document.getElementById('rent-ktp-base64').value               = '';
    document.getElementById('rent-ktp-file').value                 = '';
    document.getElementById('rent-ktp-upload-box').style.display   = 'block';
    $('.select2-rent').val('').trigger('change');
    updateCostSummary();
});

$('.select2-rent').on('select2:select', function () {
    var id = $(this).val();
    if (!id) return;
    fetch('/booking/customer/' + id)
        .then(function (r) { return r.json(); })
        .then(function (data) {
            document.querySelector('#rentForm input[name="phone_number"]').value = data.phone_number || '';
            document.querySelector('#rentForm input[name="address"]').value      = data.address      || '';
            var previewBox = document.getElementById('rent-ktp-preview-box');
            var preview    = document.getElementById('rent-ktp-preview');
            var b64Input   = document.getElementById('rent-ktp-base64');
            if (data.ktp_photo) {
                var imgUrl = '/ktp/' + data.ktp_photo;
                preview.src = imgUrl;
                previewBox.style.display = 'block';
                document.getElementById('rent-ktp-upload-box').style.display = 'none';
                fetch(imgUrl).then(function (r) { return r.blob(); }).then(function (blob) {
                    var reader = new FileReader();
                    reader.onload = function (e) { b64Input.value = e.target.result; };
                    reader.readAsDataURL(blob);
                });
            } else {
                previewBox.style.display = 'none';
                preview.src = '';
                b64Input.value = '';
                document.getElementById('rent-ktp-upload-box').style.display = 'block';
            }
        });
});

['rent-start-date', 'rent-end-date'].forEach(function (id) {
    document.getElementById(id).addEventListener('change', updateCostSummary);
});

document.querySelectorAll('input[name="_payment_type_ui"]').forEach(function (radio) {
    radio.addEventListener('change', function () {
        var isDP = this.value === 'dp';
        document.getElementById('rent-payment-type').value         = this.value;
        document.getElementById('card-full').className             = 'pay-type-card' + (!isDP ? ' sel-full' : '');
        document.getElementById('card-dp').className               = 'pay-type-card' + ( isDP ? ' sel-dp'   : '');
        document.getElementById('rent-cost-summary').className     = 'cost-box ' + (isDP ? 'dp' : 'full');
        updateCostSummary();
    });
});

function updateCostSummary() {
    var start = document.getElementById('rent-start-date').value;
    var end   = document.getElementById('rent-end-date').value;
    var isDP  = document.getElementById('pay-dp').checked;
    if (start) document.getElementById('rent-end-date').min = start;
    var days = 0, total = 0;
    if (start && end && end >= start) {
        days  = Math.floor((new Date(end) - new Date(start)) / 86400000) + 1;
        total = days * rentPrice;
    }
    var fmt = function (v) { return 'Rp ' + v.toLocaleString('id-ID'); };
    document.getElementById('cs-durasi').textContent = days > 0 ? days + ' days' : '— days';
    document.getElementById('cs-harga').textContent  = fmt(rentPrice);
    document.getElementById('cs-total').textContent  = fmt(total);
    var hiRow = document.getElementById('cs-hi-row');
    var siRow = document.getElementById('cs-sisa-row');
    if (isDP) {
        document.getElementById('cs-bayar-label').textContent = 'Down Payment (50%)';
        document.getElementById('cs-bayar-value').textContent = fmt(total * .5);
        document.getElementById('cs-sisa-value').textContent  = fmt(total * .5);
        hiRow.className = 'cost-row hi-dp';
        siRow.classList.remove('d-none');
    } else {
        document.getElementById('cs-bayar-label').textContent = 'Amount Paid Now';
        document.getElementById('cs-bayar-value').textContent = fmt(total);
        hiRow.className = 'cost-row hi';
        siRow.classList.add('d-none');
    }
}

function calcPenalty(id, remaining, latePenalty, lateDays, type) {
    var condition     = document.getElementById('conditionSelect' + id).value;
    var multiplier    = type === 'sport' ? 2 : type === 'trail' ? 1.5 : 1;
    var damagePenalty = 0, damageDesc = '—';
    if (condition === 'Minor Damage') { damagePenalty = 150000 * multiplier; damageDesc = 'Minor (Rp 150.000 x ' + multiplier + ')'; }
    if (condition === 'Major Damage') { damagePenalty = 500000 * multiplier; damageDesc = 'Major (Rp 500.000 x '  + multiplier + ')'; }
    var totalPenalty = latePenalty + damagePenalty;
    var grandTotal   = remaining + totalPenalty;
    document.getElementById('conditionHidden'      + id).value       = condition;
    document.getElementById('damagePenaltyLabel'   + id).textContent = 'Rp ' + damagePenalty.toLocaleString('id-ID');
    document.getElementById('damageDesc'           + id).textContent = damageDesc;
    document.getElementById('totalPenaltyLabel'    + id).textContent = 'Rp ' + totalPenalty.toLocaleString('id-ID');
    document.getElementById('grandTotal'           + id).textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
}

// ═══════════════════════════════════════════════════════════
//  🚗  VEHICLE SELECTION TABLE (MODAL)
// ═══════════════════════════════════════════════════════════
var typeLabels = {
    @foreach($vehicleTypes as $vt)
    '{{ $vt->name }}': '{{ $vt->label }}',
    @endforeach
};

function loadModalVehicles(page) {
    page = page || 1;
    var search = document.getElementById('modalSearch').value;
    var type   = document.getElementById('modalTypeFilter').value;
    var status = document.getElementById('modalStatusFilter').value;
    fetch('/booking/vehicles-json?page=' + page
        + '&search=' + encodeURIComponent(search)
        + '&type='   + encodeURIComponent(type)
        + '&status=' + encodeURIComponent(status))
        .then(function (r) { return r.json(); })
        .then(function (data) {
            renderModalRows(data);
            renderModalPagination(data);
        });
}

function renderModalRows(data) {
    var tbody    = document.getElementById('modalTableBody');
    var noResult = document.getElementById('noModalResult');
    if (!data.data || data.data.length === 0) {
        tbody.innerHTML = '';
        noResult.classList.remove('d-none');
        return;
    }
    noResult.classList.add('d-none');
    var typeBadge = function (t) {
        var label = typeLabels[t] || t.charAt(0).toUpperCase() + t.slice(1);
        return '<span class="badge bg-primary px-2 py-1">' + label + '</span>';
    };
    tbody.innerHTML = data.data.map(function (v, i) {
        var rented    = v.status.toLowerCase() === 'rented';
        var actionBtn = rented
            ? '<button class="btn btn-sm btn-secondary fw-semibold" disabled><i class="fas fa-ban me-1"></i> Rented</button>'
            : '<button type="button" class="btn btn-sm btn-primary fw-semibold"'
              + ' data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#rentModal"'
              + ' data-vehicle-id="'    + v.id          + '"'
              + ' data-vehicle-name="'  + v.name        + '"'
              + ' data-price-per-day="' + v.price_per_day + '"'
              + ' style="background:linear-gradient(135deg,#1565c0,#1976d2);border:none;border-radius:7px;">'
              + '<i class="fas fa-key me-1"></i> Rent</button>';
        return '<tr>'
            + '<td class="text-muted fw-semibold">' + (data.from + i) + '</td>'
            + '<td><img src="/image/' + (v.image || 'default.png') + '" class="vehicle-thumb"></td>'
            + '<td class="fw-semibold text-start">' + v.name + '</td>'
            + '<td>' + typeBadge(v.type) + '</td>'
            + '<td><span class="plate-mono">' + v.plate_number + '</span></td>'
            + '<td class="fw-semibold text-success">Rp ' + parseInt(v.price_per_day).toLocaleString('id-ID') + '</td>'
            + '<td>' + (rented ? '<span class="chip-rented">Rented</span>' : '<span class="chip-available">Available</span>') + '</td>'
            + '<td>' + actionBtn + '</td>'
            + '</tr>';
    }).join('');
}

function renderModalPagination(data) {
    var info = document.getElementById('modal-pagination-info');
    var btns = document.getElementById('modal-pagination-buttons');
    var to   = data.from + data.data.length - 1;
    info.textContent = data.total > 0
        ? 'Showing ' + data.from + '–' + to + ' of ' + data.total + ' vehicles'
        : '';
    if (data.last_page <= 1) { btns.innerHTML = ''; return; }
    var html = '<button class="btn btn-sm btn-outline-secondary" '
             + (data.current_page === 1 ? 'disabled' : '')
             + ' onclick="loadModalVehicles(' + (data.current_page - 1) + ')">&laquo;</button>';
    for (var p = 1; p <= data.last_page; p++) {
        html += '<button class="btn btn-sm '
              + (p === data.current_page ? 'btn-primary' : 'btn-outline-primary')
              + '" onclick="loadModalVehicles(' + p + ')">' + p + '</button>';
    }
    html += '<button class="btn btn-sm btn-outline-secondary" '
          + (data.current_page === data.last_page ? 'disabled' : '')
          + ' onclick="loadModalVehicles(' + (data.current_page + 1) + ')">&raquo;</button>';
    btns.innerHTML = html;
}

function filterModal() { loadModalVehicles(1); }

document.getElementById('addRentModal').addEventListener('show.bs.modal', function () {
    document.getElementById('modalSearch').value       = '';
    document.getElementById('modalTypeFilter').value   = '';
    document.getElementById('modalStatusFilter').value = '';
    document.getElementById('modalTableBody').innerHTML =
        '<tr><td colspan="8" class="text-center py-5 text-muted">'
      + '<i class="fas fa-spinner fa-spin me-2"></i> Loading data…</td></tr>';
    document.getElementById('noModalResult').classList.add('d-none');
    loadModalVehicles(1);
});

</script>
@endpush