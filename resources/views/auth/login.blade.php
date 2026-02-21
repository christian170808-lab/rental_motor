<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentalMotor — Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #1d4ed8 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 16px;
        }

        /* Brand logo at top */
        .brand {
            text-align: center;
            margin-bottom: 24px;
        }

        .brand-title {
            color: #ffffff;
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .brand-title i {
            color: #93c5fd;
            margin-left: 8px;
        }

        .brand-sub {
            color: rgba(255,255,255,0.65);
            font-size: 0.875rem;
            margin-top: 4px;
        }

        /* Card */
        .card {
            background: #ffffff;
            border-radius: 20px;
            padding: 36px 40px;
            box-shadow:
                0 20px 60px rgba(0,0,0,0.3),
                0 8px 24px rgba(0,0,0,0.2);
        }

        .card-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 6px;
        }

        .card-sub {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 28px;
        }

        /* Alerts */
        .alert-success {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #166534;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 15px;
        }

        input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            color: #1f2937;
            background: #f9fafb;
            transition: all 0.2s;
            outline: none;
        }

        input:focus {
            border-color: #1e40af;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(30,64,175,0.12);
        }

        /* Error field highlight */
        input.is-error {
            border-color: #ef4444;
            background: #fff5f5;
        }

        .field-error {
            color: #ef4444;
            font-size: 12px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Button */
        button[type="submit"] {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #1e3a8a, #1e40af);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(30,58,138,0.35);
            margin-top: 8px;
            letter-spacing: 0.3px;
        }

        button[type="submit"]:hover {
            background: linear-gradient(135deg, #1e40af, #1d4ed8);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(30,58,138,0.45);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        @media (max-width: 480px) {
            .card { padding: 28px 24px; }
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    <!-- Brand -->
    <div class="brand">
        <div class="brand-title">
            RentalMotor <i class="fas fa-motorcycle"></i>
        </div>
        <p class="brand-sub">Motorcycle Rental Management System</p>
    </div>

    <!-- Card -->
    <div class="card">
        <div class="card-title">Welcome back 👋</div>
        <p class="card-sub">Please sign in to continue</p>

        {{-- Success message --}}
        @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- Error messages --}}
        @if($errors->has('email') || $errors->has('password'))
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first('email') ?? $errors->first('password') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.process') }}">
            @csrf

            <div class="form-group">
                <label>Email</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email"
                           value="{{ old('email') }}"
                           placeholder="admin@example.com"
                           class="{{ $errors->has('email') ? 'is-error' : '' }}"
                           required autofocus>
                </div>
                @if($errors->has('email'))
                    <div class="field-error">
                        <i class="fas fa-circle-exclamation"></i>
                        {{ $errors->first('email') }}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password"
                           placeholder="••••••••"
                           class="{{ $errors->has('password') ? 'is-error' : '' }}"
                           required>
                </div>
                @if($errors->has('password'))
                    <div class="field-error">
                        <i class="fas fa-circle-exclamation"></i>
                        {{ $errors->first('password') }}
                    </div>
                @endif
            </div>

            <button type="submit">
                <i class="fas fa-right-to-bracket me-2"></i> Sign In
            </button>
        </form>
    </div>

</div>

</body>
</html>