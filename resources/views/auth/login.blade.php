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

        .login-wrapper { width: 100%; max-width: 420px; padding: 16px; }

        .brand { text-align: center; margin-bottom: 24px; }

        .brand-title {
            color: #fff; font-size: 1.8rem; font-weight: 800; letter-spacing: 1px;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .brand-title i { color: #93c5fd; }
        .brand-sub { color: rgba(255,255,255,0.65); font-size: 0.875rem; margin-top: 4px; }

        .card {
            background: #fff; border-radius: 20px; padding: 36px 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3), 0 8px 24px rgba(0,0,0,0.2);
        }

        .card-title { font-size: 1.4rem; font-weight: 700; color: #1e3a8a; margin-bottom: 4px; }
        .card-sub   { font-size: 0.85rem; color: #6b7280; margin-bottom: 28px; }

        .alert {
            border-radius: 10px; padding: 10px 14px; font-size: 14px;
            margin-bottom: 20px; display: flex; align-items: center; gap: 8px;
        }
        .alert-success { background: #dcfce7; border: 1px solid #86efac; color: #166534; }
        .alert-error   { background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }

        .form-group { margin-bottom: 20px; }

        label {
            display: block; font-size: 13px; font-weight: 600; color: #374151;
            margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;
        }

        .input-wrapper { position: relative; }

        .input-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: #9ca3af; font-size: 15px; pointer-events: none; z-index: 1;
        }

        /* ─── semua input di dalam input-wrapper ─── */
        .input-wrapper input {
            width: 100%;
            padding: 12px 44px 12px 40px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            color: #1f2937;
            background: #f9fafb;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none;
        }

        .input-wrapper input:focus {
            border-color: #1e40af;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(30,64,175,0.12);
        }

        /* error selalu menang */
        .input-wrapper input.is-error,
        .input-wrapper input.is-error:focus {
            border-color: #ef4444 !important;
            background: #fee2e2 !important;
            box-shadow: 0 0 0 3px rgba(239,68,68,0.15) !important;
        }

        /* paksa warna autofill */
        .input-wrapper input:-webkit-autofill,
        .input-wrapper input:-webkit-autofill:hover,
        .input-wrapper input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0px 1000px #f9fafb inset !important;
            -webkit-text-fill-color: #1f2937 !important;
            border-color: #e5e7eb !important;
        }
        .input-wrapper input.is-error:-webkit-autofill,
        .input-wrapper input.is-error:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0px 1000px #fee2e2 inset, 0 0 0 3px rgba(239,68,68,0.15) !important;
            border-color: #ef4444 !important;
        }

        /* ─── toggle show/hide ─── */
        .toggle-password {
            position: absolute; right: 13px; top: 50%; transform: translateY(-50%);
            background: none; border: none; padding: 4px 5px;
            cursor: pointer; color: #9ca3af; font-size: 15px;
            display: flex; align-items: center; border-radius: 5px;
            transition: color 0.2s, background 0.2s;
        }
        .toggle-password:hover { color: #1e40af; background: #eff6ff; }

        .field-error {
            color: #ef4444; font-size: 12px; margin-top: 5px;
            display: flex; align-items: center; gap: 4px;
        }

        button[type="submit"] {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, #1e3a8a, #1e40af);
            color: white; border: none; border-radius: 10px;
            font-size: 15px; font-weight: 700; cursor: pointer;
            transition: all 0.2s; box-shadow: 0 4px 16px rgba(30,58,138,0.35);
            margin-top: 8px; display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        button[type="submit"]:hover {
            background: linear-gradient(135deg, #1e40af, #1d4ed8);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(30,58,138,0.45);
        }
        button[type="submit"]:active { transform: translateY(0); }

        @media (max-width: 480px) { .card { padding: 28px 24px; } }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="brand">
        <div class="brand-title">RentalMotor <i class="fas fa-motorcycle"></i></div>
        <p class="brand-sub">Motorcycle Rental Management System</p>
    </div>

    <div class="card">
        <div class="card-title">Welcome back 👋</div>
        <p class="card-sub">Please sign in to continue</p>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
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
                    <input type="password" name="password" id="passwordInput"
                           class="{{ $errors->has('password') ? 'is-error' : '' }}"
                           autocomplete="new-password"
                           required>
                    <button type="button" class="toggle-password" id="togglePassword"
                            tabindex="-1" title="Show/hide password">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
                @if($errors->has('password'))
                    <div class="field-error">
                        <i class="fas fa-circle-exclamation"></i>
                        {{ $errors->first('password') }}
                    </div>
                @endif
            </div>

            <button type="submit">
                <i class="fas fa-right-to-bracket"></i> Sign In
            </button>
        </form>
    </div>
</div>

<script>
    /* ─── Toggle show/hide password ─── */
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input = document.getElementById('passwordInput');
        const icon  = document.getElementById('toggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
        input.focus();
    });

    /* ─── Force clear field yang error saat page load (bypass browser autofill) ─── */
    window.addEventListener('load', function () {
        @if($errors->has('password'))
            const pwInput = document.getElementById('passwordInput');
            pwInput.value = '';
            pwInput.setAttribute('autocomplete', 'new-password');
        @endif

        @if($errors->has('email'))
            const emailInput = document.querySelector('input[name="email"]');
            emailInput.value = '';
            emailInput.setAttribute('autocomplete', 'off');
        @endif
    });
</script>

</body>
</html>