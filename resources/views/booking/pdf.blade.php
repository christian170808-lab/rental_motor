<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Booking - {{ $booking->id }}</title>
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
            padding: 0;
        }
        .table-data {                
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table-data th, .table-data td {                
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table-data th {                
            background-color: #f2f2f2;
            width: 30%;
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
        <h2>Laporan Booking Kendaraan</h2>
        <p>Bukti penyewaan resmi</p>
    </div>

    <table class="table-data">
        <tr>
            <th>ID Booking</th>
            <td>{{ $booking->id }}</td>
        </tr>
        <tr>
            <th>Nama Pelanggan</th>
            <td>{{ $booking->customer_name }}</td>
        </tr>
        
        @if($booking->identity_card)
        <tr>
            <th>Foto KTP</th>
            <td>
                @if(!empty($ktpDataUri))
                    <img src="{{ $ktpDataUri }}" alt="Foto KTP" class="ktp-image">
                @else
                    No Image
                @endif
            </td>
        </tr>
        @endif

        <tr>
            <th>Kendaraan</th>
            <td>{{ $booking->vehicle->name }} ({{ $booking->vehicle->plate_number }})</td>
        </tr>
        <tr>
            <th>Tanggal Mulai Sewa</th>
            <td>{{ \Carbon\Carbon::parse($booking->start_date)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <th>Tanggal Selesai Sewa</th>
            <td>{{ \Carbon\Carbon::parse($booking->end_date)->format('d-m-Y') }}</td>
        </tr>
        <tr class="total-row">
            <th>Total Biaya</th>
            <td>Rp {{ number_format($booking->total_cost, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Status Pembayaran</th>
            <td style="text-transform: uppercase;">{{ $booking->payment_status }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}</p>
    </div>
</body>
</html>