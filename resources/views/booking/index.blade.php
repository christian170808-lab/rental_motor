@extends('layouts.app')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

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
                    <option value="skuter" {{ request('type') == 'skuter' ? 'selected' : '' }}>Skuter</option>
                    <option value="sport" {{ request('type') == 'sport' ? 'selected' : '' }}>Sport</option>
                    <option value="trail" {{ request('type') == 'trail' ? 'selected' : '' }}>Adventure/Trail</option>
                </select>
            </div>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($vehicles->isEmpty())
        <div class="alert alert-warning text-center">No vehicle data found.</div>
    @else
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Image</th>
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
                        <td class="text-center">
                            <img src="{{ asset('image/' . ($v->image ?? 'default.png')) }}" width="90">
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
        // Inisialisasi Select2 pada dropdown
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Tipe...',
            allowClear: true
        });
    });
</script>
@endsection