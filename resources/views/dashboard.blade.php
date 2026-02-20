@extends('layouts.app')

@push('styles')
<style>
/* ===============================
   CARD & STAT AREA
================================*/
.stat-card {
    border-radius: 16px;
    border: none;
    background: #ffffff;
    transition: all 0.3s ease;
    box-shadow: 
        0 4px 10px rgba(0,0,0,0.05),
        0 10px 25px rgba(0,0,0,0.08);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 
        0 10px 25px rgba(0,0,0,0.12),
        0 20px 45px rgba(0,0,0,0.15);
}

.stat-card h4 {
    font-size: 28px;
    margin-top: 6px;
    margin-bottom: 0;
}

.stat-card small {
    color: #6b7280;
    font-weight: 500;
}

/* ===============================
   TABLE AREA (Data Motor)
================================*/
.table-wrapper {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: none !important;
}

.table {
    margin-bottom: 0;
    background: white;
}

.table thead th {
    background: #ffffff;
    color: #374151;
    font-weight: 600;
    border-bottom: 2px solid #e5e7eb;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table tbody tr {
    transition: background-color 0.2s;
}

.table tbody tr:hover {
    background: #f1f5f9;
}

.table td,
.table th {
    padding: 12px 14px;
    vertical-align: middle;
}

.badge {
    padding: 6px 12px;
    font-size: 0.875rem;
    border-radius: 8px;
    font-weight: 500;
}
</style>
@endpush

@section('content')
<div class="container py-4">

    <h2 class="mb-4 fw-bold text-dark">Dashboard</h2>

    <!-- Stat Cards -->
    <div class="row g-3 mb-5">
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <small>Total Motor</small>
                    <h4 class="fw-bold">{{ $totalMotor }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <small>Motor Tersedia</small>
                    <h4 class="fw-bold text-success">{{ $motorTersedia }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <small>Motor Disewa</small>
                    <h4 class="fw-bold text-warning">{{ $motorDisewa }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <small>Pendapatan</small>
                    <h4 class="fw-bold text-primary">
                        Rp {{ number_format($pendapatan, 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Motor -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Data Motor</h6>
        <div style="max-height: 200px; overflow-y: auto;"> {{-- ← dari 350px jadi 200px --}}
            <table class="table table-sm align-middle"> {{-- ← tambah table-sm --}}
                <thead class="sticky-top bg-white">
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
</div>
    <!-- Chart -->
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold mb-4 text-dark">Penyewaan Bulanan</h5>
            <canvas id="chart" style="max-height: 350px;"></canvas>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById('chart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
            datasets: [{
                label: 'Total Penyewaan',
                data: @json($chartData ?? array_fill(0, 12, 0)),
                backgroundColor: 'rgba(59, 130, 246, 0.6)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>
@endpush
@endsection