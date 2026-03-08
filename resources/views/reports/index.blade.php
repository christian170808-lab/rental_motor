@extends('layouts.app')

@push('styles')
<style>
    @keyframes popIn {
    0%   { opacity: 0; transform: scale(.85) translateY(-20px); }
    70%  { transform: scale(1.03) translateY(2px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-30px); }
    to   { opacity: 1; transform: translateY(0); }
}
.modal.show .modal-dialog      { animation: popIn .32s cubic-bezier(.34, 1.56, .64, 1) both; }
.modal.show .modal-header-blue,
.modal.show .modal-header-red  { animation: slideDown .28s ease both .05s; }
.modal-content                 { border: none; border-radius: 16px !important; overflow: hidden; }
.page-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 60%, #1d4ed8 100%);
    border-radius: 16px;
    padding: 22px 28px;
    margin-bottom: 20px;
    box-shadow: 0 8px 32px rgba(37,99,235,0.25);
}
.page-header h2 { color: #fff; font-weight: 700; margin: 0; font-size: 1.5rem; }
.page-header p  { color: rgba(255,255,255,0.7); margin: 4px 0 0; font-size: 0.9rem; }

.header-controls { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

.header-select {
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.35);
    color: #fff; border-radius: 10px; padding: 7px 14px;
    font-size: 14px; font-weight: 500; cursor: pointer;
    backdrop-filter: blur(4px); transition: background 0.2s;
    appearance: none; -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 12px center;
    background-color: rgba(255,255,255,0.15); padding-right: 34px;
}
.header-select:hover  { background-color: rgba(255,255,255,0.25); }
.header-select option { background: #1e3a8a; color: #fff; }
.header-select:focus  { outline: none; border-color: rgba(255,255,255,0.6); }

/* ─── FILTER BAR ─── */
.filter-bar {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    display: flex;
    gap: 10px;
    align-items: flex-end;
    flex-wrap: wrap;
}
.filter-bar label {
    font-size: 11px; font-weight: 700; color: #6b7280;
    text-transform: uppercase; letter-spacing: 0.5px;
    display: block; margin-bottom: 5px;
}
.filter-bar .form-control,
.filter-bar .form-select {
    border: 1.5px solid #e5e7eb; border-radius: 8px;
    font-size: 13.5px; padding: 7px 11px; color: #1f2937; transition: border-color .18s;
}
.filter-bar .form-control:focus,
.filter-bar .form-select:focus { border-color: #1d4ed8; box-shadow: none; outline: none; }
.btn-filter {
    background: linear-gradient(135deg, #1d4ed8, #1e3a8a);
    color: #fff; border: none; border-radius: 8px;
    padding: 8px 20px; font-size: 13.5px; font-weight: 600;
    cursor: pointer; transition: all .18s; white-space: nowrap;
    display: inline-flex; align-items: center; gap: 6px;
}
.btn-filter:hover { box-shadow: 0 4px 12px rgba(29,78,216,.3); transform: translateY(-1px); }
.btn-reset {
    background: #f1f5f9; color: #64748b; border: 1.5px solid #e2e8f0;
    border-radius: 8px; padding: 8px 16px; font-size: 13.5px; font-weight: 600;
    cursor: pointer; transition: all .18s; white-space: nowrap;
    display: inline-flex; align-items: center; gap: 6px; text-decoration: none;
}
.btn-reset:hover { background: #e2e8f0; color: #374151; }

.filter-active-badge {
    display: inline-flex; align-items: center; gap: 5px;
    background: #eff6ff; border: 1px solid #bfdbfe;
    border-radius: 20px; padding: 3px 10px;
    font-size: 12px; color: #1d4ed8; font-weight: 600; margin-bottom: 12px;
}

/* ─── STATS CARDS ─── */
.stats-container {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px; margin-bottom: 24px;
}
.stat-card {
    background: #fff; border-radius: 14px; padding: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07); border: 1px solid #e5e7eb;
    display: flex; align-items: flex-start; gap: 16px;
}
.stat-icon {
    width: 48px; height: 48px; border-radius: 12px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 22px;
}
.stat-icon.revenue      { background: #fef3c7; color: #f59e0b; }
.stat-icon.transactions { background: #d1fae5; color: #10b981; }
.stat-icon.progress     { background: #ede9fe; color: #7c3aed; }
.stat-content { flex: 1; min-width: 0; }
.stat-content h4      { font-size: 13px; color: #6b7280; margin: 0 0 4px; font-weight: 500; }
.stat-content .s-main { font-size: 18px; font-weight: 700; color: #111827; margin: 0; line-height: 1.3; }
.stat-content .s-sub  { font-size: 12px; color: #9ca3af; margin: 2px 0 0; }
.stat-content .s-slash { font-size: 13px; font-weight: 500; color: #9ca3af; }
.prog-wrap   { margin-top: 9px; }
.prog-bg     { width: 100%; height: 6px; background: #e5e7eb; border-radius: 99px; overflow: hidden; }
.prog-fill   { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #7c3aed, #a78bfa); transition: width .5s ease; }
.prog-labels { display: flex; justify-content: space-between; font-size: 11px; color: #6b7280; margin-top: 4px; }

/* ─── TABLE ─── */
.table-card {
    background: #fff; border-radius: 14px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07); border: 1px solid #e5e7eb;
}
.table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; width: 100%; }
.table-scroll table { width: 100%; min-width: 700px; margin-bottom: 0; }
.table thead th {
    background: linear-gradient(90deg, #1e3a8a, #1d4ed8);
    color: #fff; font-size: 13px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.5px;
    padding: 13px 12px; border: none; text-align: center; white-space: nowrap;
}
.table td {
    padding: 12px; vertical-align: middle; font-size: 14px;
    border-color: #f1f5f9; text-align: center; white-space: nowrap;
}
.badge-custom { font-size: 12px; padding: 5px 10px; border-radius: 6px; }
.btn-view {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    border: none; border-radius: 8px; padding: 5px 10px;
    font-size: 13px; font-weight: 600; color: #fff;
}
.btn-view:hover { background: linear-gradient(135deg, #1d4ed8, #1e3a8a); color: #fff; }
.plate-number {
    color: #1e40af; font-family: monospace; font-weight: 600;
    background: #eff6ff; padding: 2px 6px; border-radius: 4px; font-size: 13px;
}
.action-buttons { display: flex; gap: 6px; justify-content: center; }

/* ─── DP Cost Format ─── */
.dp-cost-paid   { font-weight: 700; color: #d97706; font-size: 13px; }
.dp-cost-slash  { color: #9ca3af; font-size: 12px; margin: 0 2px; }
.dp-cost-total  { color: #6b7280; font-size: 12px; }
.dp-cost-label  { font-size: 10px; color: #f59e0b; background: #fef3c7; border-radius: 4px; padding: 1px 5px; display: inline-block; margin-top: 2px; }

/* ─── MODAL (Report) ─── */
.modal-detail .modal-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
    color: #fff; border-radius: 12px 12px 0 0; padding: 18px 24px; border: none;
}
.modal-detail .modal-header .modal-title { font-weight: 700; font-size: 1.1rem; }
.modal-detail .modal-header .btn-close { filter: invert(1) brightness(2); }
.modal-detail .modal-content { border-radius: 14px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
.modal-detail .modal-body { padding: 20px; }
.modal-detail .modal-footer { border-top: 1px solid #f1f5f9; padding: 14px 20px; }
.detail-field label { font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; display: block; }
.detail-field .field-value { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 14px; font-size: 14px; color: #1f2937; min-height: 42px; word-break: break-word; }
.modal-detail .ktp-preview { width: 100%; max-height: 160px; object-fit: cover; border-radius: 8px; border: 1px solid #e5e7eb; }
.modal-detail .ktp-preview-wrapper { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px; text-align: center; }
.btn-pdf { background: linear-gradient(135deg, #ef4444, #dc2626); border: none; border-radius: 8px; padding: 5px 10px; font-size: 13px; font-weight: 600; color: #fff; }
.btn-pdf:hover { background: linear-gradient(135deg, #dc2626, #b91c1c); color: #fff; }

/* ─── RECEIPT MODAL ─── */
.booking-modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.5); z-index: 9999;
    align-items: center; justify-content: center;
}
.booking-modal-overlay.active { display: flex; }
.booking-modal {
    background: #fff; border-radius: 16px; max-width: 400px; width: 100%;
    font-family: 'Courier New', monospace; box-shadow: 0 20px 60px rgba(0,0,0,0.25);
}
.receipt-header { padding: 20px; text-align: center; }
.receipt-body { padding: 0 24px 20px 24px; }
.receipt-body p { margin: 6px 0; font-size: 14px; }
.receipt-body hr { border-top: 1px dashed #000; margin: 12px 0; }

/* ─── PAGINATION ─── */
.page-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 34px; height: 34px; border-radius: 8px;
    border: 1px solid #e5e7eb; background: #fff;
    color: #374151; font-size: 14px; font-weight: 500;
    text-decoration: none; cursor: pointer; transition: all 0.2s;
}
.page-btn:hover:not([disabled]) { border-color: #3b82f6; color: #3b82f6; }
.page-btn.active { background: #3b82f6; border-color: #3b82f6; color: #fff; }
.page-btn[disabled] { opacity: 0.4; cursor: not-allowed; }

@media (max-width: 768px) {
    .page-header { padding: 16px 18px; flex-direction: column !important; align-items: flex-start !important; }
    .page-header h2 { font-size: 1.2rem; }
    .header-controls { width: 100%; }
    .header-select { flex: 1; }
    .filter-bar { flex-direction: column; align-items: stretch; }
    .filter-bar > * { width: 100%; }
}
</style>
@endpush

@section('content')
<div class="container mt-4 px-3">

    @php
        $currentView = request('view', 'report');
        $pct = ($totalAll ?? 0) > 0 ? round(($totalPaid / $totalAll) * 100) : 0;
        $hasFilter = request('date_from') || request('date_to') || (request('type_filter') && request('type_filter') !== 'all');
        $typeLabels = [
            'completed' => 'Completed',
            'dp'        => 'Incomplete (DP)',
            'full'      => 'Paid Off (Full)',
            'cancelled' => 'Cancelled',
        ];
    @endphp

    {{-- ─── PAGE HEADER ─── --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            @if($currentView === 'report')
                <h2><i class="fas fa-file-alt me-2"></i> Rental Report</h2>
                <p>Complete vehicle rental history</p>
            @else
                <h2><i class="fas fa-credit-card me-2"></i> Payment History</h2>
                <p>Complete motorcycle rental payment records</p>
            @endif
        </div>
        <div class="header-controls">
            @if($currentView === 'report')
            <form method="GET" action="{{ request()->url() }}" id="sortForm">
                <input type="hidden" name="view"        value="report">
                <input type="hidden" name="date_from"   value="{{ request('date_from') }}">
                <input type="hidden" name="date_to"     value="{{ request('date_to') }}">
                <input type="hidden" name="type_filter" value="{{ request('type_filter') }}">
                <select name="sort" class="header-select" onchange="document.getElementById('sortForm').submit()">
                    <option value="newest" {{ request('sort','newest') == 'newest' ? 'selected' : '' }}>🕐 Newest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest'          ? 'selected' : '' }}>🕐 Oldest First</option>
                    <option value="id_asc" {{ request('sort') == 'id_asc'          ? 'selected' : '' }}>🔢 ID Ascending</option>
                </select>
            </form>
            @endif
            <form method="GET" action="{{ request()->url() }}" id="viewForm">
                <input type="hidden" name="sort"        value="{{ request('sort', 'newest') }}">
                <input type="hidden" name="date_from"   value="{{ request('date_from') }}">
                <input type="hidden" name="date_to"     value="{{ request('date_to') }}">
                <input type="hidden" name="type_filter" value="{{ request('type_filter') }}">
                <select name="view" class="header-select" onchange="document.getElementById('viewForm').submit()">
                    <option value="report"  {{ $currentView === 'report'  ? 'selected' : '' }}>📄 Rental Report</option>
                    <option value="payment" {{ $currentView === 'payment' ? 'selected' : '' }}>💳 Payment History</option>
                </select>
            </form>
        </div>
    </div>

    {{-- ─── FILTER BAR ─── --}}
    <form method="GET" action="{{ request()->url() }}" id="filterForm">
        <input type="hidden" name="view" value="{{ $currentView }}">
        <input type="hidden" name="sort" value="{{ request('sort', 'newest') }}">
        <div class="filter-bar">
            <div>
                <label><i class="fas fa-calendar me-1"></i> From</label>
                <input type="date" name="date_from" class="form-control"
                    value="{{ request('date_from') }}" style="min-width:140px;">
            </div>
            <div>
                <label><i class="fas fa-calendar me-1"></i> To</label>
                <input type="date" name="date_to" class="form-control"
                    value="{{ request('date_to') }}" style="min-width:140px;">
            </div>
            <div>
                <label><i class="fas fa-filter me-1"></i> Report Type</label>
                <select name="type_filter" class="form-select" style="min-width:190px;">
                    <option value="all"       {{ request('type_filter','all') == 'all'       ? 'selected' : '' }}>📋 All Status</option>
                    <option value="completed" {{ request('type_filter') == 'completed'        ? 'selected' : '' }}>✅ Completed</option>
                    <option value="dp"        {{ request('type_filter') == 'dp'               ? 'selected' : '' }}>⏳ Incomplete (DP)</option>
                    <option value="full"      {{ request('type_filter') == 'full'             ? 'selected' : '' }}>💳 Paid Off (Full)</option>
                    <option value="cancelled" {{ request('type_filter') == 'cancelled'        ? 'selected' : '' }}>❌ Cancelled</option>
                </select>
            </div>
            <div>
                <label style="visibility:hidden;">a</label>
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
            @if($hasFilter)
            <div>
                <label style="visibility:hidden;">a</label>
                <a href="{{ request()->url() }}?view={{ $currentView }}&sort={{ request('sort','newest') }}"
                   class="btn-reset">
                    <i class="fas fa-times"></i> Reset
                </a>
            </div>
            @endif
        </div>
    </form>

    @if($hasFilter)
    <div class="mb-3">
        <span class="filter-active-badge">
            <i class="fas fa-filter"></i>
            Filter active:
            @if(request('date_from')) From {{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }} @endif
            @if(request('date_to')) — {{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }} @endif
            @if(request('type_filter') && request('type_filter') !== 'all')
                · {{ $typeLabels[request('type_filter')] ?? request('type_filter') }}
            @endif
        </span>
    </div>
    @endif

    {{-- ════════════════════════════════ --}}
    {{-- REPORT VIEW                      --}}
    {{-- ════════════════════════════════ --}}
    @if($currentView === 'report')
    <div class="table-card">
        <div class="table-scroll">
            <table class="table table-borderless mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Vehicle</th>
                        <th>Plate</th>
                        <th>Rental Period</th>
                        <th>Total Cost</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    @php
                        $startDate  = \Carbon\Carbon::parse($booking->start_date);
                        $endDate    = \Carbon\Carbon::parse($booking->end_date);
                        $duration   = $startDate->diffInDays($endDate) + 1;
                        $penalty    = optional($booking->returnVehicle)->penalty ?? 0;
                        $customer   = $booking->customer;
                        $vehicle    = $booking->vehicle;
                        $isDP       = $booking->payment_status == 'paid' && $booking->payment_type == 'dp';
                        $dpPaid     = $booking->total_cost * 0.5;
                        $dpTotal    = $booking->total_cost;
                    @endphp
                    <tr>
                        <td><span class="text-muted fw-semibold">#{{ $booking->id }}</span></td>
                        <td class="fw-semibold">{{ optional($customer)->customer_name ?? '—' }}</td>
                        <td class="fw-semibold">{{ optional($vehicle)->name ?? '—' }}</td>
                        <td>
                            @if(optional($vehicle)->plate_number)
                                <span class="plate-number">{{ $vehicle->plate_number }}</span>
                            @else <span class="text-muted">—</span> @endif
                        </td>
                        <td>{{ $startDate->format('d M Y') }} – {{ $endDate->format('d M Y') }}</td>

                        {{-- ─── TOTAL COST ─── --}}
                        <td>
                            @if($booking->payment_status == 'cancelled')
                                <span class="text-muted text-decoration-line-through">Rp {{ number_format($booking->total_cost, 0, ',', '.') }}</span>
                            @elseif($isDP)
                                <div>
                                    <span class="dp-cost-paid">Rp {{ number_format($dpPaid, 0, ',', '.') }}</span>
                                    <span class="dp-cost-slash">/</span>
                                    <span class="dp-cost-total">Rp {{ number_format($dpTotal, 0, ',', '.') }}</span>
                                </div>
                                <div><span class="dp-cost-label">DP 50%</span></div>
                            @else
                                Rp {{ number_format($booking->total_cost, 0, ',', '.') }}
                            @endif
                        </td>

                        <td>
                            @if($booking->payment_status == 'completed')
                                <span class="badge bg-success badge-custom">Completed</span>
                            @elseif($booking->payment_status == 'cancelled')
                                <span class="badge bg-danger badge-custom">Cancelled</span>
                            @elseif($isDP)
                                <span class="badge bg-warning text-dark badge-custom">Incomplete (DP)</span>
                            @elseif($booking->payment_status == 'paid' && $booking->payment_type == 'full')
                                <span class="badge bg-primary badge-custom">Paid Off</span>
                            @else
                                <span class="badge bg-secondary badge-custom">{{ ucfirst($booking->payment_status) }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-sm btn-view"
                                    data-bs-toggle="modal" data-bs-target="#detailModal"
                                    data-id="{{ $booking->id }}"
                                    data-customer="{{ optional($customer)->customer_name ?? '—' }}"
                                    data-phone="{{ optional($customer)->phone_number ?? '—' }}"
                                    data-address="{{ optional($customer)->address ?? '—' }}"
                                    data-vehicle="{{ optional($vehicle)->name ?? '—' }}"
                                    data-plate="{{ optional($vehicle)->plate_number ?? '—' }}"
                                    data-start="{{ $startDate->format('d M Y') }}"
                                    data-end="{{ $endDate->format('d M Y') }}"
                                    data-duration="{{ $duration }}"
                                    data-cost="Rp {{ number_format($booking->total_cost, 0, ',', '.') }}"
                                    data-dp-paid="{{ $isDP ? 'Rp ' . number_format($dpPaid, 0, ',', '.') : '' }}"
                                    data-penalty="{{ $penalty > 0 ? 'Rp ' . number_format($penalty, 0, ',', '.') : '—' }}"
                                    data-status="{{ $booking->payment_status }}"
                                    data-payment-type="{{ $booking->payment_type }}"
                                    data-ktp="{{ $booking->identity_card ? Storage::url('ktp/' . $booking->identity_card) : '' }}"
                                    data-payment="{{ $booking->payment_proof ? Storage::url('payments/' . $booking->payment_proof) : '' }}">
                                    <i class="fas fa-eye me-1"></i> View
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted fst-italic">
                            @if($hasFilter) No data found for the selected filter.
                            @else No rental data found. @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted" style="font-size:13px;">
                Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }} results
            </span>
            <div class="d-flex gap-1 flex-wrap">
                @if($bookings->onFirstPage())
                    <button class="page-btn" disabled>&lsaquo;</button>
                @else
                    <a href="{{ $bookings->appends(request()->except('page'))->previousPageUrl() }}" class="page-btn">&lsaquo;</a>
                @endif
                @for($page = 1; $page <= $bookings->lastPage(); $page++)
                    @if($page == $bookings->currentPage())
                        <button class="page-btn active">{{ $page }}</button>
                    @else
                        <a href="{{ $bookings->appends(request()->except('page'))->url($page) }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endfor
                @if($bookings->hasMorePages())
                    <a href="{{ $bookings->appends(request()->except('page'))->nextPageUrl() }}" class="page-btn">&rsaquo;</a>
                @else
                    <button class="page-btn" disabled>&rsaquo;</button>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- ════════════════════════════════ --}}
    {{-- PAYMENT HISTORY VIEW             --}}
    {{-- ════════════════════════════════ --}}
    @else
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon revenue"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-content">
                <h4>Total Revenue</h4>
                <p class="s-main">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
                <p class="s-sub">From completed transactions</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon transactions"><i class="fas fa-check-circle"></i></div>
            <div class="stat-content">
                <h4>Paid Transactions</h4>
                <p class="s-main">{{ $totalTransactions ?? 0 }} Transactions</p>
                <p class="s-sub">Paid &amp; completed</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon progress"><i class="fas fa-chart-pie"></i></div>
            <div class="stat-content">
                <h4>Payment Collection</h4>
                <p class="s-main">
                    Rp {{ number_format($totalPaid ?? 0, 0, ',', '.') }}
                    <span class="s-slash"> / Rp {{ number_format($totalAll ?? 0, 0, ',', '.') }}</span>
                </p>
                <div class="prog-wrap">
                    <div class="prog-bg">
                        <div class="prog-fill" style="width: {{ $pct }}%;"></div>
                    </div>
                    <div class="prog-labels">
                        <span>Collected</span>
                        <span>{{ $pct }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-scroll">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ optional($payment->customer)->customer_name ?? '—' }}</td>
                        <td class="fw-bold text-success">Rp {{ number_format($payment->total_price, 0, ',', '.') }}</td>
                        <td>
                            @if($payment->status == 'completed')
                                <span class="badge bg-success badge-custom">Completed</span>
                            @elseif($payment->status == 'paid')
                                <span class="badge bg-primary badge-custom">Paid</span>
                            @else
                                <span class="badge bg-secondary badge-custom">{{ $payment->status }}</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-view" onclick="openDetailModal({
                                customer: '{{ addslashes(optional($payment->customer)->customer_name ?? '—') }}',
                                phone: '{{ addslashes(optional($payment->customer)->phone_number ?? '—') }}',
                                vehicle: '{{ addslashes(optional($payment->vehicle)->name ?? '—') }}',
                                start: '{{ \Carbon\Carbon::parse($payment->start_date)->format('d M Y') }}',
                                end: '{{ \Carbon\Carbon::parse($payment->end_date)->format('d M Y') }}',
                                total: 'Rp {{ number_format($payment->total_price, 0, ',', '.') }}',
                                status: '{{ $payment->status }}'
                            })">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted fst-italic">
                            @if($hasFilter) No payment records found for the selected filter.
                            @else No payment records found. @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

{{-- ─── REPORT DETAIL MODAL ─── --}}
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-detail">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i> Detail Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-6 detail-field"><label>Booking ID</label><div class="field-value" id="modal-id"></div></div>
                    <div class="col-6 detail-field"><label>Status</label><div class="field-value" id="modal-status"></div></div>
                    <div class="col-md-6 detail-field"><label>Customer</label><div class="field-value" id="modal-customer"></div></div>
                    <div class="col-md-6 detail-field"><label>Phone</label><div class="field-value" id="modal-phone"></div></div>
                    <div class="col-12 detail-field"><label>Address</label><div class="field-value" id="modal-address"></div></div>
                    <div class="col-md-6 detail-field"><label>Vehicle</label><div class="field-value" id="modal-vehicle"></div></div>
                    <div class="col-md-6 detail-field"><label>Plate</label><div class="field-value" id="modal-plate"></div></div>
                    <div class="col-md-6 detail-field"><label>Rental Period</label><div class="field-value" id="modal-dates"></div></div>
                    <div class="col-md-6 detail-field"><label>Duration</label><div class="field-value" id="modal-duration"></div></div>
                    <div class="col-md-6 detail-field"><label>Total Cost</label><div class="field-value" id="modal-cost"></div></div>
                    <div class="col-md-6 detail-field" id="modal-dp-row" style="display:none;">
                        <label>DP Paid</label>
                        <div class="field-value fw-bold text-warning" id="modal-dp-paid"></div>
                    </div>
                    <div class="col-md-6 detail-field"><label>Penalty</label><div class="field-value" id="modal-penalty"></div></div>
                    <div class="col-md-6 detail-field"><label>ID Card</label><div id="modal-ktp-wrapper"><div class="field-value text-muted">—</div></div></div>
                    <div class="col-md-6 detail-field"><label>Payment Proof</label><div id="modal-payment-wrapper"><div class="field-value text-muted">—</div></div></div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="modal-pdf-link" class="btn btn-sm btn-pdf"><i class="fas fa-file-pdf me-1"></i> PDF</a>
            </div>
        </div>
    </div>
</div>

{{-- ─── PAYMENT RECEIPT MODAL ─── --}}
<div class="booking-modal-overlay" id="detailModalOverlay" onclick="closeOnOverlay(event)">
    <div class="booking-modal">
        <div class="receipt-header">
            <h5>RENTAL MOTOR</h5>
            <small>Denpasar, Bali</small>
            <hr>
            <strong>PAYMENT RECEIPT</strong>
        </div>
        <div class="receipt-body">
            <p><strong>Customer :</strong> <span id="r-customer"></span></p>
            <p><strong>Phone :</strong> <span id="r-phone"></span></p>
            <p><strong>Motor :</strong> <span id="r-vehicle"></span></p>
            <hr>
            <p><strong>Rental Period :</strong></p>
            <p id="r-dates"></p>
            <hr>
            <p><strong>Total Payment :</strong></p>
            <h5 class="text-end fw-bold text-success" id="r-total"></h5>
            <hr>
            <p class="text-center" id="r-status"></p>
            <div class="text-center mt-3"><small>Thank you for your trust 🙏</small></div>
        </div>
        <div class="text-center pb-3">
            <button class="btn btn-sm btn-secondary" onclick="closeDetailModal()">Close</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('detailModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function (event) {
            const btn         = event.relatedTarget;
            const status      = btn.dataset.status;
            const paymentType = btn.dataset.paymentType;
            const isDP        = status === 'paid' && paymentType === 'dp';

            document.getElementById('modal-id').textContent       = '#' + btn.dataset.id;
            document.getElementById('modal-customer').textContent = btn.dataset.customer;
            document.getElementById('modal-phone').textContent    = btn.dataset.phone;
            document.getElementById('modal-address').textContent  = btn.dataset.address;
            document.getElementById('modal-vehicle').textContent  = btn.dataset.vehicle;
            document.getElementById('modal-plate').textContent    = btn.dataset.plate;
            document.getElementById('modal-dates').textContent    = btn.dataset.start + ' s/d ' + btn.dataset.end;
            document.getElementById('modal-duration').textContent = btn.dataset.duration + ' hari';
            document.getElementById('modal-penalty').textContent  = btn.dataset.penalty;

            // Total Cost
            if (isDP && btn.dataset.dpPaid) {
                document.getElementById('modal-cost').innerHTML =
                    `<span style="color:#d97706;font-weight:700;">${btn.dataset.dpPaid}</span>` +
                    `<span style="color:#9ca3af;"> / </span>` +
                    `<span style="color:#6b7280;">${btn.dataset.cost}</span>` +
                    `<br><small style="background:#fef3c7;color:#f59e0b;border-radius:4px;padding:1px 6px;font-size:11px;">DP 50% — sisa belum lunas</small>`;
                document.getElementById('modal-dp-row').style.display = 'none';
            } else {
                document.getElementById('modal-cost').textContent = btn.dataset.cost;
                document.getElementById('modal-dp-row').style.display = 'none';
            }

            // Status badge
            let statusHtml;
            if (status === 'completed') {
                statusHtml = '<span class="badge bg-success badge-custom">Completed</span>';
            } else if (status === 'cancelled') {
                statusHtml = '<span class="badge bg-danger badge-custom">Cancelled</span>';
            } else if (isDP) {
                statusHtml = '<span class="badge bg-warning text-dark badge-custom">Incomplete (DP)</span>';
            } else if (status === 'paid' && paymentType === 'full') {
                statusHtml = '<span class="badge bg-primary badge-custom">Paid Off</span>';
            } else {
                statusHtml = `<span class="badge bg-secondary badge-custom">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
            }
            document.getElementById('modal-status').innerHTML = statusHtml;

            document.getElementById('modal-ktp-wrapper').innerHTML = btn.dataset.ktp
                ? `<div class="ktp-preview-wrapper"><img src="${btn.dataset.ktp}" class="ktp-preview" alt="KTP"></div>`
                : '<div class="field-value text-muted">—</div>';

            document.getElementById('modal-payment-wrapper').innerHTML = btn.dataset.payment
                ? `<div class="ktp-preview-wrapper"><img src="${btn.dataset.payment}" class="ktp-preview" alt="Payment Proof"></div>`
                : '<div class="field-value text-muted">—</div>';

            document.getElementById('modal-pdf-link').href = '/reports/' + btn.dataset.id + '/pdf';
        });
    }

    const dateFrom = document.querySelector('input[name="date_from"]');
    const dateTo   = document.querySelector('input[name="date_to"]');
    if (dateFrom && dateTo) {
        dateFrom.addEventListener('change', function () {
            if (dateTo.value && dateTo.value < this.value) dateTo.value = this.value;
            dateTo.min = this.value;
        });
    }
});

function openDetailModal(data) {
    document.getElementById('r-customer').textContent = data.customer;
    document.getElementById('r-phone').textContent    = data.phone;
    document.getElementById('r-vehicle').textContent  = data.vehicle;
    document.getElementById('r-dates').textContent    = data.start + ' – ' + data.end;
    document.getElementById('r-total').textContent    = data.total;

    const statusMap = {
        'paid':      '<span class="badge bg-primary badge-custom">Paid</span>',
        'completed': '<span class="badge bg-success badge-custom">Completed</span>'
    };
    document.getElementById('r-status').innerHTML =
        statusMap[data.status] ?? `<span class="badge bg-secondary badge-custom">${data.status}</span>`;

    document.getElementById('detailModalOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDetailModal() {
    document.getElementById('detailModalOverlay').classList.remove('active');
    document.body.style.overflow = '';
}

function closeOnOverlay(event) {
    if (event.target === document.getElementById('detailModalOverlay')) closeDetailModal();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDetailModal();
});
</script>
@endpush