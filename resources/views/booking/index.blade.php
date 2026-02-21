@extends('layouts.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
.page-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 60%, #1d4ed8 100%);
    border-radius: 16px;
    padding: 24px 28px;
    margin-bottom: 24px;
    box-shadow: 0 8px 32px rgba(37,99,235,0.3);
}
.page-header h2 { color: #ffffff; font-weight: 700; margin: 0; font-size: 1.6rem; letter-spacing: 0.3px; }
.page-header p { color: rgba(255,255,255,0.7); margin: 4px 0 0; font-size: 0.9rem; }
#filterForm { background: #ffffff; padding: 16px; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #e5e7eb; margin-bottom: 20px; }
.vehicle-table { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.07); border: 1px solid #e5e7eb; }
.vehicle-table thead { background: linear-gradient(90deg, #1e3a8a, #1d4ed8); color: white; }
.vehicle-table thead th { border: none; font-weight: 600; font-size: 13px; letter-spacing: 0.6px; padding: 14px 16px; text-align: center; text-transform: uppercase; }
.vehicle-table tbody tr { transition: all 0.2s ease; border-bottom: 1px solid #f1f5f9; }
.vehicle-table tbody tr:hover { background-color: #eff6ff; transform: scale(1.001); }
.vehicle-table td { vertical-align: middle; font-size: 14px; color: #374151; padding: 14px 16px; text-align: center; }
.vehicle-table td.image-cell { width: 110px; }
.vehicle-table td.image-cell img { width: 90px; height: 70px; object-fit: cover; border-radius: 10px; border: 2px solid #e5e7eb; transition: 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.vehicle-table td.image-cell img:hover { transform: scale(1.08); border-color: #3b82f6; }
.price-cell { font-weight: 700; color: #16a34a; font-size: 14px; }
.btn-rent { background: linear-gradient(135deg, #22c55e, #16a34a); border: none; color: white; border-radius: 8px; font-size: 13px; padding: 6px 14px; font-weight: 600; transition: all 0.2s; box-shadow: 0 2px 8px rgba(34,197,94,0.3); }
.btn-rent:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(34,197,94,0.4); color: white; }
.btn-return { background: linear-gradient(135deg, #0ea5e9, #0284c7); border: none; color: white; border-radius: 8px; font-size: 13px; padding: 6px 14px; font-weight: 600; transition: all 0.2s; box-shadow: 0 2px 8px rgba(14,165,233,0.3); }
.btn-return:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(14,165,233,0.4); color: white; }
.btn-unavailable { background: #e5e7eb; border: none; color: #9ca3af; border-radius: 8px; font-size: 13px; padding: 6px 14px; font-weight: 600; cursor: not-allowed; }
.select2-container--bootstrap-5 .select2-selection { height: 38px; border-radius: 8px; }
</style>
@endpush

@section('content')
<div class="container mt-4" style="max-width: 1200px;">

    <div class="page-header">
        <h2><i class="fas fa-motorcycle me-2"></i> Vehicle List</h2>
        <p>Manage and search for motorcycles available for rent</p>
    </div>

    <form action="{{ route('booking.index') }}" method="GET" class="mb-4" id="filterForm">
        <div class="row g-2">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0"
                        placeholder="Search by name or plate..."
                        value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary px-4">Search</button>
                </div>
            </div>
            <div class="col-md-4">
                <select name="type" class="form-select select2" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="scooter"   {{ request('type') == 'scooter'   ? 'selected' : '' }}>Scooter</option>
                    <option value="sport"     {{ request('type') == 'sport'     ? 'selected' : '' }}>Sport</option>
                    <option value="adventure" {{ request('type') == 'trail' ? 'selected' : '' }}>Adventure/Trail</option>
                </select>
            </div>
        </div>
    </form>

    @if($vehicles->isEmpty())
        <div class="alert alert-warning text-center rounded-3">
            <i class="fas fa-exclamation-circle me-2"></i> No vehicle data found.
        </div>
    @else
        <div class="vehicle-table">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width:110px;">Image</th>
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
                            <td class="fw-semibold">{{ $v->name }}</td>
                            <td>
                                <span class="badge bg-light text-dark border" style="font-size:12px;">
                                    {{ ucfirst($v->type) }}
                                </span>
                            </td>
                            <td><code style="color:#1e40af; font-size:13px;">{{ $v->plate_number }}</code></td>
                            <td class="price-cell">Rp {{ number_format($v->price_per_day, 0, ',', '.') }}</td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    @if($v->status == 'available')
                                        <a href="{{ route('bookings.create', $v->id) }}" class="btn btn-rent btn-sm">
                                            <i class="fas fa-key me-1"></i> Rent
                                        </a>
                                    @else
                                        <button class="btn btn-unavailable btn-sm" disabled>Unavailable</button>
                                    @endif
                                    <a href="{{ route('returns.create', ['vehicle_id' => $v->id]) }}" class="btn btn-return btn-sm">
                                        <i class="fas fa-clipboard-check me-1"></i> Check
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap-5', placeholder: 'Select Type...', allowClear: true });
    });
</script>
@endsection