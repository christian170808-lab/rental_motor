<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #1e1e1e;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: #f5f5f5;
            width: 380px;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
        }

        h2 {
            text-align: center;
            color: #1e40ff;
            margin-bottom: 30px;
        }

        label {
            font-size: 14px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 18px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        input:focus {
            border-color: #1e40ff;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #1e40ff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
        }

        button:hover {
            background: #1632cc;
        }

        .msg-success {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }

        .msg-error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Login</h2>

    @if(session('success'))
        <p class="msg-success">{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <p class="msg-error">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('login.process') }}">
        @csrf
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>