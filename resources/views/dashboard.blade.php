@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h2 class="mb-4 fw-bold">Dashboard</h2>

    <!-- STAT CARDS -->
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <small>Total Motor</small>
                    <h4 class="fw-bold">{{ $totalMotor }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <small>Motor Tersedia</small>
                    <h4 class="fw-bold text-success">{{ $motorTersedia }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <small>Motor Disewa</small>
                    <h4 class="fw-bold text-warning">{{ $motorDisewa }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <small>Pendapatan</small>
                    <h4 class="fw-bold text-primary">
                        Rp {{ number_format($pendapatan, 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>

    </div>

    <!-- DATA MOTOR -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Data Motor</h6>
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Nama Motor</th>
                        <th>Plat</th>
                        <th>Status</th>
                        <th>Harga/Hari</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Models\Vehicle::latest()->take(15)->get() as $motor)
                    <tr>
                        <td>{{ $motor->name }}</td>
                        <td>{{ $motor->plate_number }}</td>
                        <td>
                            @if($motor->status == 'available')
                                <span class="badge bg-success">Tersedia</span>
                            @else
                                <span class="badge bg-warning">Disewa</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($motor->price_per_day,0,',','.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- CHART -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Penyewaan Bulanan</h6>
            <canvas id="chart"></canvas>
        </div>
    </div>

    @push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    const ctx = document.getElementById('chart');

    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    'Jan','Feb','Mar','Apr','Mei','Jun',
                    'Jul','Agu','Sep','Okt','Nov','Des'
                ],
                datasets: [{
                    label: 'Total Penyewaan',
                    data: @json($chartData),
                    borderWidth: 1
                }]
            },
        });
    }

});
</script>
@endpush

</div>
@endsection
