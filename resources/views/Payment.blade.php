@extends('layouts.app')

@push('styles')
<style>
/* ─── PAGE HEADER ─── */
.page-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 60%, #1d4ed8 100%);
    border-radius: 16px;
    padding: 22px 28px;
    margin-bottom: 20px;
    box-shadow: 0 8px 32px rgba(37,99,235,0.25);
}
.page-header h2 { color: #fff; font-weight: 700; margin: 0; font-size: 1.5rem; }
.page-header p  { color: rgba(255,255,255,0.7); margin: 4px 0 0; font-size: 0.9rem; }

/* ─── STATS CARDS ─── */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}
.stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07);
    border: 1px solid #e5e7eb;
    display: flex; align-items: center; gap: 16px;
}
.stat-icon {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center; font-size: 24px;
}
.stat-icon.revenue      { background: #fef3c7; color: #f59e0b; }
.stat-icon.transactions { background: #d1fae5; color: #10b981; }
.stat-content h4 { font-size: 14px; color: #6b7280; margin: 0 0 4px; font-weight: 500; }
.stat-content p  { font-size: 24px; font-weight: 700; color: #111827; margin: 0; }

/* ─── TABLE CARD ─── */
.table-card {
    background: #fff; border-radius: 14px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07);
    border: 1px solid #e5e7eb; overflow: hidden;
}
.table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.table-scroll table { width: 100%; margin-bottom: 0; }

.table thead th {
    background: linear-gradient(90deg, #1e3a8a, #1d4ed8);
    color: #fff; font-size: 13px; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.5px;
    padding: 14px 16px; border: none; text-align: center; white-space: nowrap;
}
.table td {
    padding: 14px 16px; vertical-align: middle;
    font-size: 14px; border-color: #f1f5f9;
    text-align: center; white-space: nowrap;
}
.table tbody tr { border-bottom: 1px solid #f1f5f9; }
.table tbody tr:last-child { border-bottom: none; }
.table tbody tr:hover { background: #f8fafc; }

/* ─── BADGE ─── */
.badge-custom { font-size: 12px; padding: 6px 12px; border-radius: 20px; font-weight: 600; }

/* ─── BUTTONS ─── */
.btn-view {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    border: none; color: #fff; border-radius: 8px;
    padding: 7px 16px; font-size: 13px; font-weight: 600;
    transition: all 0.2s; cursor: pointer;
}
.btn-view:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59,130,246,0.35); color: #fff; }

/* ─── DETAIL MODAL ─── */
.booking-modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.5); z-index: 9999;
    align-items: center; justify-content: center;
    animation: fadeOverlay 0.2s ease;
}
.booking-modal-overlay.active { display: flex; }

@keyframes fadeOverlay { from { opacity: 0; } to { opacity: 1; } }

.booking-modal {
    background: #fff; border-radius: 16px;
    width: 100%; max-width: 520px; margin: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25); overflow: hidden;
    animation: slideUp 0.25s ease;
}

@keyframes slideUp {
    from { transform: translateY(40px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
}

.booking-modal-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
    padding: 20px 24px; display: flex; align-items: center; justify-content: space-between;
}
.booking-modal-header h5 { color: #fff; font-size: 1.1rem; font-weight: 700; margin: 0; }
.booking-modal-close {
    background: none; border: none; color: rgba(255,255,255,0.8);
    font-size: 22px; line-height: 1; cursor: pointer; padding: 0; transition: color 0.2s;
}
.booking-modal-close:hover { color: #fff; }

.booking-modal-body { padding: 24px; display: flex; flex-direction: column; gap: 14px; }

.modal-field label { display: block; font-weight: 600; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
.modal-field .field-value { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 14px; font-size: 14px; color: #111827; width: 100%; }
</style>
@endpush

@section('content')
<div class="container mt-4">

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <h2><i class="fas fa-credit-card me-2"></i> Payment History</h2>
        <p>Complete motorcycle rental payment records</p>
    </div>

    {{-- STATS CARDS --}}
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon revenue"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-content">
                <h4>Total Revenue</h4>
                <p>Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon transactions"><i class="fas fa-check-circle"></i></div>
            <div class="stat-content">
                <h4>Paid Transactions</h4>
                <p>{{ $totalTransactions ?? 0 }} Transactions</p>
            </div>
        </div>
    </div>

    {{-- PAYMENTS TABLE --}}
    <div class="table-card">
        <div class="table-scroll">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Customer</th>
                        <th>Duration</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $index => $payment)
                    @php
                        $startDate   = \Carbon\Carbon::parse($payment->start_date);
                        $endDate     = \Carbon\Carbon::parse($payment->end_date);
                        $duration    = $startDate->diffInDays($endDate) + 1;
                        $status      = $payment->status;
                        $paymentType = optional($payment->booking)->payment_type ?? 'full';
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $index + 1 }}</td>
                        <td class="fw-semibold">{{ optional($payment->customer)->customer_name ?? '—' }}</td>
                        <td>
                            <span class="badge badge-custom" style="background-color: #e5e7eb; color: #6b7280;">
                                <i class="fas fa-clock me-1"></i> {{ $duration }} {{ $duration > 1 ? 'days' : 'day' }}
                            </span>
                        </td>
                        <td class="fw-bold text-success">Rp {{ number_format($payment->total_price, 0, ',', '.') }}</td>        
                        <td>
                            @if($status == 'completed')
                                <span class="badge bg-success badge-custom"><i class="fas fa-check me-1"></i> Completed</span>
                            @elseif($status == 'paid' && $paymentType == 'dp')
                                <span class="badge bg-warning text-dark badge-custom"><i class="fas fa-clock me-1"></i> DP 50%</span>
                            @elseif($status == 'paid')
                                <span class="badge bg-primary badge-custom"><i class="fas fa-check-circle me-1"></i> Paid</span>
                            @elseif($status == 'active')
                                <span class="badge bg-warning text-dark badge-custom"><i class="fas fa-clock me-1"></i> Active</span>
                            @else
                                <span class="badge bg-secondary badge-custom">{{ $status ?? 'Unknown' }}</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn-view"
                                onclick="openDetailModal({
                                    customer: '{{ addslashes(optional($payment->customer)->customer_name ?? '—') }}',
                                    phone:    '{{ addslashes(optional($payment->customer)->phone_number ?? '—') }}',
                                    vehicle:  '{{ addslashes(optional($payment->vehicle)->name ?? '—') }}',
                                    plate:    '{{ addslashes(optional($payment->vehicle)->plate_number ?? '—') }}',
                                    start:    '{{ $startDate->format('d M Y') }}',
                                    end:      '{{ $endDate->format('d M Y') }}',
                                    duration: '{{ $duration }} {{ $duration > 1 ? 'days' : 'day' }}',
                                    total:    'Rp {{ number_format($payment->total_price, 0, ',', '.') }}',
                                    status: '{{ $status }}',
                                    paymentType: '{{ $paymentType }}'
                                })">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block opacity-25"></i>
                            No payment records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($payments->hasPages())
        <div class="p-3 border-top d-flex justify-content-center">
            {{ $payments->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>

{{-- DETAIL MODAL --}}
<div class="booking-modal-overlay" id="detailModalOverlay" onclick="closeOnOverlay(event)">
    <div class="booking-modal">
        <div class="booking-modal-header">
            <h5><i class="fas fa-file-invoice me-2"></i> Booking Detail</h5>
            <button class="booking-modal-close" onclick="closeDetailModal()">&times;</button>
        </div>
        <div class="booking-modal-body">
            <div class="modal-field">
                <label>Customer Name</label>
                <div class="field-value" id="modal-customer">—</div>
            </div>
            <div class="modal-field">
                <label>Phone Number</label>
                <div class="field-value" id="modal-phone">—</div>
            </div>
            <div class="modal-field">
                <label>Vehicle</label>
                <div class="field-value" id="modal-vehicle">—</div>
            </div>
            <div class="modal-field">
                <label>Plate Number</label>
                <div class="field-value" id="modal-plate">—</div>
            </div>
            <div class="modal-field">
                <label>Rental Period</label>
                <div class="field-value" id="modal-dates">—</div>
            </div>
            <div class="modal-field">
                <label>Duration</label>
                <div class="field-value" id="modal-duration">—</div>
            </div>
            <div class="modal-field">
                <label>Total Cost</label>
                <div class="field-value fw-bold text-success" id="modal-total">—</div>
            </div>
            <div class="modal-field">
                <label>Status</label>
                <div class="field-value" id="modal-status">—</div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openDetailModal(data) {
    document.getElementById('modal-customer').textContent = data.customer;
    document.getElementById('modal-phone').textContent    = data.phone;
    document.getElementById('modal-vehicle').textContent  = data.vehicle;
    document.getElementById('modal-plate').textContent    = data.plate;
    document.getElementById('modal-dates').textContent    = data.start + ' – ' + data.end;
    document.getElementById('modal-duration').textContent = data.duration;
    document.getElementById('modal-total').textContent    = data.total;

    const statusMap = {
        'paid':    '<span class="badge bg-primary badge-custom"><i class="fas fa-check-circle me-1"></i> Paid</span>',
        'paid_dp': '<span class="badge bg-warning text-dark badge-custom"><i class="fas fa-clock me-1"></i> DP 50%</span>',
        'unpaid':  '<span class="badge bg-danger badge-custom"><i class="fas fa-times-circle me-1"></i> Unpaid</span>',
        'completed': '<span class="badge bg-success badge-custom"><i class="fas fa-check me-1"></i> Completed</span>',
        'active':    '<span class="badge bg-warning text-dark badge-custom"><i class="fas fa-clock me-1"></i> Active</span>',
    };
    const key = data.status === 'paid' && data.paymentType === 'dp' ? 'paid_dp' : data.status;
    document.getElementById('modal-status').innerHTML = statusMap[key]
        ?? `<span class="badge bg-secondary badge-custom">${data.status}</span>`;

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

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDetailModal(); });
</script>
@endpush

@endsection