<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rental Report</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }

        .header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 3px solid #1e3a8a;
        }
        .header h2 { font-size: 20px; color: #1e3a8a; margin-bottom: 4px; }
        .header p  { color: #666; font-size: 11px; }

        .booking-card {
            border: 1px solid #d1d5db;
            margin-bottom: 16px;
            page-break-inside: avoid;
        }

        .card-header {
            background: #1e3a8a;
            padding: 9px 16px;
        }
        .card-header table { width: 100%; border-collapse: collapse; }
        .card-header td { padding: 0; }
        .booking-id { font-size: 14px; font-weight: 700; color: #fff; }
        .badge { font-size: 11px; padding: 2px 10px; border-radius: 10px; font-weight: 600; color: #fff; }
        .badge-completed { background: #16a34a; }
        .badge-active    { background: #d97706; }
        .badge-other     { background: #6b7280; }

        .card-body { padding: 0; }

        .field-row {
            border-bottom: 1px solid #f1f5f9;
            padding: 8px 16px;
        }
        .field-row:last-child { border-bottom: none; }
        .field-label {
            font-size: 10px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 2px;
        }
        .field-value {
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
        }
        .val-blue { color: #1e3a8a; }
        .val-red  { color: #dc2626; }
        .val-gray { color: #9ca3af; font-weight: 400; }

        .section-divider {
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            padding: 5px 16px;
            font-size: 10px;
            font-weight: 700;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .img-label {
            font-size: 10px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 6px;
        }
        .no-image {
            background: #f8fafc;
            border: 1px dashed #d1d5db;
            height: 60px;
            text-align: center;
            line-height: 60px;
            color: #9ca3af;
            font-size: 11px;
        }

        .footer {
            margin-top: 16px;
            text-align: right;
            font-size: 10px;
            color: #888;
            font-style: italic;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Motorcycle Rental Report</h2>
    <p>Printed on: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}</p>
</div>

@forelse($bookings as $b)
@php
    $durasi  = \Carbon\Carbon::parse($b->start_date)->diffInDays(\Carbon\Carbon::parse($b->end_date)) + 1;
    $penalty = optional($b->returnVehicle)->penalty ?? 0;
@endphp

<div class="booking-card">

    {{-- Header --}}
    <div class="card-header">
        <table>
            <tr>
                <td class="booking-id">Booking #{{ $b->id }}</td>
                <td style="text-align:right;">
                    @if($b->payment_status == 'completed')
                        <span class="badge badge-completed">Completed</span>
                    @elseif($b->payment_status == 'paid')
                        <span class="badge badge-active">Active</span>
                    @else
                        <span class="badge badge-other">{{ ucfirst($b->payment_status) }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="card-body">

        {{-- DATA CUSTOMER --}}
        <div class="section-divider">Customer Data</div>

        <div class="field-row">
            <div class="field-label">Customer</div>
            <div class="field-value">{{ optional($b->customer)->customer_name ?? '—' }}</div>
        </div>
        <div class="field-row">
            <div class="field-label">Phone Number</div>
            <div class="field-value">{{ optional($b->customer)->phone_number ?? '—' }}</div>
        </div>
        <div class="field-row">
            <div class="field-label">Address</div>
            <div class="field-value">{{ optional($b->customer)->address ?? '—' }}</div>
        </div>

        {{-- DATA KENDARAAN --}}
        <div class="section-divider">Vehicle Data</div>

        <div class="field-row">
            <div class="field-label">Vehicle</div>
            <div class="field-value">{{ optional($b->vehicle)->name ?? '—' }}</div>
        </div>
        <div class="field-row">
            <div class="field-label">Plate Number</div>
            <div class="field-value">{{ optional($b->vehicle)->plate_number ?? '—' }}</div>
        </div>
        <div class="field-row">
            <div class="field-label">Type</div>
            <div class="field-value">{{ ucfirst(optional($b->vehicle)->type ?? '—') }}</div>
        </div>

        {{-- DATA SEWA --}}
        <div class="section-divider">Rental Details</div>

        <div class="field-row">
            <div class="field-label">Start Date</div>
            <div class="field-value">{{ \Carbon\Carbon::parse($b->start_date)->format('d M Y') }}</div>
        </div>
        <div class="field-row">
            <div class="field-label">End Date</div>
            <div class="field-value">{{ \Carbon\Carbon::parse($b->end_date)->format('d M Y') }}</div>
        </div>
        <div class="field-row">
            <div class="field-label">Duration</div>
            <div class="field-value">{{ $durasi }} hari</div>
        </div>
        <div class="field-row">
            <div class="field-label">Total Cost</div>
            <div class="field-value val-blue">Rp {{ number_format($b->total_cost, 0, ',', '.') }}</div>
        </div>
        <div class="field-row">
            <div class="field-label">Penalty</div>
            <div class="field-value {{ $penalty > 0 ? 'val-red' : 'val-gray' }}">
                {{ $penalty > 0 ? 'Rp ' . number_format($penalty, 0, ',', '.') : '—' }}
            </div>
        </div>

        {{-- GAMBAR --}}
        <div class="section-divider">Documents</div>

        <div style="display: table; width: 100%; padding: 12px 16px; box-sizing: border-box;">
            <div style="display: table-cell; width: 50%; padding-right: 8px; vertical-align: top;">
                <div class="img-label">ID Card</div>
                @if(!empty($b->ktpDataUri))
                    <img src="{{ $b->ktpDataUri }}" alt="KTP"
                         style="max-width:100%; max-height:160px; height:auto; border:1px solid #e5e7eb; object-fit:contain;">
                @else
                    <div class="no-image">No Image</div>
                @endif
            </div>
            <div style="display: table-cell; width: 50%; padding-left: 8px; vertical-align: top;">
                <div class="img-label">Payment Proof</div>
                @if(!empty($b->proofDataUri))
                    <img src="{{ $b->proofDataUri }}" alt="Payment Proof"
                         style="max-width:100%; max-height:160px; height:auto; border:1px solid #e5e7eb; object-fit:contain;">
                @else
                    <div class="no-image">No Image</div>
                @endif
            </div>
        </div>

    </div>
</div>

@empty
<p style="text-align:center; color:#888; padding:20px;">No data found.</p>
@endforelse

<div class="footer">
    Total Records: {{ $bookings->count() }} &nbsp;|&nbsp; Generated: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}
</div>

</body>
</html>