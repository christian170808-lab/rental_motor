<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengembalian</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #1f2937, #111827);
            padding: 40px 20px;
        }

        .page-card {
            background: white;
            max-width: 950px;
            margin: auto;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.25);
            animation: fadeIn 0.4s ease;
        }

        h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #111827;
            letter-spacing: 0.5px;
        }

        /* Alert sukses */
        .alert {
            background: #dcfce7;
            color: #166534;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
            border-left: 6px solid #22c55e;
        }

        /* Toolbar */
        .search-form {
            margin-bottom: 20px;
        }

        .btn-back {
            text-decoration: none;
            padding: 10px 18px;
            background: #6b7280;
            color: white;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-back:hover {
            background: #4b5563;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            border-radius: 10px;
        }

        thead {
            background: #111827;
            color: white;
        }

        th, td {
            padding: 14px 12px;
            text-align: center;
        }

        th {
            font-size: 14px;
            letter-spacing: 0.4px;
        }

        tbody tr {
            border-bottom: 1px solid #e5e7eb;
            transition: 0.2s;
        }

        tbody tr:hover {
            background: #f3f4f6;
        }

        tbody tr:nth-child(even) {
            background: #fafafa;
        }

        /* Kondisi badge */
        .badge {
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-good {
            background: #dcfce7;
            color: #166534;
        }

        .badge-damage {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Animasi */
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(10px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>

<body>

<div class="page-card">

    <h2>Data Pengembalian Kendaraan</h2>

    @if(session('success'))
        <div class="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="search-form">
        <a href="{{ url('/booking') }}" class="btn-back">
            ← Kembali ke Dashboard
        </a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Tanggal Kembali</th>
                <th>Terlambat (Hari)</th>
                <th>Denda</th>
                <th>Kondisi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($returns as $return)
                <tr>
                    <td>{{ $return->booking_id }}</td>
                    <td>{{ \Carbon\Carbon::parse($return->return_date)->format('d-m-Y') }}</td>
                    <td>{{ $return->late_days }}</td>
                    <td>Rp {{ number_format($return->penalty, 0, ',', '.') }}</td>
                    <td>
                        @if(strtolower($return->vehicle_condition) == 'baik')
                            <span class="badge badge-good">Baik</span>
                        @else
                            <span class="badge badge-damage">{{ $return->vehicle_condition }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        Belum ada data atau hasil pencarian tidak ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

</body>
</html>
