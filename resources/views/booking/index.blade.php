@extends('layouts.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
.container { max-width: 1200px; }
h2 { font-weight: 600; color: #1f2937; letter-spacing: 0.5px; }
#filterForm { background: #ffffff; padding: 15px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.vehicle-table { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 14px rgba(0,0,0,0.06); border: none; }
.vehicle-table thead { background: linear-gradient(90deg,#2563eb,#1d4ed8); color: white; }
.vehicle-table thead th { border: none; font-weight: 600; font-size: 14px; letter-spacing: 0.5px; padding: 14px; text-align: center; }
.vehicle-table tbody tr { transition: all 0.25s ease; }
.vehicle-table tbody tr:hover { background-color: #f1f5f9; transform: scale(1.002); }
.vehicle-table td { vertical-align: middle; font-size: 14px; color: #374151; padding: 14px; text-align: center; }
.vehicle-table td.image-cell { width: 110px; height: 90px; }
.vehicle-table td.image-cell img { width: 100%; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #e5e7eb; transition: 0.3s; }
.vehicle-table td.image-cell img:hover { transform: scale(1.05); }
.vehicle-table td:nth-child(5) { font-weight: 600; color: #16a34a; }
.btn-sm { border-radius: 6px; font-size: 13px; padding: 6px 12px; font-weight: 500; }
.btn-success { background: #22c55e; border: none; }
.btn-success:hover { background: #16a34a; }
.btn-info { background: #0ea5e9; border: none; color: white; }
.btn-info:hover { background: #0284c7; }
.btn-secondary { background: #9ca3af; border: none; }
.select2-container--bootstrap-5 .select2-selection { height: 38px; border-radius: 6px; }
</style>
@endpush

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Vehicle List</h2>
    </div>

    <form action="{{ route('booking.index') }}" method="GET" class="mb-4" id="filterForm">
        <div class="row g-2">
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" 
                        placeholder="Cari nama atau plat..." 
                        value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <select name="type" class="form-select select2" onchange="this.form.submit()">
                    <option value="">Semua Tipe</option>
                    <option value="scooter" {{ request('type') == 'scooter' ? 'selected' : '' }}>Skuter</option>
                    <option value="sport" {{ request('type') == 'sport' ? 'selected' : '' }}>Sport</option>
                    <option value="trail" {{ request('type') == 'trail' ? 'selected' : '' }}>Adventure/Trail</option>
                </select>
            </div>
        </div>
    </form>

    @if($vehicles->isEmpty())
        <div class="alert alert-warning text-center">No vehicle data found.</div>
    @else
        <table class="table table-bordered table-striped vehicle-table">
            <thead>
                <tr>
                    <th style="width: 110px;">Image</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Plate</th>
                    <th>Price/Day</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicles as $v)
                    <tr>
                        <td class="image-cell">
                            <img src="{{ asset('image/' . ($v->image ?? 'default.png')) }}" 
                                 alt="{{ $v->name }}" loading="lazy">
                        </td>
                        <td>{{ $v->name }}</td>
                        <td>{{ $v->type }}</td>
                        <td>{{ $v->plate_number }}</td>
                        <td>Rp {{ number_format($v->price_per_day, 0, ',', '.') }}</td>
                        <td>
                            @if($v->status == 'available')
                                <a href="{{ route('bookings.create', $v->id) }}" class="btn btn-success btn-sm">Rent</a>
                            @else
                                <button class="btn btn-secondary btn-sm" disabled>Unavailable</button>
                            @endif
                            <a href="{{ route('returns.create', ['vehicle_id' => $v->id]) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-search"></i> Check
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap-5', placeholder: 'Pilih Tipe...', allowClear: true });
    });
</script>
@endsection