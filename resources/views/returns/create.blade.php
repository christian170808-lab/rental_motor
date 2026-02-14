<!DOCTYPE html>
<html>
<head>
    <title>Return Vehicle</title>

        <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #ffffff;
            padding: 30px;
            width: 420px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        p {
            margin: 8px 0;
            font-size: 14px;
            color: #555;
        }

        label {
            font-weight: bold;
            font-size: 14px;
        }

        select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            outline: none;
        }

        select:focus {
            border-color: #2a5298;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #2a5298;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 15px;
            transition: 0.3s;
        }

        button:hover {
            background-color: #1e3c72;
        }

        .error {
            color: red;
            background: #ffe5e5;
            padding: 8px;
            border-radius: 5px;
            margin-bottom: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
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
</div>

</body>
</html>