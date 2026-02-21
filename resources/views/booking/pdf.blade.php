<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Booking Report - {{ $booking->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table-data th, 
        .table-data td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table-data th {
            background-color: #f2f2f2;
            width: 35%;
            text-align: left;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-style: italic;
        }
        .ktp-image {
            max-width: 200px;
            height: auto;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>Vehicle Booking Report</h2>
    <p>Official Rental Receipt</p>
</div>

<table class="table-data">
    <tr>
        <th>ID Booking</th>
        <td>{{ $booking->id }}</td>
    </tr>

    <tr>
        <th>Customer Code</th>
        <td>{{ optional($booking->customer)->customer_id ?? '-' }}</td>
    </tr>

    <tr>
        <th>Customer Name</th>
        <td>{{ optional($booking->customer)->customer_name ?? '-' }}</td>
    </tr>

    @if($booking->identity_card)
    <tr>
        <th>ID Card Photo</th>
        <td>
            @if(!empty($ktpDataUri))
                <img src="{{ $ktpDataUri }}" alt="Foto KTP" class="ktp-image">
            @else
                Not available
            @endif
        </td>
    </tr>
    @endif

    <tr>
        <th>Vehicle</th>
        <td>
            {{ $booking->vehicle->name }} 
            ({{ $booking->vehicle->plate_number }})
        </td>
    </tr>

    <tr>
        <th>Rental Start Date</th>
        <td>
            {{ \Carbon\Carbon::parse($booking->start_date)->format('d-m-Y') }}
        </td>
    </tr>

    <tr>
        <th>Rental End Date</th>
        <td>
            {{ \Carbon\Carbon::parse($booking->end_date)->format('d-m-Y') }}
        </td>
    </tr>

    <tr class="total-row">
        <th>Total Cost</th>
        <td>
            Rp {{ number_format($booking->total_cost, 0, ',', '.') }}
        </td>
    </tr>

    <tr>
        <th>Payment Status</th>
        <td style="text-transform: uppercase;">
            {{ $booking->payment_status }}
        </td>
    </tr>
</table>

<div class="footer">
    Printed on: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}
</div>

</body>
</html>
