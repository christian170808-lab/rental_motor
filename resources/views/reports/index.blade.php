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
    max-width: 100%;
}

.table-scroll table {
    width: 100%;
    margin-bottom: 0;
}

.table thead th {
    background: linear-gradient(90deg, #1e3a8a, #1d4ed8);
    color: #fff;
    font-size: 16px;
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
    font-size: 16px;
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
    font-size: 14px;
    padding: 6px 10px;
    border-radius: 6px;
}

.btn-pdf {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    border: none;
    border-radius: 8px;
    padding: 5px 10px;
    font-size: 14px;
    font-weight: 600;
    color: #fff;
}

.btn-pdf:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: #fff;
}

.btn-view {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    border: none;
    border-radius: 8px;
    padding: 5px 10px;
    font-size: 14px;
    font-weight: 600;
    color: #fff;
}

.btn-view:hover {
    background: linear-gradient(135deg, #1d4ed8, #1e3a8a);
    color: #fff;
}

.plate-number {
    color: #1e40af;
    font-family: monospace;
    font-weight: 600;
    background: #eff6ff;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 15px;
}

.action-buttons {
    display: flex;
    gap: 6px;
    justify-content: center;
}

/* MODAL */
.modal-detail .modal-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
    color: #fff;
    border-radius: 12px 12px 0 0;
    padding: 18px 24px;
    border: none;
}

.modal-detail .modal-header .modal-title {
    font-weight: 700;
    font-size: 1.15rem;
}

.modal-detail .modal-header .btn-close {
    filter: invert(1) brightness(2);
}

.modal-detail .modal-content {
    border-radius: 14px;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}

.modal-detail .modal-body {
    padding: 24px;
}

.modal-detail .modal-footer {
    border-top: 1px solid #f1f5f9;
    padding: 16px 24px;
}

.detail-field label {
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}

.detail-field .field-value {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 15px;
    color: #1f2937;
    min-height: 42px;
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
</style>
@endpush

@section('content')
<div class="container mt-4">

    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2><i class="fas fa-file-alt me-2"></i> Rental Report</h2>
            <p>Complete motorcycle rental history</p>
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
                            <td><span class="booking-id">#{{ $booking->id }}</span></td>
                            <td class="fw-semibold" style="font-size:15px;">{{ optional($customer)->customer_name ?? '—' }}</td>
                            <td>{{ optional($customer)->phone_number ?? '—' }}</td>
                            <td>{{ optional($customer)->address ?? '—' }}</td>
                            <td class="fw-semibold">{{ optional($vehicle)->name ?? '—' }}</td>
                            <td>
                                @if(optional($vehicle)->plate_number)
                                    <span class="plate-number">{{ $vehicle->plate_number }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $startDate->format('d M Y') }}</td>
                            <td>{{ $endDate->format('d M Y') }}</td>
                            <td><span class="fw-semibold">{{ $duration }} days</span></td>
                            <td><span class="text-cost">Rp {{ number_format($booking->total_cost, 0, ',', '.') }}</span></td>
                            <td>
                                @if($penalty > 0)
                                    <span class="text-penalty">Rp {{ number_format($penalty, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($booking->identity_card)
                                    <img src="{{ Storage::url('ktp/' . $booking->identity_card) }}" class="ktp-thumb" alt="KTP">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($booking->payment_proof)
    <img src="{{ Storage::url('payments/' . $booking->payment_proof) }}" class="ktp-thumb" alt="Payment">
@else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($booking->payment_status == 'completed')
                                    <span class="badge bg-success badge-custom">Completed</span>
                                @elseif($booking->payment_status == 'paid')
                                    <span class="badge bg-warning text-dark badge-custom">incomplete</span>
                                @else
                                    <span class="badge bg-secondary badge-custom">{{ ucfirst($booking->payment_status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                   <button type="button" class="btn btn-sm btn-view"
    data-bs-toggle="modal"
    data-bs-target="#detailModal"
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
    data-payment="{{ $booking->payment_proof ? Storage::url('payments/' . $booking->payment_proof) : '' }}"
>
    <i class="fas fa-eye me-1"></i> View
</button>


</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="text-center py-5 text-muted fst-italic" style="font-size:16px;">
                                No rental data found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Detail Booking Modal --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-detail">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fas fa-info-circle me-2"></i> Detail Booking
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">

                    <div class="col-md-6 detail-field">
                        <label>Booking ID</label>
                        <div class="field-value" id="modal-id"></div>
                    </div>

                    <div class="col-md-6 detail-field">
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
    const detailModal = document.getElementById('detailModal');

    detailModal.addEventListener('show.bs.modal', function (event) {
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

        // Status badge
        const status = btn.dataset.status;
        let badgeHtml = '';
        if (status === 'completed') {
            badgeHtml = '<span class="badge bg-success badge-custom">Completed</span>';
        } else if (status === 'paid') {
            badgeHtml = '<span class="badge bg-warning text-dark badge-custom">incomplete</span>';
        } else {
            badgeHtml = `<span class="badge bg-secondary badge-custom">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
        }
        document.getElementById('modal-status').innerHTML = badgeHtml;

        // KTP Image
        const ktpWrapper = document.getElementById('modal-ktp-wrapper');
        ktpWrapper.innerHTML = btn.dataset.ktp
            ? `<div class="ktp-preview-wrapper"><img src="${btn.dataset.ktp}" class="ktp-preview" alt="KTP"></div>`
            : '<div class="field-value text-muted">—</div>';

        // Payment Proof Image
        const paymentWrapper = document.getElementById('modal-payment-wrapper');
        paymentWrapper.innerHTML = btn.dataset.payment
            ? `<div class="ktp-preview-wrapper"><img src="${btn.dataset.payment}" class="ktp-preview" alt="Payment Proof"></div>`
            : '<div class="field-value text-muted">—</div>';

        // PDF Link
        document.getElementById('modal-pdf-link').href = '/reports/' + btn.dataset.id + '/pdf';
    });
});
</script>
@endpush