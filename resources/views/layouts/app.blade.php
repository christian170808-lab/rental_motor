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
<body >

<div class="d-flex">
    @include('layouts.sidebar')

    <div class="main-content w-100">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')


</body>
</html>
