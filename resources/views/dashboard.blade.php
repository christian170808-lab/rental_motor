@extends('layouts.app')

@push('styles')
<style>
.page-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 60%, #1d4ed8 100%);
    border-radius: 16px;
    padding: 22px 28px;
    margin-bottom: 24px;
    box-shadow: 0 8px 32px rgba(37,99,235,0.25);
}
.page-header h2 { color: #fff; font-weight: 700; margin: 0; font-size: 1.5rem; }
.page-header p  { color: rgba(255,255,255,0.7); margin: 4px 0 0; font-size: 0.9rem; }
.stat-card {
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    background: #fff;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.12);
}
.stat-card .icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    margin-bottom: 12px;
}
.stat-card h4 { font-size: 22px; font-weight: 700; margin: 4px 0 0; }
.stat-card small { color: #6b7280; font-weight: 500; font-size: 13px; }
.section-title {
    font-weight: 700;
    padding-bottom: 10px;
    margin-bottom: 20px;
    position: relative;
    color: #1e3a8a;
    font-size: 1rem;
}
.section-title::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0;
    width: 50px; height: 4px;
    background: linear-gradient(90deg, #1e3a8a, #3b82f6);
    border-radius: 2px;
}
.table-card {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07);
    border: 1px solid #e5e7eb;
}
.table thead th {
    position: sticky; top: 0; z-index: 1;
    background: linear-gradient(90deg, #1e3a8a, #1d4ed8);
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 13px 16px;
    border: none;
    text-align: center;
}
.table td { padding: 12px 16px; vertical-align: middle; font-size: 14px; border-color: #f1f5f9; text-align: center; }
.table tbody tr:hover { background: #eff6ff; }
.chart-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07);
    border: 1px solid #e5e7eb;
    padding: 1.5rem;
}

/* Chart summary stats */
.chart-summary {
    display: flex;
    gap: 24px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}
.chart-summary-item {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 10px 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.chart-summary-item .cs-icon {
    width: 36px; height: 36px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
}
.chart-summary-item .cs-label { font-size: 12px; color: #6b7280; font-weight: 500; }
.chart-summary-item .cs-value { font-size: 16px; font-weight: 700; color: #111827; margin: 0; }
</style>
@endpush

@section('content')
<div class="container py-4" style="max-width: 1200px;">

    <div class="page-header">
        <h2><i class="fas fa-tachometer-alt me-2"></i> Dashboard</h2>
        <p>Welcome back! Here's your rental overview.</p>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4 align-items-stretch">
        <div class="col-md-3 col-sm-6 col-6">
            <a href="{{ route('vehicles.index') }}" class="text-decoration-none">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="icon bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-motorcycle"></i>
                        </div>
                        <small>Total Vehicles</small>
                        <h4 class="text-primary">{{ $totalMotor }}</h4>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 col-6">
            <a href="{{ route('vehicles.index', ['status' => 'available']) }}" class="text-decoration-none">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="icon bg-success bg-opacity-10 text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <small>Available</small>
                        <h4 class="text-success">{{ $motorTersedia }}</h4>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 col-6">
            <a href="{{ route('booking.index') }}" class="text-decoration-none">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="icon bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-key"></i>
                        </div>
                        <small>Rented</small>
                        <h4 class="text-warning">{{ $motorDisewa }}</h4>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 col-6">
          <a href="{{ route('reports.index', ['view' => 'payment']) }}" class="text-decoration-none">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="icon bg-info bg-opacity-10 text-info">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <small>Revenue</small>
                        <h4 class="text-info" style="font-size:20px;">
                            Rp {{ number_format($pendapatan, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Rented Vehicle Table --}}
    <div class="mb-4">
        <h5 class="section-title">Rented Vehicles</h5>
        <div class="table-card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Vehicle</th>
                            <th>Name</th>
                            <th>Plate</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rentedVehicles as $motor)
                        <tr>
                            <td>
                                <img src="{{ asset('image/' . ($motor->image ?? 'default.png')) }}"
                                     style="width:70px; height:52px; object-fit:cover; border-radius:8px; border:1px solid #e5e7eb;">
                            </td>
                            <td class="fw-semibold">{{ $motor->name }}</td>
                            <td><code style="color:#1e40af;">{{ $motor->plate_number }}</code></td>
                            <td><span class="badge bg-warning text-dark">Rented</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted fst-italic">
                                No vehicles currently rented.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($rentedVehicles->hasPages())
            <div class="d-flex justify-content-center py-3">
                {{ $rentedVehicles->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>

    {{-- Rental Statistics Chart --}}
    <h5 class="section-title">Rental Statistics</h5>
    <div class="chart-card">

        {{-- Filter Buttons --}}
        <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
            <button class="btn btn-sm btn-primary"         onclick="loadChart('daily')"   id="btn-daily">
                <i class="fas fa-calendar-day me-1"></i> Daily
            </button>
            <button class="btn btn-sm btn-outline-primary" onclick="loadChart('weekly')"  id="btn-weekly">
                <i class="fas fa-calendar-week me-1"></i> Weekly
            </button>
            <button class="btn btn-sm btn-outline-primary" onclick="loadChart('monthly')" id="btn-monthly">
                <i class="fas fa-calendar-alt me-1"></i> Monthly
            </button>
            <button class="btn btn-sm btn-outline-primary" onclick="loadChart('yearly')"  id="btn-yearly">
                <i class="fas fa-calendar me-1"></i> Year
            </button>
            <button class="btn btn-sm btn-outline-primary" onclick="loadChart('all')"     id="btn-all">
                <i class="fas fa-infinity me-1"></i> All
            </button>
            <div class="d-flex gap-2 align-items-center ms-auto flex-wrap">
                <input type="date" id="date-from" class="form-control form-control-sm" style="width:140px;">
                <span class="text-muted">-</span>
                <input type="date" id="date-to" class="form-control form-control-sm" style="width:140px;">
                <button class="btn btn-sm btn-outline-secondary" onclick="loadChart('custom')">
                    <i class="fas fa-filter me-1"></i> Custom
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="resetChart()">
                    <i class="fas fa-undo me-1"></i> Reset
                </button>
            </div>
        </div>

        {{-- Summary Stats (di atas grafik) --}}
        <div class="chart-summary" id="chart-summary">
            <div class="chart-summary-item">
                <div class="cs-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-motorcycle"></i>
                </div>
                <div>
                    <div class="cs-label">Total Rentals</div>
                    <div class="cs-value" id="summary-rentals">-</div>
                </div>
            </div>
            <div class="chart-summary-item">
                <div class="cs-icon bg-success bg-opacity-10 text-success">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <div class="cs-label">Total Revenue</div>
                    <div class="cs-value text-success" id="summary-revenue">-</div>
                </div>
            </div>
        </div>

        {{-- Canvas --}}
        <div id="chart-wrapper" style="height: 280px;">
            <canvas id="chart"></canvas>
        </div>
        <div id="chart-empty" style="display:none; text-align:center; padding: 60px 20px;">
            <i class="fas fa-chart-bar fa-3x mb-3" style="color:#d1d5db;"></i>
            <p class="fw-semibold text-muted mb-1">No rental data yet</p>
            <small class="text-muted">Data will appear here once rentals are recorded</small>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let chartInstance = null;

function setActiveBtn(type) {
    ['daily', 'weekly', 'monthly', 'yearly', 'all'].forEach(t => {
        const btn = document.getElementById('btn-' + t);
        if (!btn) return;
        btn.classList.toggle('btn-primary', t === type);
        btn.classList.toggle('btn-outline-primary', t !== type);
    });
}

function formatRupiah(num) {
    if (num >= 1000000000) return 'Rp ' + (num / 1000000000).toFixed(1) + 'M';
    if (num >= 1000000)    return 'Rp ' + (num / 1000000).toFixed(1) + 'Jt';
    if (num >= 1000)       return 'Rp ' + (num / 1000).toFixed(0) + 'K';
    return 'Rp ' + num;
}

function loadChart(type) {
    setActiveBtn(type);

    let url = '/dashboard/chart?type=' + type;
    if (type === 'custom') {
        const from = document.getElementById('date-from').value;
        const to   = document.getElementById('date-to').value;
        if (!from || !to) { alert('Pilih tanggal mulai dan akhir!'); return; }
        url += '&from=' + from + '&to=' + to;
    }

    fetch(url)
        .then(res => res.json())
        .then(data => {
            const isEmpty = !data.values || data.values.every(v => v === 0);

            // Show/hide empty state
            document.getElementById('chart-wrapper').style.display = isEmpty ? 'none' : 'block';
            document.getElementById('chart-empty').style.display   = isEmpty ? 'block' : 'none';

            // Update summary
            const totalRentals = data.values.reduce((a, b) => a + b, 0);
            const totalRevenue = data.revenues ? data.revenues.reduce((a, b) => a + b, 0) : 0;
            document.getElementById('summary-rentals').textContent = isEmpty ? '-' : totalRentals + ' rentals';
            document.getElementById('summary-revenue').textContent = isEmpty ? '-' : 'Rp ' + totalRevenue.toLocaleString('id-ID');

            if (chartInstance) chartInstance.destroy();
            if (isEmpty) return;

            const ctx = document.getElementById('chart');
            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Total Rentals',
                            data: data.values,
                            backgroundColor: 'rgba(30,64,175,0.6)',
                            borderColor: 'rgba(30,64,175,1)',
                            borderWidth: 1,
                            borderRadius: 6,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Revenue (Rp)',
                            data: data.revenues ?? [],
                            type: 'line',
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16,185,129,0.1)',
                            borderWidth: 2,
                            pointBackgroundColor: '#10b981',
                            pointRadius: 4,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y2',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                            title: { display: true, text: 'Rentals' }
                        },
                        y2: {
                            beginAtZero: true,
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: { callback: val => formatRupiah(val) },
                            title: { display: true, text: 'Revenue' }
                        }
                    },
                    plugins: {
                        legend: { display: true, position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    if (ctx.dataset.label === 'Revenue (Rp)') {
                                        return 'Revenue: Rp ' + ctx.raw.toLocaleString('id-ID');
                                    }
                                    return 'Rentals: ' + ctx.raw;
                                }
                            }
                        }
                    }
                }
            });
        });
}

function resetChart() {
    document.getElementById('date-from').value = '';
    document.getElementById('date-to').value = '';
    loadChart('daily');
}

document.addEventListener('DOMContentLoaded', () => loadChart('daily'));
</script>
@endpush