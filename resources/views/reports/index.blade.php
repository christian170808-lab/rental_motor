@extends('layouts.app')

@push('styles')
<style>
.page-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 60%, #1d4ed8 100%);
    border-radius: 16px;
    padding: 22px 28px;
    margin-bottom: 20px;
    box-shadow: 0 8px 32px rgba(37,99,235,0.25);
}
.page-header h2 {
    color: #fff;
    font-weight: 700;
    margin: 0;
    font-size: 1.5rem;
}
.page-header p {
    color: rgba(255,255,255,0.7);
    margin: 4px 0 0;
    font-size: 0.9rem;
}

.table-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07);
    border: 1px solid #e5e7eb;
}

.table-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
}

.table-scroll table {
    width: 100%;
    min-width: 900px; /* supaya tidak terlalu gepeng di mobile */
    margin-bottom: 0;
}

.table thead th {
    background: linear-gradient(90deg, #1e3a8a, #1d4ed8);
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 13px 12px;
    border: none;
    text-align: center;
    white-space: nowrap;
}

.table td {
    padding: 12px;
    vertical-align: middle;
    font-size: 14px;
    border-color: #f1f5f9;
    text-align: center;
    white-space: nowrap;
}

.ktp-thumb {
    width: 60px;
    height: 40px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
}

.badge-custom {
    font-size: 12px;
    padding: 5px 10px;
    border-radius: 6px;
}

.btn-pdf {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border: none;
    border-radius: 8px;
    padding: 5px 10px;
    font-size: 13px;
    font-weight: 600;
    color: #fff;
}
.btn-pdf:hover { background: linear-gradient(135deg, #dc2626, #b91c1c); color: #fff; }

.btn-view {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    border: none;
    border-radius: 8px;
    padding: 5px 10px;
    font-size: 13px;
    font-weight: 600;
    color: #fff;
}
.btn-view:hover { background: linear-gradient(135deg, #1d4ed8, #1e3a8a); color: #fff; }

.plate-number {
    color: #1e40af;
    font-family: monospace;
    font-weight: 600;
    background: #eff6ff;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 13px;
}

.action-buttons {
    display: flex;
    gap: 6px;
    justify-content: center;
}

/* ─── SORT FORM RESPONSIVE ─── */
.sort-form select {
    width: 100%;
    max-width: 180px;
    border-radius: 8px;
    font-size: 14px;
    border: 1px solid #e5e7eb;
}

/* ─── MODAL ─── */
.modal-detail .modal-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
    color: #fff;
    border-radius: 12px 12px 0 0;
    padding: 18px 24px;
    border: none;
}
.modal-detail .modal-header .modal-title { font-weight: 700; font-size: 1.1rem; }
.modal-detail .modal-header .btn-close { filter: invert(1) brightness(2); }
.modal-detail .modal-content { border-radius: 14px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
.modal-detail .modal-body { padding: 20px; }
.modal-detail .modal-footer { border-top: 1px solid #f1f5f9; padding: 14px 20px; }

.detail-field label {
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
    display: block;
}
.detail-field .field-value {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 14px;
    color: #1f2937;
    min-height: 42px;
    word-break: break-word;
}
.modal-detail .ktp-preview {
    width: 100%;
    max-height: 160px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}
.modal-detail .ktp-preview-wrapper {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px;
    text-align: center;
}

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

/* ─── RESPONSIVE ─── */
@media (max-width: 768px) {
    .page-header {
        padding: 16px 18px;
        flex-direction: column !important;
        align-items: flex-start !important;
    }
    .page-header h2 { font-size: 1.2rem; }
    .sort-form select { max-width: 100%; }

    /* Pagination wrap */
    .pagination-wrapper {
        flex-direction: column !important;
        gap: 8px;
        align-items: flex-start !important;
    }
    .pagination-wrapper .page-info { font-size: 12px; }

    /* Modal fullscreen di mobile */
    .modal-dialog { margin: 0.5rem; }
    .modal-detail .modal-body { padding: 14px; }
}

@media (max-width: 480px) {
    .page-header h2 { font-size: 1rem; }
    .table thead th { font-size: 11px; padding: 10px 8px; }
    .table td { font-size: 12px; padding: 8px; }
    .ktp-thumb { width: 44px; height: 30px; }
    .btn-view, .btn-pdf { font-size: 11px; padding: 4px 7px; }
}
</style>
@endpush

@section('content')
<div class="container mt-4 px-3">

    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2><i class="fas fa-file-alt me-2"></i> Rental Report</h2>
            <p>Complete vehicle rental history</p>
        </div>
        <div class="sort-form">
            <form method="GET" action="{{ request()->url() }}" id="sortForm">
                <select name="sort" class="form-select" onchange="document.getElementById('sortForm').submit()">
                    <option value="newest" {{ request('sort','newest') == 'newest' ? 'selected' : '' }}>🕐 Newest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest'          ? 'selected' : '' }}>🕐 Oldest First</option>
                    <option value="id_asc" {{ request('sort') == 'id_asc'          ? 'selected' : '' }}>🔢 ID Ascending</option>
                </select>
            </form>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="table-card">
        <div class="table-scroll">
            <table class="table table-borderless mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Vehicle</th>
                        <th>Plate</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Duration</th>
                        <th>Total Cost</th>
                        <th>Penalty</th>
                        <th>ID Card</th>
                        <th>Payment Proof</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    @php
                        $startDate = \Carbon\Carbon::parse($booking->start_date);
                        $endDate   = \Carbon\Carbon::parse($booking->end_date);
                        $duration  = $startDate->diffInDays($endDate) + 1;
                        $penalty   = optional($booking->returnVehicle)->penalty ?? 0;
                        $customer  = $booking->customer;
                        $vehicle   = $booking->vehicle;
                    @endphp
                    <tr>
                        <td><span class="text-muted fw-semibold">#{{ $booking->id }}</span></td>
                        <td class="fw-semibold">{{ optional($customer)->customer_name ?? '—' }}</td>
                        <td>{{ optional($customer)->phone_number ?? '—' }}</td>
                        <td>{{ optional($customer)->address ?? '—' }}</td>
                        <td class="fw-semibold">{{ optional($vehicle)->name ?? '—' }}</td>
                        <td>
                            @if(optional($vehicle)->plate_number)
                                <span class="plate-number">{{ $vehicle->plate_number }}</span>
                            @else <span class="text-muted">—</span> @endif
                        </td>
                        <td>{{ $startDate->format('d M Y') }}</td>
                        <td>{{ $endDate->format('d M Y') }}</td>
                        <td class="fw-semibold">{{ $duration }} days</td>
                        <td>Rp {{ number_format($booking->total_cost, 0, ',', '.') }}</td>
                        <td>
                            @if($penalty > 0)
                                <span class="text-danger fw-semibold">Rp {{ number_format($penalty, 0, ',', '.') }}</span>
                            @else <span class="text-muted">—</span> @endif
                        </td>
                        <td>
                            @if($booking->identity_card)
                                <img src="{{ Storage::url('ktp/' . $booking->identity_card) }}" class="ktp-thumb" alt="KTP">
                            @else <span class="text-muted">—</span> @endif
                        </td>
                        <td>
                            @if($booking->payment_proof)
                                <img src="{{ Storage::url('payments/' . $booking->payment_proof) }}" class="ktp-thumb" alt="Payment">
                            @else <span class="text-muted">—</span> @endif
                        </td>
                        <td>
                            @if($booking->payment_status == 'completed')
                                <span class="badge bg-success badge-custom">Completed</span>
                            @elseif($booking->payment_status == 'paid')
                                <span class="badge bg-warning text-dark badge-custom">Incomplete</span>
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
                                    data-penalty="{{ $penalty > 0 ? 'Rp ' . number_format($penalty, 0, ',', '.') : '—' }}"
                                    data-status="{{ $booking->payment_status }}"
                                    data-ktp="{{ $booking->identity_card ? Storage::url('ktp/' . $booking->identity_card) : '' }}"
                                    data-payment="{{ $booking->payment_proof ? Storage::url('payments/' . $booking->payment_proof) : '' }}">
                                    <i class="fas fa-eye me-1"></i> View
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="15" class="text-center py-5 text-muted fst-italic">
                            No rental data found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($bookings->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2 pagination-wrapper">
            <span class="text-muted page-info" style="font-size:13px;">
                Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }} results
            </span>
            <div class="d-flex gap-1 flex-wrap">
                @if($bookings->onFirstPage())
                    <button class="page-btn" disabled>&lsaquo;</button>
                @else
                    <a href="{{ $bookings->appends(['sort' => request('sort')])->previousPageUrl() }}" class="page-btn">&lsaquo;</a>
                @endif

                @for($page = 1; $page <= $bookings->lastPage(); $page++)
                    @if($page == $bookings->currentPage())
                        <button class="page-btn active">{{ $page }}</button>
                    @else
                        <a href="{{ $bookings->appends(['sort' => request('sort')])->url($page) }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endfor

                @if($bookings->hasMorePages())
                    <a href="{{ $bookings->appends(['sort' => request('sort')])->nextPageUrl() }}" class="page-btn">&rsaquo;</a>
                @else
                    <button class="page-btn" disabled>&rsaquo;</button>
                @endif
            </div>
        </div>
        @endif
    </div>

</div>

{{-- Detail Booking Modal --}}
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-detail">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i> Detail Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-6 detail-field">
                        <label>Booking ID</label>
                        <div class="field-value" id="modal-id"></div>
                    </div>
                    <div class="col-6 detail-field">
                        <label>Status</label>
                        <div class="field-value" id="modal-status"></div>
                    </div>
                    <div class="col-md-6 detail-field">
                        <label>Nama Customer</label>
                        <div class="field-value" id="modal-customer"></div>
                    </div>
                    <div class="col-md-6 detail-field">
                        <label>No Telepon</label>
                        <div class="field-value" id="modal-phone"></div>
                    </div>
                    <div class="col-12 detail-field">
                        <label>Alamat</label>
                        <div class="field-value" id="modal-address"></div>
                    </div>
                    <div class="col-md-6 detail-field">
                        <label>Kendaraan</label>
                        <div class="field-value" id="modal-vehicle"></div>
                    </div>
                    <div class="col-md-6 detail-field">
                        <label>Plat Nomor</label>
                        <div class="field-value" id="modal-plate"></div>
                    </div>
                    <div class="col-md-6 detail-field">
                        <label>Tanggal Sewa</label>
                        <div class="field-value" id="modal-dates"></div>
                    </div>
                    <div class="col-md-6 detail-field">
                        <label>Durasi</label>
                        <div class="field-value" id="modal-duration"></div>
                    </div>
                    <div class="col-md-6 detail-field">
                        <label>Total Biaya</label>
                        <div class="field-value" id="modal-cost"></div>
                    </div>
                    <div class="col-md-6 detail-field">
                        <label>Denda</label>
                        <div class="field-value" id="modal-penalty"></div>
                    </div>
                    <div class="col-md-6 detail-field">
                        <label>KTP / ID Card</label>
                        <div id="modal-ktp-wrapper">
                            <div class="field-value text-muted">—</div>
                        </div>
                    </div>
                    <div class="col-md-6 detail-field">
                        <label>Bukti Pembayaran</label>
                        <div id="modal-payment-wrapper">
                            <div class="field-value text-muted">—</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="modal-pdf-link" class="btn btn-sm btn-pdf">
                    <i class="fas fa-file-pdf me-1"></i> PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('detailModal').addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;

        document.getElementById('modal-id').textContent       = '#' + btn.dataset.id;
        document.getElementById('modal-customer').textContent = btn.dataset.customer;
        document.getElementById('modal-phone').textContent    = btn.dataset.phone;
        document.getElementById('modal-address').textContent  = btn.dataset.address;
        document.getElementById('modal-vehicle').textContent  = btn.dataset.vehicle;
        document.getElementById('modal-plate').textContent    = btn.dataset.plate;
        document.getElementById('modal-dates').textContent    = btn.dataset.start + ' s/d ' + btn.dataset.end;
        document.getElementById('modal-duration').textContent = btn.dataset.duration + ' hari';
        document.getElementById('modal-cost').textContent     = btn.dataset.cost;
        document.getElementById('modal-penalty').textContent  = btn.dataset.penalty;

        const status = btn.dataset.status;
        let badgeHtml = '';
        if (status === 'completed') {
            badgeHtml = '<span class="badge bg-success badge-custom">Completed</span>';
        } else if (status === 'paid') {
            badgeHtml = '<span class="badge bg-warning text-dark badge-custom">Incomplete</span>';
        } else {
            badgeHtml = `<span class="badge bg-secondary badge-custom">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
        }
        document.getElementById('modal-status').innerHTML = badgeHtml;

        document.getElementById('modal-ktp-wrapper').innerHTML = btn.dataset.ktp
            ? `<div class="ktp-preview-wrapper"><img src="${btn.dataset.ktp}" class="ktp-preview" alt="KTP"></div>`
            : '<div class="field-value text-muted">—</div>';

        document.getElementById('modal-payment-wrapper').innerHTML = btn.dataset.payment
            ? `<div class="ktp-preview-wrapper"><img src="${btn.dataset.payment}" class="ktp-preview" alt="Payment Proof"></div>`
            : '<div class="field-value text-muted">—</div>';

        document.getElementById('modal-pdf-link').href = '/reports/' + btn.dataset.id + '/pdf';
    });
});
</script>
@endpush