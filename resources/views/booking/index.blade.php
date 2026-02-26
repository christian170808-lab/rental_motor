@extends('layouts.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
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
.bk-filter {
    background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
    padding: 12px 14px; margin-bottom: 18px; box-shadow: 0 2px 8px rgba(0,0,0,.05);
}
.bk-filter .form-control, .bk-filter .form-select {
    border: 1.5px solid #e5e7eb; border-radius: 8px; font-size: 13.5px;
    padding: 8px 12px; transition: border-color .18s;
}
.bk-filter .form-control:focus, .bk-filter .form-select:focus { border-color: #1565c0; box-shadow: none; }
.btn-search {
    background: linear-gradient(135deg, #1565c0, #1976d2);
    color: #fff; border: none; border-radius: 8px;
    font-weight: 600; padding: 8px 22px; font-size: 13.5px; transition: all .18s;
}
.btn-search:hover { box-shadow: 0 4px 12px rgba(21,101,192,.35); transform: translateY(-1px); color: #fff; }
.btn-add-rent {
    background: linear-gradient(135deg, #16a34a, #15803d);
    color: #fff; border: none; border-radius: 8px; font-weight: 700;
    padding: 8px 18px; font-size: 13.5px; transition: all .18s; white-space: nowrap;
    display: inline-flex; align-items: center; gap: 6px;
}
.btn-add-rent:hover { box-shadow: 0 4px 12px rgba(22,163,74,.35); transform: translateY(-1px); color: #fff; }
.bk-table-wrap {
    background: #fff; border-radius: 12px; border: 1px solid #e5e7eb;
    overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.06); margin-bottom: 20px;
}
.bk-table { width: 100%; margin: 0; border-collapse: collapse; }
.bk-table thead th {
    background: linear-gradient(90deg, #1a237e 0%, #1565c0 100%);
    color: #fff; font-size: 12px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .6px; padding: 13px 16px; border: none; text-align: center; white-space: nowrap;
}
.bk-table tbody td {
    padding: 14px 16px; vertical-align: middle; font-size: 14px;
    border-bottom: 1px solid #f1f5f9; text-align: center; white-space: nowrap; color: #1f2937;
}
.bk-table tbody tr:last-child td { border-bottom: none; }
.bk-table tbody tr:hover { background: #f0f7ff; }
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
.btn-act {
    display: inline-flex; align-items: center; gap: 5px; font-size: 13px; font-weight: 600;
    padding: 6px 13px; border-radius: 7px; border: none; cursor: pointer;
    transition: all .15s; white-space: nowrap; line-height: 1.4;
}
.btn-act:hover { transform: translateY(-1px); filter: brightness(1.1); }
.btn-act.view   { background: #06b6d4; color: #fff; box-shadow: 0 2px 6px rgba(6,182,212,.3); }
.btn-act.edit   { background: #f59e0b; color: #fff; box-shadow: 0 2px 6px rgba(245,158,11,.3); }
.btn-act.ret    { background: #3b82f6; color: #fff; box-shadow: 0 2px 6px rgba(59,130,246,.3); }
.btn-act.payoff { background: #10b981; color: #fff; box-shadow: 0 2px 6px rgba(16,185,129,.3); }
.btn-act.del    { background: #ef4444; color: #fff; box-shadow: 0 2px 6px rgba(239,68,68,.3); }
.vehicle-thumb { width: 68px; height: 50px; object-fit: cover; border-radius: 7px; border: 1px solid #e5e7eb; }
.chip-available { background: #dcfce7; color: #15803d; font-size: 12px; font-weight: 700; padding: 4px 13px; border-radius: 20px; display: inline-block; }
.chip-rented    { background: #fee2e2; color: #dc2626; font-size: 12px; font-weight: 700; padding: 4px 13px; border-radius: 20px; display: inline-block; }
.bk-empty {
    background: #fff; border-radius: 12px; border: 1px solid #e5e7eb;
    padding: 60px 20px; text-align: center; box-shadow: 0 2px 12px rgba(0,0,0,.05);
}
.bk-empty i { font-size: 3rem; color: #d1d5db; margin-bottom: 14px; display: block; }
.modal .modal-content { border: none; border-radius: 14px; box-shadow: 0 20px 60px rgba(0,0,0,.2); overflow: hidden; }
.modal-header-blue { background: linear-gradient(135deg, #1a237e, #1565c0); border: none; }
.modal-header-blue .modal-title, .modal-header-blue h5 { color: #fff; font-weight: 700; margin: 0; font-size: 1.05rem; }
.modal-header-red { background: linear-gradient(135deg, #991b1b, #dc2626); border: none; }
.field-label { font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 5px; display: block; }
.field-value { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 9px 13px; font-size: 14px; color: #111827; min-height: 40px; }
.info-box { background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 10px; padding: 13px 16px; margin-bottom: 18px; }
.info-box p { margin: 0 0 2px; color: #1e40af; font-size: 13.5px; }
.info-box p b { color: #1e3a8a; }
.penalty-box { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 9px; padding: 13px 15px; margin-bottom: 14px; }
.penalty-title { font-size: 11.5px; font-weight: 700; color: #ea580c; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 9px; }
.penalty-row { display: flex; justify-content: space-between; font-size: 13px; color: #92400e; padding: 2px 0; }
.penalty-row.total { border-top: 1px dashed #fed7aa; margin-top: 7px; padding-top: 7px; font-weight: 700; color: #ea580c; font-size: 14px; }
.grand-total-box { background: linear-gradient(135deg, #fef2f2, #fee2e2); border: 1.5px solid #fca5a5; border-radius: 9px; padding: 13px 16px; }
.grand-total-box .gt-label { font-size: 11.5px; font-weight: 700; color: #dc2626; text-transform: uppercase; letter-spacing: .5px; }
.grand-total-box .gt-value { font-size: 21px; font-weight: 800; color: #b91c1c; margin-top: 2px; }
.delete-warning { background: linear-gradient(135deg, #fef2f2, #fee2e2); border: 1px solid #fca5a5; border-radius: 11px; padding: 18px; text-align: center; margin-bottom: 14px; }
.delete-warning .dw-icon { font-size: 2.5rem; color: #dc2626; margin-bottom: 8px; }
.delete-warning .dw-text { font-size: 14.5px; color: #7f1d1d; font-weight: 600; }
.delete-warning .dw-name { font-size: 17px; font-weight: 800; color: #991b1b; margin-top: 5px; }
.delete-warning .dw-note { font-size: 12px; color: #b91c1c; margin-top: 7px; }
.pay-type-group { display: flex; gap: 10px; }
.pay-type-card {
    flex: 1; border: 2px solid #e5e7eb; border-radius: 10px;
    padding: 11px 12px; cursor: pointer; text-align: center;
    background: #f9fafb; transition: border-color .18s, background .18s;
}
.pay-type-card input[type="radio"] { display: none; }
.pay-type-card:hover { border-color: #bfdbfe; background: #eff6ff; }
.pay-type-card.sel-full { border-color: #16a34a; background: #f0fdf4; }
.pay-type-card.sel-dp   { border-color: #1d4ed8; background: #eff6ff; }
.pay-type-card .pt-icon  { font-size: 1.3rem; }
.pay-type-card .pt-title { font-weight: 700; font-size: 13.5px; color: #1f2937; margin-top: 4px; }
.pay-type-card .pt-sub   { font-size: 11px; color: #6b7280; }
.cost-box { border-radius: 10px; padding: 13px 15px; margin-top: 13px; font-size: 13.5px; }
.cost-box.full { background: #f0fdf4; border: 1.5px solid #86efac; }
.cost-box.dp   { background: #eff6ff; border: 1.5px solid #bfdbfe; }
.cost-row { display: flex; justify-content: space-between; padding: 3px 0; }
.cost-row .cl { color: #374151; }
.cost-row .cv { font-weight: 700; color: #111827; }
.cost-row.hi    .cl, .cost-row.hi    .cv { color: #16a34a; font-size: 15px; font-weight: 800; }
.cost-row.hi-dp .cl, .cost-row.hi-dp .cv { color: #1d4ed8; font-size: 15px; font-weight: 800; }
.cost-divider { border-top: 1px dashed #d1d5db; margin: 6px 0; }
.modal-veh-table { width: 100%; margin: 0; border-collapse: collapse; }
.modal-veh-table thead th {
    background: linear-gradient(90deg, #1a237e, #1565c0); color: #fff;
    font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
    padding: 12px 14px; border: none; text-align: center;
}
.modal-veh-table td {
    padding: 11px 14px; vertical-align: middle;
    font-size: 13.5px; border-bottom: 1px solid #f1f5f9; text-align: center;
}
.modal-veh-table tbody tr:hover { background: #f0f7ff; }
</style>
@endpush

@section('content')
<div class="container-fluid" style="max-width: 1400px;">

    {{-- PAGE HEADER --}}
    <div class="bk-header">
        <div class="bk-icon"><i class="fas fa-motorcycle"></i></div>
        <div>
            <h2>Vehicle Management</h2>
            <p>Manage all active rentals and vehicle returns</p>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <form action="{{ route('booking.index') }}" method="GET" class="bk-filter">
        <div class="row g-2 align-items-center justify-content-between">
            <div class="col-md-8 d-flex gap-2">
                <div class="input-group">
                    <input type="text" name="search" class="form-control"
                        placeholder="Search by name or plate…" value="{{ request('search') }}">
                    <button type="submit" class="btn-search">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </div>
                <select name="type" class="form-select" onchange="this.form.submit()" style="max-width:140px;">
                    <option value="">All Types</option>
                    <option value="scooter" {{ request('type') == 'scooter' ? 'selected' : '' }}>Scooter</option>
                    <option value="sport"   {{ request('type') == 'sport'   ? 'selected' : '' }}>Sport</option>
                    <option value="trail"   {{ request('type') == 'trail'   ? 'selected' : '' }}>Trail</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2 justify-content-end align-items-center">
                <select name="status" class="form-select" onchange="this.form.submit()" style="max-width:150px;">
                    <option value="">All Status</option>
                    <option value="active"    {{ request('status') == 'active'    ? 'selected' : '' }}>Active</option>
                    <option value="paid"      {{ request('status') == 'paid'      ? 'selected' : '' }}>Paid</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                <button type="button" class="btn-add-rent" data-bs-toggle="modal" data-bs-target="#addRentModal">
                    <i class="fas fa-plus"></i> Add Rent
                </button>
            </div>
        </div>
    </form>

    {{-- ACTIVE RENTALS TABLE --}}
    @if($rentedVehicles->isEmpty())
    <div class="bk-empty">
        <i class="fas fa-motorcycle opacity-25"></i>
        <p class="fw-semibold mb-1 text-muted">No active rentals</p>
        <small class="text-muted">Click <strong>+ Add Rent</strong> to start a new rental.</small>
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
                        $ab       = $v->bookings->where('payment_status', 'paid')
                                     ->whereIn('payment_type', ['dp', 'full'])
                                     ->sortByDesc('id')->first();
                        $customer = $ab ? $ab->customer : null;
                        $dp       = $ab ? $ab->total_cost * .5 : null;
                        $sisa     = $dp;
                        $durasi   = $ab ? \Carbon\Carbon::parse($ab->start_date)->diffInDays(\Carbon\Carbon::parse($ab->end_date)) + 1 : null;
                        $cName    = optional($customer)->customer_name ?? '';
                        $cPhone   = optional($customer)->phone_number  ?? '';
                        $isFull   = $ab && $ab->payment_type === 'full';
                        $lateDays = 0; $latePenalty = 0;
                        if ($ab) {
                            $ed = \Carbon\Carbon::parse($ab->end_date)->startOfDay();
                            $td = \Carbon\Carbon::now()->startOfDay();
                            if ($td->gt($ed)) { $lateDays = $ed->diffInDays($td); $latePenalty = $lateDays * 50000; }
                        }
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $cName ?: '—' }}</td>
                        <td>{{ $cPhone ?: '—' }}</td>
                        <td>{{ $v->type ? ucfirst($v->type) : '—' }}</td>
                        <td><span class="plate-mono">{{ $ab ? $v->plate_number : '—' }}</span></td>
                        <td>
                            @if($durasi)
                                <span style="display:inline-flex;align-items:center;gap:5px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:20px;padding:5px 13px;font-size:13px;font-weight:600;color:#475569;">
                                    <i class="fas fa-clock" style="color:#94a3b8;font-size:12px;"></i> {{ $durasi }} days
                                </span>
                            @else —
                            @endif
                        </td>
                        <td>
                            @if($ab)
                                @if($isFull)
                                    <span class="badge-paid"><i class="fas fa-check-circle"></i> Paid Off</span>
                                @else
                                    <span class="badge-dp"><i class="fas fa-clock"></i> DP 50%</span>
                                    <div class="mt-1" style="font-size:12px;color:#1d4ed8;font-weight:600;">
                                        Rp {{ number_format($dp, 0, ',', '.') }}
                                    </div>
                                @endif
                            @else —
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
                                    <i class="fas fa-eye"></i> View
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
                                        style="opacity:.45; cursor:not-allowed; pointer-events:none;" disabled>
                                        <i class="fas fa-lock"></i> Return
                                    </button>
                                    @endif
                                @endif
                                <button type="button" class="btn-act del"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal{{ $v->id }}">
                                    <i class="fas fa-trash"></i> Delete
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
                                        <div class="col-md-6"><label class="field-label">Customer</label><div class="field-value">{{ $cName ?: '—' }}</div></div>
                                        <div class="col-md-6"><label class="field-label">Phone Number</label><div class="field-value">{{ $cPhone ?: '—' }}</div></div>
                                        <div class="col-md-6"><label class="field-label">Vehicle</label><div class="field-value">{{ $v->name }}</div></div>
                                        <div class="col-md-6"><label class="field-label">Plate Number</label><div class="field-value">{{ $v->plate_number }}</div></div>
                                        <div class="col-md-6">
                                            <label class="field-label">Rental Period</label>
                                            <div class="field-value">
                                                @if($ab)
                                                    {{ \Carbon\Carbon::parse($ab->start_date)->format('d M Y') }} — {{ \Carbon\Carbon::parse($ab->end_date)->format('d M Y') }}
                                                @else —
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6"><label class="field-label">Duration</label><div class="field-value">{{ $durasi ? $durasi . ' days' : '—' }}</div></div>
                                        <div class="col-md-6"><label class="field-label">Total Cost</label><div class="field-value fw-bold text-primary">{{ $ab ? 'Rp ' . number_format($ab->total_cost, 0, ',', '.') : '—' }}</div></div>
                                        <div class="col-md-6">
                                            <label class="field-label">Payment Status</label>
                                            <div class="field-value">
                                                @if($isFull)
                                                    <span class="badge-paid"><i class="fas fa-check-circle"></i> Paid Off</span>
                                                @else
                                                    <span class="badge-dp"><i class="fas fa-clock"></i> DP 50% — Rp {{ number_format($dp, 0, ',', '.') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if(!$isFull && $sisa)
                                        <div class="col-md-6"><label class="field-label">Remaining Balance</label><div class="field-value fw-bold text-danger">Rp {{ number_format($sisa, 0, ',', '.') }}</div></div>
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
                                        <div class="mb-3"><label class="field-label">Phone Number</label><input type="text" name="phone_number" class="form-control" value="{{ $cPhone }}" required></div>
                                        <div class="mb-3"><label class="field-label">Renter Name</label><input type="text" name="customer_name" class="form-control" value="{{ $cName }}" required></div>
                                        <div class="mb-3"><label class="field-label">Vehicle Type</label><div class="field-value">{{ ucfirst($v->type) }}</div></div>
                                        <div class="mb-3"><label class="field-label">Plate Number</label><div class="field-value">{{ $v->plate_number }}</div></div>
                                        <div class="mb-3"><label class="field-label">Start Date</label><input type="date" name="start_date" class="form-control" value="{{ $ab->start_date }}" required></div>
                                        <div class="mb-4"><label class="field-label">End Date</label><input type="date" name="end_date" class="form-control" value="{{ $ab->end_date }}" required></div>
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

                    {{-- DELETE MODAL --}}
                    <div class="modal fade" id="deleteModal{{ $v->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
                            <div class="modal-content">
                                <div class="modal-header modal-header-red">
                                    <h5 class="modal-title text-white fw-bold mb-0"><i class="fas fa-trash me-2"></i> Delete Record</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="delete-warning">
                                        <div class="dw-icon"><i class="fas fa-exclamation-triangle"></i></div>
                                        <div class="dw-text">Are you sure you want to delete this record?</div>
                                        <div class="dw-name">{{ $v->name }} — {{ $v->plate_number }}</div>
                                        @if($ab)
                                        <div class="dw-note"><i class="fas fa-exclamation-circle me-1"></i> This vehicle is currently rented by <strong>{{ $cName }}</strong>!</div>
                                        @endif
                                        <div class="dw-note">This action cannot be undone.</div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <form action="{{ route('booking.destroy', $v->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger fw-bold">
                                            <i class="fas fa-trash me-1"></i> Yes, Delete
                                        </button>
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
                                    <h5 class="modal-title"><i class="fas fa-money-bill-wave me-2"></i> Pay Off Rental</h5>
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
                                        <div class="col-6"><label class="field-label">Total Rental Cost</label><div class="field-value fw-bold">Rp {{ number_format($ab->total_cost, 0, ',', '.') }}</div></div>
                                        <div class="col-6"><label class="field-label">Already Paid (DP 50%)</label><div class="field-value fw-bold text-success">Rp {{ number_format($dp, 0, ',', '.') }}</div></div>
                                    </div>
                                    <div class="penalty-box">
                                        <div class="penalty-title"><i class="fas fa-exclamation-triangle me-1"></i> Penalty Breakdown</div>
                                        <div class="penalty-row">
                                            <span>Late return <span id="lateDaysLabel{{ $v->id }}">{{ $lateDays > 0 ? '(' . $lateDays . ' days x Rp 50,000)' : '(on time)' }}</span></span>
                                            <span id="latePenaltyLabel{{ $v->id }}">Rp {{ number_format($latePenalty, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="penalty-row">
                                            <span>Damage <span id="damageDesc{{ $v->id }}">—</span></span>
                                            <span id="damagePenaltyLabel{{ $v->id }}">Rp 0</span>
                                        </div>
                                        <div class="penalty-row total">
                                            <span>Total Penalty</span>
                                            <span id="totalPenaltyLabel{{ $v->id }}">Rp {{ number_format($latePenalty, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="grand-total-box">
                                        <div class="gt-label"><i class="fas fa-wallet me-1"></i> Total Amount Due</div>
                                        <div class="gt-value" id="grandTotal{{ $v->id }}">Rp {{ number_format($sisa + $latePenalty, 0, ',', '.') }}</div>
                                        <div style="font-size:11.5px;color:#dc2626;margin-top:4px;">Remaining balance + penalties</div>
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
                                   placeholder="Search by name or plate…" onkeyup="filterModal()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="modalTypeFilter" class="form-select" onchange="filterModal()">
                            <option value="">All Types</option>
                            <option value="scooter">Scooter</option>
                            <option value="sport">Sport</option>
                            <option value="trail">Trail</option>
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
                                <i class="fas fa-spinner fa-spin me-2"></i> Loading...
                            </td></tr>
                        </tbody>
                    </table>
                </div>
                <div id="noModalResult" class="text-center py-5 text-muted fst-italic d-none">
                    <i class="fas fa-search fa-2x d-block mb-3 opacity-25"></i> No vehicles found.
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light flex-wrap gap-2">
                <span id="modal-pagination-info" class="text-muted" style="font-size:13px;"></span>
                <div id="modal-pagination-buttons" class="d-flex gap-1 flex-wrap"></div>
            </div>
        </div>
    </div>
</div>

{{-- RENT BOOKING MODAL --}}
<div class="modal fade" id="rentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-blue">
                <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i> Booking: <span id="rent-vehicle-name"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('bookings.store') }}" method="POST" enctype="multipart/form-data" id="rentForm">
                    @csrf
                    <input type="hidden" name="vehicle_id" id="rent-vehicle-id">
                    <input type="hidden" name="payment_type" id="rent-payment-type" value="full">
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
                            <input type="text" name="address" class="form-control" placeholder="Customer address..." required>
                        </div>
                        <div class="col-12">
                            <label class="field-label">Phone Number</label>
                            <input type="text" name="phone_number" class="form-control" placeholder="Phone number..." required>
                        </div>
                        <div class="col-12">
                            <label class="field-label">ID Card Photo</label>
                            <input type="file" name="identity_card" class="form-control" accept="image/png,image/jpg,image/jpeg,image/webp" required>
                            <small class="text-muted">PNG, JPG, JPEG, WEBP only</small>
                        </div>
                        <div class="col-12">
                            <label class="field-label">Proof of Payment</label>
                            <input type="file" name="payment_proof" class="form-control" accept="image/png,image/jpg,image/jpeg,image/webp" required>
                            <small class="text-muted">PNG, JPG, JPEG, WEBP only</small>
                        </div>
                        <div class="col-12">
                            <label class="field-label">Start Date</label>
                            <input type="date" name="start_date" id="rent-start-date" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="field-label">End Date</label>
                            <input type="date" name="end_date" id="rent-end-date" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="field-label">Payment Method</label>
                            <div class="pay-type-group">
                                <label class="pay-type-card sel-full" id="card-full" for="pay-full">
                                    <input type="radio" id="pay-full" name="_payment_type_ui" value="full" checked>
                                    <div class="pt-icon">💳</div>
                                    <div class="pt-title">Full Payment</div>
                                    <div class="pt-sub">Pay the full amount upfront</div>
                                </label>
                                <label class="pay-type-card" id="card-dp" for="pay-dp">
                                    <input type="radio" id="pay-dp" name="_payment_type_ui" value="dp">
                                    <div class="pt-icon">💰</div>
                                    <div class="pt-title">Down Payment 50%</div>
                                    <div class="pt-sub">Pay half now, rest on return</div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="rent-cost-summary" class="cost-box full">
                        <div class="cost-row"><span class="cl">Duration</span><span class="cv" id="cs-durasi">— days</span></div>
                        <div class="cost-row"><span class="cl">Price / Day</span><span class="cv" id="cs-harga">Rp 0</span></div>
                        <div class="cost-row"><span class="cl">Total Cost</span><span class="cv" id="cs-total">Rp 0</span></div>
                        <div class="cost-divider"></div>
                        <div class="cost-row hi" id="cs-hi-row">
                            <span class="cl" id="cs-bayar-label">Amount Due Now</span>
                            <span class="cv" id="cs-bayar-value">Rp 0</span>
                        </div>
                        <div class="cost-row d-none" id="cs-sisa-row">
                            <span class="cl" style="color:#6b7280;">Remaining on return</span>
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

{{-- RETURN MODAL --}}
<div class="modal fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-blue">
                <h5 class="modal-title"><i class="fas fa-undo me-2"></i> Return: <span id="return-vehicle-name"></span></h5>
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
                        <button type="button" class="btn btn-outline-secondary flex-fill" data-bs-dismiss="modal" style="border-radius:9px;">
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
$('#returnModal').on('show.bs.modal', function (e) {
    const b = e.relatedTarget;
    document.getElementById('return-vehicle-name').textContent    = b.dataset.vehicleName;
    document.getElementById('return-customer').textContent        = b.dataset.customer;
    document.getElementById('return-plate').textContent           = b.dataset.plate;
    document.getElementById('return-booking-id-text').textContent = '#' + b.dataset.bookingId;
    document.getElementById('return-booking-id').value            = b.dataset.bookingId;
    document.getElementById('return-enddate').textContent         = b.dataset.endDate;
});

let rentPrice = 0;
$('#rentModal').on('show.bs.modal', function (e) {
    const b = e.relatedTarget;
    document.getElementById('rent-vehicle-id').value         = b.dataset.vehicleId   || '';
    document.getElementById('rent-vehicle-name').textContent = b.dataset.vehicleName || '';
    rentPrice = parseFloat(b.dataset.pricePerDay || 0);
    document.getElementById('rent-start-date').value = '';
    document.getElementById('rent-end-date').value   = '';
    document.getElementById('pay-full').checked = true;
    document.getElementById('rent-payment-type').value = 'full';
    document.getElementById('card-full').className = 'pay-type-card sel-full';
    document.getElementById('card-dp').className   = 'pay-type-card';
    document.getElementById('rent-cost-summary').className = 'cost-box full';
    updateCostSummary();
    $(this).find('.select2-rent').select2({ placeholder: 'Search customer...', dropdownParent: $('#rentModal') });
});

['rent-start-date','rent-end-date'].forEach(id => {
    document.getElementById(id).addEventListener('change', updateCostSummary);
});

document.querySelectorAll('input[name="_payment_type_ui"]').forEach(radio => {
    radio.addEventListener('change', function () {
        const isDP = this.value === 'dp';
        document.getElementById('rent-payment-type').value = this.value;
        document.getElementById('card-full').className = 'pay-type-card' + (!isDP ? ' sel-full' : '');
        document.getElementById('card-dp').className   = 'pay-type-card' + ( isDP ? ' sel-dp'   : '');
        document.getElementById('rent-cost-summary').className = 'cost-box ' + (isDP ? 'dp' : 'full');
        updateCostSummary();
    });
});

function updateCostSummary() {
    const start = document.getElementById('rent-start-date').value;
    const end   = document.getElementById('rent-end-date').value;
    const isDP  = document.getElementById('pay-dp').checked;
    let days = 0, total = 0;
    if (start && end && end >= start) {
        days  = Math.floor((new Date(end) - new Date(start)) / 86400000) + 1;
        total = days * rentPrice;
    }
    const fmt = v => 'Rp ' + v.toLocaleString('id-ID');
    document.getElementById('cs-durasi').textContent = days > 0 ? days + ' days' : '— days';
    document.getElementById('cs-harga').textContent  = fmt(rentPrice);
    document.getElementById('cs-total').textContent  = fmt(total);
    const hiRow = document.getElementById('cs-hi-row');
    const siRow = document.getElementById('cs-sisa-row');
    if (isDP) {
        document.getElementById('cs-bayar-label').textContent = 'Down Payment Due (50%)';
        document.getElementById('cs-bayar-value').textContent = fmt(total * .5);
        document.getElementById('cs-sisa-value').textContent  = fmt(total * .5);
        hiRow.className = 'cost-row hi-dp';
        siRow.classList.remove('d-none');
    } else {
        document.getElementById('cs-bayar-label').textContent = 'Amount Due Now';
        document.getElementById('cs-bayar-value').textContent = fmt(total);
        hiRow.className = 'cost-row hi';
        siRow.classList.add('d-none');
    }
}

function calcPenalty(id, remaining, latePenalty, lateDays, type) {
    const condition = document.getElementById('conditionSelect' + id).value;
    document.getElementById('conditionHidden' + id).value = condition;
    const multiplier = type === 'sport' ? 2 : type === 'trail' ? 1.5 : 1;
    let damagePenalty = 0, damageDesc = '—';
    if (condition === 'Minor Damage') { damagePenalty = 150000 * multiplier; damageDesc = 'Minor (Rp 150,000 x ' + multiplier + ')'; }
    if (condition === 'Major Damage') { damagePenalty = 500000 * multiplier; damageDesc = 'Major (Rp 500,000 x ' + multiplier + ')'; }
    const totalPenalty = latePenalty + damagePenalty;
    const grandTotal   = remaining + totalPenalty;
    document.getElementById('damagePenaltyLabel' + id).textContent = 'Rp ' + damagePenalty.toLocaleString('id-ID');
    document.getElementById('damageDesc'          + id).textContent = damageDesc;
    document.getElementById('totalPenaltyLabel'   + id).textContent = 'Rp ' + totalPenalty.toLocaleString('id-ID');
    document.getElementById('grandTotal'          + id).textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
}

function loadModalVehicles(page = 1) {
    const search = document.getElementById('modalSearch').value;
    const type   = document.getElementById('modalTypeFilter').value;
    const status = document.getElementById('modalStatusFilter').value;
    fetch('/booking/vehicles-json?page=' + page + '&search=' + encodeURIComponent(search) + '&type=' + encodeURIComponent(type) + '&status=' + encodeURIComponent(status))
        .then(r => r.json())
        .then(data => { renderModalRows(data); renderModalPagination(data); });
}

function renderModalRows(data) {
    const tbody    = document.getElementById('modalTableBody');
    const noResult = document.getElementById('noModalResult');
    if (!data.data || data.data.length === 0) {
        tbody.innerHTML = '';
        noResult.classList.remove('d-none');
        return;
    }
    noResult.classList.add('d-none');
    const typeBadge = t =>
        t === 'scooter' ? '<span class="badge bg-info text-dark px-2 py-1">Scooter</span>' :
        t === 'sport'   ? '<span class="badge bg-danger px-2 py-1">Sport</span>' :
                          '<span class="badge bg-success px-2 py-1">Trail</span>';
    tbody.innerHTML = data.data.map((v, i) => {
        const rented    = v.status.toLowerCase() === 'rented';
        const actionBtn = rented
            ? '<button class="btn btn-sm btn-secondary fw-semibold" disabled><i class="fas fa-ban me-1"></i> Rented</button>'
            : '<button type="button" class="btn btn-sm btn-primary fw-semibold" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#rentModal" data-vehicle-id="' + v.id + '" data-vehicle-name="' + v.name + '" data-price-per-day="' + v.price_per_day + '" style="background:linear-gradient(135deg,#1565c0,#1976d2);border:none;border-radius:7px;"><i class="fas fa-key me-1"></i> Rent</button>';
        return '<tr>' +
            '<td class="text-muted fw-semibold">' + (data.from + i) + '</td>' +
            '<td><img src="/image/' + (v.image || 'default.png') + '" class="vehicle-thumb"></td>' +
            '<td class="fw-semibold text-start">' + v.name + '</td>' +
            '<td>' + typeBadge(v.type) + '</td>' +
            '<td><span class="plate-mono">' + v.plate_number + '</span></td>' +
            '<td class="fw-semibold text-success">Rp ' + parseInt(v.price_per_day).toLocaleString('id-ID') + '</td>' +
            '<td>' + (rented ? '<span class="chip-rented">Rented</span>' : '<span class="chip-available">Available</span>') + '</td>' +
            '<td>' + actionBtn + '</td>' +
            '</tr>';
    }).join('');
}

function renderModalPagination(data) {
    const info = document.getElementById('modal-pagination-info');
    const btns = document.getElementById('modal-pagination-buttons');
    const to   = data.from + data.data.length - 1;
    info.textContent = data.total > 0 ? 'Showing ' + data.from + '-' + to + ' of ' + data.total + ' results' : '';
    if (data.last_page <= 1) { btns.innerHTML = ''; return; }
    let html = '<button class="btn btn-sm btn-outline-secondary" ' + (data.current_page===1?'disabled':'') + ' onclick="loadModalVehicles(' + (data.current_page-1) + ')">&laquo;</button>';
    for (let p = 1; p <= data.last_page; p++) {
        html += '<button class="btn btn-sm ' + (p===data.current_page?'btn-primary':'btn-outline-primary') + '" onclick="loadModalVehicles(' + p + ')">' + p + '</button>';
    }
    html += '<button class="btn btn-sm btn-outline-secondary" ' + (data.current_page===data.last_page?'disabled':'') + ' onclick="loadModalVehicles(' + (data.current_page+1) + ')">&raquo;</button>';
    btns.innerHTML = html;
}

function filterModal() { loadModalVehicles(1); }

document.getElementById('addRentModal').addEventListener('show.bs.modal', function () {
    document.getElementById('modalSearch').value       = '';
    document.getElementById('modalTypeFilter').value   = '';
    document.getElementById('modalStatusFilter').value = '';
    document.getElementById('modalTableBody').innerHTML = '<tr><td colspan="8" class="text-center py-5 text-muted"><i class="fas fa-spinner fa-spin me-2"></i> Loading...</td></tr>';
    document.getElementById('noModalResult').classList.add('d-none');
    loadModalVehicles(1);
});
</script>
@endpush