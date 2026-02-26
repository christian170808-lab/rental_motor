<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>RentalMotor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        img { max-width: 100%; height: auto; }
        body { margin: 0; background: #f8fafc; }
        .main-content { margin-left: 250px; padding: 25px; transition: margin-left 0.3s; }
        .table-responsive-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 15px; }
        }
        @media (max-width: 576px) {
            .main-content { padding: 10px; }
            .container { padding-left: 10px; padding-right: 10px; }
            table { font-size: 12px; }
            .btn { font-size: 12px; padding: 4px 8px; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="d-flex">
    @include('layouts.sidebar')
    <div class="main-content w-100">
        @yield('content')
    </div>
</div>

{{-- Toast Notification --}}
@if(session('success') || session('error'))
<div style="position:fixed;top:20px;right:20px;z-index:99999;min-width:340px;">
    <div id="toastNotif"
         class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} d-flex align-items-center shadow"
         role="alert" style="border-radius:12px;padding:14px 18px;">
        <i class="fas {{ session('success') ? 'fa-check-circle' : 'fa-exclamation-circle' }} me-2 fs-5"></i>
        <div class="fw-semibold flex-grow-1">{{ session('success') ?? session('error') }}</div>
        <button type="button" class="btn-close ms-2"
                onclick="document.getElementById('toastNotif').remove()"></button>
    </div>
</div>
<script>
    setTimeout(function() {
        const el = document.getElementById('toastNotif');
        if (el) { el.style.transition = 'opacity 0.8s'; el.style.opacity = '0'; setTimeout(() => el.remove(), 800); }
    }, 5000);
</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>