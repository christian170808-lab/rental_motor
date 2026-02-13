<!DOCTYPE html>
<html>
<head>
    <title>Return Vehicle</title>
</head>
<body>

    {{-- Judul Halaman Pengembalian --}}
    <h2>Pengembalian Kendaraan: {{ $vehicle->name }}</h2>

    {{-- Pesan Error Notifikasi --}}
    @if(session('error'))
        <p style="color:red">{{ session('error') }}</p>
    @endif

    {{-- Form Proses Pengembalian --}}
    <form action="/returns/store" method="POST">
        @csrf
        
        {{-- Input Hidden untuk ID Booking --}}
        <input type="hidden" name="booking_id" value="{{ $booking->id }}">

        {{-- Detail Informasi Sewa --}}
        <p>Nama Customer: {{ $booking->customer_name }}</p>
        <p>Plat Nomor: {{ $vehicle->plate_number }}</p>
        <p>Tanggal Sewa: {{ $booking->start_date }}</p>
        <p>Tanggal Kembali (Seharusnya): {{ $booking->end_date }}</p>

        {{-- Dropdown Kondisi Kendaraan --}}
        <label>Kondisi Kendaraan</label><br>
        <select name="vehicle_condition" required>
            <option value="Baik">Baik</option>
            <option value="Rusak Ringan">Rusak Ringan</option>
            <option value="Rusak Berat">Rusak Berat</option>
        </select>
        <br><br>

        {{-- Tombol Submit Form --}}
        <button type="submit">Proses Pengembalian</button>
    </form>

</body>
</html>