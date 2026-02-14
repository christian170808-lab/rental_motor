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

        <p>Customer Name: {{ $booking->customer_name }}</p>
        <p>Plate Number: {{ $vehicle->plate_number }}</p>
        <p>Rental Date: {{ $booking->start_date }}</p>
        <p>Return Date (Expected): {{ $booking->end_date }}</p>

        <label>Vehicle Condition</label><br>
        <select name="vehicle_condition" required>
            <option value="Good">Good</option>
            <option value="Minor Damage">Minor Damage</option>
            <option value="Major Damage">Major Damage</option>
        </select>
        <br><br>

        <button type="submit">Process Return</button>
    </form>

</body>
</html>