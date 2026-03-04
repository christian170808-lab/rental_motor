@extends('layouts.app')

@push('styles')
<style>
    /* ─── PAGE HEADER ─── */
    .page-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 60%, #1d4ed8 100%);
        border-radius: 16px;
        padding: 22px 28px;
        margin-bottom: 20px;
        box-shadow: 0 8px 32px rgba(37, 99, 235, 0.25);
    }

    .page-header h2 {
        color: #fff;
        font-weight: 700;
        margin: 0;
        font-size: 1.5rem;
    }

    .page-header p {
        color: rgba(255, 255, 255, 0.7);
        margin: 4px 0 0;
        font-size: 0.9rem;
    }

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
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .stat-icon.revenue {
        background: #fef3c7;
        color: #f59e0b;
    }

    .stat-icon.transactions {
        background: #d1fae5;
        color: #10b981;
    }

    .stat-content h4 {
        font-size: 14px;
        color: #6b7280;
        margin: 0 0 4px;
        font-weight: 500;
    }

    .stat-content p {
        font-size: 24px;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    /* ─── TABLE CARD ─── */
    .table-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .table-scroll {
        overflow-x: auto;
    }

    .table thead th {
        background: linear-gradient(90deg, #1e3a8a, #1d4ed8);
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        padding: 14px 16px;
        border: none;
        text-align: center;
    }

    .table td {
        padding: 14px 16px;
        font-size: 14px;
        text-align: center;
    }

    .table tbody tr:hover {
        background: #f8fafc;
    }

    .badge-custom {
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
    }

    /* ─── BUTTON ─── */
    .btn-view {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border: none;
        color: #fff;
        border-radius: 8px;
        padding: 7px 16px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-view:hover {
        transform: translateY(-1px);
        color: #fff;
    }

    /* ─── RECEIPT MODAL ─── */
    .booking-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .booking-modal-overlay.active {
        display: flex;
    }

    .booking-modal {
        background: #fff;
        border-radius: 16px;
        max-width: 400px;
        width: 100%;
        font-family: 'Courier New', monospace;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
    }

    .receipt-header {
        padding: 20px;
        text-align: center;
    }

    .receipt-body {
        padding: 0 24px 20px 24px;
    }

    .receipt-body p {
        margin: 6px 0;
        font-size: 14px;
    }

    .receipt-body hr {
        border-top: 1px dashed #000;
        margin: 12px 0;
    }

    .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
        text-decoration: none;
    }

    .page-btn.active {
        background: #3b82f6;
        color: #fff;
    }

    .page-btn[disabled] {
        opacity: 0.4;
    }
</style>
@endpush

@section('content')
<div class="container mt-4">

    <div class="page-header">
        <h2><i class="fas fa-credit-card me-2"></i> Payment History</h2>
        <p>Complete motorcycle rental payment records</p>
    </div>

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
                            <td class="fw-bold text-success">
                                Rp {{ number_format($payment->total_price, 0, ',', '.') }}
                            </td>
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
                                <button class="btn-view" onclick="openDetailModal({
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
                            <td colspan="5" class="text-center py-4">No payment records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- RECEIPT MODAL --}}
<div class="booking-modal-overlay" id="detailModalOverlay" onclick="closeOnOverlay(event)">
    <div class="booking-modal">
        <div class="receipt-header">
            <h5>RENTAL MOTOR</h5>
            <small>Denpasar, Bali</small>
            <hr>
            <strong>PAYMENT RECEIPT</strong>
        </div>

        <div class="receipt-body">
            <p><strong>Customer :</strong> <span id="modal-customer"></span></p>
            <p><strong>Phone :</strong> <span id="modal-phone"></span></p>
            <p><strong>Motor :</strong> <span id="modal-vehicle"></span></p>

            <hr>

            <p><strong>Rental Period :</strong></p>
            <p id="modal-dates"></p>

            <hr>

            <p><strong>Total Payment :</strong></p>
            <h5 class="text-end fw-bold text-success" id="modal-total"></h5>

            <hr>

            <p class="text-center" id="modal-status"></p>

            <div class="text-center mt-3">
                <small>Thank you for your trust 🙏</small>
            </div>
        </div>

        <div class="text-center pb-3">
            <button class="btn btn-sm btn-secondary" onclick="closeDetailModal()">Close</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openDetailModal(data) {
        document.getElementById('modal-customer').textContent = data.customer;
        document.getElementById('modal-phone').textContent = data.phone;
        document.getElementById('modal-vehicle').textContent = data.vehicle;
        document.getElementById('modal-dates').textContent = data.start + ' – ' + data.end;
        document.getElementById('modal-total').textContent = data.total;

        const statusMap = {
            'paid': '<span class="badge bg-primary badge-custom">Paid</span>',
            'completed': '<span class="badge bg-success badge-custom">Completed</span>'
        };

        document.getElementById('modal-status').innerHTML =
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

@endsection