<!DOCTYPE html>
<html>
<head>
    <title>Return Vehicle</title>
</head>
<body>

    <h2>Pengembalian Kendaraan: {{ $vehicle->name }}</h2>

    @if(session('error'))
        <p style="color:red">{{ session('error') }}</p>
    @endif

    <form action="/returns/store" method="POST">
        @csrf
        
        <input type="hidden" name="booking_id" value="{{ $booking->id }}">

        <p>Nama Customer: {{ $booking->customer_name }}</p>
        <p>Plat Nomor: {{ $vehicle->plate_number }}</p>
        <p>Tanggal Sewa: {{ $booking->start_date }}</p>
        <p>Tanggal Kembali (Seharusnya): {{ $booking->end_date }}</p>

        <label>Kondisi Kendaraan</label><br>
        <select name="vehicle_condition" required>
            <option value="Baik">Baik</option>
            <option value="Rusak Ringan">Rusak Ringan</option>
            <option value="Rusak Berat">Rusak Berat</option>
        </select>
        <br><br>

        <button type="submit">Proses Pengembalian</button>
    </form>

</body>
</html>