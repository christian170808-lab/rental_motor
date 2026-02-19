<style>
    body {
        margin: 0;
        font-family: Arial, Helvetica, sans-serif;
        background: #1e1e1e;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .notif-box {
        background: #ffffff;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        text-align: center;
        max-width: 420px;
        animation: pop 0.4s ease;
        border-top: 6px solid #22c55e;
    }

    .notif-title {
        color: #16a34a;
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .notif-text {
        color: #333;
        font-size: 14px;
        margin-top: 8px;
    }

    @keyframes pop {
        from {
            transform: scale(0.9);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>

@if(session('success'))
    <div class="notif-box">
        <div class="notif-title">✅ Success</div>
        <div>{{ session('success') }}</div>
        <p class="notif-text">Redirecting to the booking page in 3 seconds...</p>
    </div>

    <script>
        setTimeout(function(){
            window.location.href = "{{ route('booking.index') }}";
        }, 3000);
    </script>
@endif