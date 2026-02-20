<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Motor</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { margin: 0; }
        .main-content {
            margin-left: 250px;
            padding: 25px;
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

{{-- TOAST NOTIFICATION --}}
@if(session('success') || session('error'))

<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
    <symbol id="check-circle-fill" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
    </symbol>
    <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
    </symbol>
</svg>

<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999; min-width: 350px;">
    <div id="toastNotif" class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} d-flex align-items-center shadow" role="alert">
        <svg class="bi flex-shrink-0 me-2" width="16" height="16" role="img">
            @if(session('success'))
                <use xlink:href="#check-circle-fill"/>
            @else
                <use xlink:href="#exclamation-triangle-fill"/>
            @endif
        </svg>
        <div class="fw-semibold">
            {{ session('success') ?? session('error') }}
        </div>
        <button type="button" class="btn-close ms-auto" onclick="document.getElementById('toastNotif').style.display='none'"></button>
    </div>
</div>

<script>
    setTimeout(function() {
        const notif = document.getElementById('toastNotif');
        if (notif) {
            notif.style.transition = 'opacity 0.8s ease';
            notif.style.opacity = '0';
            setTimeout(() => notif.style.display = 'none', 800);
        }
    }, 5000);
</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

</body>
</html>