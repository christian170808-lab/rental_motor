@extends('layouts.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
/* ─── HIDE PAGINATION TEXT ─── */
nav.d-flex p.small { display: none !important; }

/* ─── SELECT2 FILTER ─── */
.select2-filter .select2-container .select2-selection--single {
    height: 40px !important; border: 1px solid #e5e7eb !important;
    border-radius: 8px !important; display: flex; align-items: center;
    padding: 0 12px; font-size: 14px; color: #374151; min-width: 160px;
}
.select2-filter .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px !important; right: 8px; }
.select2-filter .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px !important; color: #374151 !important; padding: 0; }
.select2-filter .select2-container--default .select2-selection--single .select2-selection__placeholder { color: #9ca3af; }
.select2-filter .select2-container--default.select2-container--open .select2-selection--single { border-color: #3b82f6 !important; }
.select2-dropdown { border-radius: 8px !important; border: 1px solid #e5e7eb !important; box-shadow: 0 4px 20px rgba(0,0,0,0.1) !important; font-size: 14px; }
.select2-container--default .select2-search--dropdown .select2-search__field { border-radius: 6px !important; border: 1px solid #e5e7eb !important; padding: 6px 10px !important; font-size: 13px; }
.select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #1e40af !important; }

/* ─── PAGE HEADER ─── */
.page-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 60%, #1d4ed8 100%);
    border-radius: 16px; padding: 22px 28px; margin-bottom: 20px;
    box-shadow: 0 8px 32px rgba(37,99,235,0.25);
}
.page-header h2 { color: #fff; font-weight: 700; margin: 0; font-size: 1.5rem; }
.page-header p  { color: rgba(255,255,255,0.7); margin: 4px 0 0; font-size: 0.9rem; }

/* ─── SECTION TITLE ─── */
.section-title {
    font-weight: 700; padding-bottom: 10px; margin-bottom: 20px;
    position: relative; color: #1e3a8a;
}
.section-title::after {
    content: ''; position: absolute; bottom: 0; left: 0;
    width: 50px; height: 4px;
    background: linear-gradient(90deg, #1e3a8a, #3b82f6); border-radius: 2px;
}

/* ─── TABLE CARD ─── */
.table-card {
    background: #fff; border-radius: 14px; overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07); border: 1px solid #e5e7eb; margin-bottom: 2rem;
}
.table thead th {
    background: linear-gradient(90deg, #1e3a8a, #1d4ed8); color: #fff;
    font-size: 13px; font-weight: 600; text-transform: uppercase;
    letter-spacing: 0.5px; padding: 13px 16px; border: none; text-align: center;
}
.table td { padding: 13px 16px; vertical-align: middle; font-size: 14px; border-color: #f1f5f9; text-align: center; }
.table tbody tr:hover { background: #eff6ff; }

/* ─── SEARCH BAR ─── */
.search-bar {
    background: #fff; border-radius: 12px; padding: 14px 18px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07); border: 1px solid #e5e7eb;
    margin-bottom: 20px; display: flex; gap: 10px; align-items: center;
}
.search-bar input {
    flex: 1; border: 1px solid #e5e7eb; border-radius: 8px;
    padding: 9px 14px; font-size: 14px; outline: none; transition: border-color 0.2s;
}
.search-bar input:focus { border-color: #3b82f6; }
.btn-search {
    background: linear-gradient(135deg, #1e3a8a, #2563eb); color: #fff;
    border: none; border-radius: 8px; padding: 9px 20px;
    font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap;
}
.btn-search:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(30,64,175,0.3); }

/* ─── ADD BUTTON ─── */
.btn-add {
    background: linear-gradient(135deg, #1e3a8a, #1e40af); color: #fff; border: none;
    font-weight: 600; padding: 0.55rem 1.2rem; border-radius: 8px;
    box-shadow: 0 2px 8px rgba(30,64,175,0.3); transition: all 0.2s;
}
.btn-add:hover { transform: translateY(-1px); color: #fff; box-shadow: 0 4px 14px rgba(30,64,175,0.4); }

/* ─── MODAL HEADER ─── */
.modal-header-blue {
    background: linear-gradient(135deg, #1e3a8a, #1d4ed8); padding: 18px 24px;
    display: flex; justify-content: space-between; align-items: center;
}
.modal-header-red {
    background: linear-gradient(135deg, #991b1b, #dc2626); padding: 18px 24px;
    display: flex; justify-content: space-between; align-items: center;
}
.modal-close-btn {
    background: rgba(255,255,255,0.2); color: #fff;
    border: 1px solid rgba(255,255,255,0.4); border-radius: 8px;
    padding: 4px 10px; font-size: 14px; cursor: pointer;
}

/* ─── PAGINATION ─── */
.page-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 36px; height: 36px; border-radius: 8px;
    border: 1px solid #e5e7eb; background: #fff;
    color: #374151; font-size: 14px; font-weight: 500;
    text-decoration: none; cursor: pointer; transition: all 0.2s;
}
.page-btn:hover:not([disabled]) { border-color: #3b82f6; color: #3b82f6; }
.page-btn.active { background: #3b82f6; border-color: #3b82f6; color: #fff; }
.page-btn[disabled] { opacity: 0.4; cursor: not-allowed; }
</style>
@endpush

@section('content')
<div class="container mt-4" style="max-width: 1300px;">

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <h2><i class="fas fa-motorcycle me-2"></i> Vehicle List</h2>
        <p>Manage and search motorcycles available for rent</p>
    </div>

    {{-- TOOLBAR --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="section-title mb-0">Vehicle Data</h5>
        <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
            <i class="fas fa-plus me-1"></i> Add Vehicle
        </button>
    </div>

    {{-- SEARCH & FILTER --}}
    <form method="GET" action="{{ route('vehicles.index') }}" id="filterForm">
        <div class="search-bar">
            <input type="text" name="search" placeholder="Search by name or plate…" value="{{ request('search') }}">
            <button type="submit" class="btn-search"><i class="fas fa-search me-1"></i> Search</button>
            <div class="select2-filter">
                <select name="type" id="typeFilter" style="width:180px;">
                    <option value="">All Types</option>
                    <option value="scooter" {{ request('type') == 'scooter' ? 'selected' : '' }}>🛵 Scooter</option>
                    <option value="sport"   {{ request('type') == 'sport'   ? 'selected' : '' }}>🏍️ Sport</option>
                    <option value="trail"   {{ request('type') == 'trail'   ? 'selected' : '' }}>🚵 Trail</option>
                </select>
            </div>
        </div>
    </form>

    {{-- VEHICLES TABLE --}}
    <div class="table-card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width:50px;">No</th>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Plate</th>
                        <th>Price / Day</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $index => $v)
                    <tr>
                        <td class="fw-semibold text-muted">{{ $vehicles->firstItem() + $index }}</td>
                        <td>
                            <img src="{{ asset('image/' . ($v->image ?? 'default.png')) }}"
                                 style="width:70px;height:52px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">
                        </td>
                        <td class="fw-semibold">{{ $v->name }}</td>
                        <td>
                            @php $type = strtolower($v->type); @endphp
                            @if($type == 'scooter') <span class="badge bg-info text-dark">Scooter</span>
                            @elseif($type == 'sport') <span class="badge bg-danger">Sport</span>
                            @elseif($type == 'trail') <span class="badge bg-success">Trail</span>
                            @else <span class="badge bg-secondary">{{ ucfirst($v->type) }}</span>
                            @endif
                        </td>
                        <td><code style="color:#1e40af;">{{ $v->plate_number }}</code></td>
                        <td class="fw-semibold text-success">Rp {{ number_format($v->price_per_day, 0, ',', '.') }}</td>
                        <td>
                            @if($v->status == 'available') <span class="badge bg-success">Available</span>
                            @elseif($v->status == 'rented') <span class="badge bg-warning text-dark">Rented</span>
                            @else <span class="badge bg-secondary">{{ ucfirst($v->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#editVehicleModal"
                                    data-id="{{ $v->id }}"
                                    data-name="{{ $v->name }}"
                                    data-type="{{ $v->type }}"
                                    data-plate="{{ $v->plate_number }}"
                                    data-price="{{ $v->price_per_day }}"
                                    data-image="{{ $v->image ? asset('image/' . $v->image) : '' }}">
                                    <i class="fas fa-pen me-1"></i> Edit
                                </button>
                                <button type="button" class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#deleteVehicleModal"
                                    data-id="{{ $v->id }}"
                                    data-name="{{ $v->name }}">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted fst-italic">No vehicles found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($vehicles->hasPages() || $vehicles->total() > 0)
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size: 14px;">
                Showing {{ $vehicles->firstItem() }} to {{ $vehicles->lastItem() }} of {{ $vehicles->total() }} results
            </span>
            <div class="d-flex gap-1">
                @if($vehicles->onFirstPage())
                    <button class="page-btn" disabled>&lsaquo;</button>
                @else
                    <a href="{{ $vehicles->appends(['page_returns' => request('page_returns')])->previousPageUrl() }}" class="page-btn">&lsaquo;</a>
                @endif

                @for($page = 1; $page <= $vehicles->lastPage(); $page++)
                    @if($page == $vehicles->currentPage())
                        <button class="page-btn active">{{ $page }}</button>
                    @else
                        <a href="{{ $vehicles->appends(['page_returns' => request('page_returns')])->url($page) }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endfor

                @if($vehicles->hasMorePages())
                    <a href="{{ $vehicles->appends(['page_returns' => request('page_returns')])->nextPageUrl() }}" class="page-btn">&rsaquo;</a>
                @else
                    <button class="page-btn" disabled>&rsaquo;</button>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- RETURN HISTORY --}}
    <h5 class="section-title mt-4 mb-4">Return History</h5>
    <div class="table-card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Vehicle</th>
                        <th>Return Date</th>
                        <th>Late (days)</th>
                        <th>Penalty</th>
                        <th>Condition</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $return)
                    <tr>
                        <td>#{{ $return->booking_id }}</td>
                        <td>{{ optional($return->booking->vehicle)->name ?? '—' }}</td>
                        <td>{{ \Carbon\Carbon::parse($return->return_date)->format('d M Y') }}</td>
                        <td>
                            @if($return->late_days > 0)
                                <span class="text-danger fw-bold">{{ $return->late_days }}</span>
                            @else
                                <span class="text-success">0</span>
                            @endif
                        </td>
                        <td>
                            @if($return->penalty > 0)
                                <strong class="text-danger">Rp {{ number_format($return->penalty, 0, ',', '.') }}</strong>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($return->vehicle_condition == 'Good')
                                <span class="badge bg-success">Good</span>
                            @elseif($return->vehicle_condition == 'Minor Damage')
                                <span class="badge bg-warning text-dark">Minor Damage</span>
                            @else
                                <span class="badge bg-danger">Major Damage</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted fst-italic">No return records yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($returns->total() > 0 && $returns->lastPage() > 1)
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size: 14px;">
                Showing {{ $returns->firstItem() }} to {{ $returns->lastItem() }} of {{ $returns->total() }} results
            </span>
            <div class="d-flex gap-1">
                @if($returns->onFirstPage())
                    <button class="page-btn" disabled>&lsaquo;</button>
                @else
                    <a href="{{ $returns->appends(['page' => request('page')])->previousPageUrl() }}" class="page-btn">&lsaquo;</a>
                @endif

                @for($page = 1; $page <= $returns->lastPage(); $page++)
                    @if($page == $returns->currentPage())
                        <button class="page-btn active">{{ $page }}</button>
                    @else
                        <a href="{{ $returns->appends(['page' => request('page')])->url($page) }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endfor

                @if($returns->hasMorePages())
                    <a href="{{ $returns->appends(['page' => request('page')])->nextPageUrl() }}" class="page-btn">&rsaquo;</a>
                @else
                    <button class="page-btn" disabled>&rsaquo;</button>
                @endif
                            </div>
                        </div>
                @endif
    </div>

    {{-- Hidden delete form --}}
    <form id="deleteVehicleForm" method="POST" style="display:none;">
        @csrf @method('DELETE')
    </form>

</div>

{{-- ADD VEHICLE MODAL --}}
<div class="modal fade" id="addVehicleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <div class="modal-header-blue">
                <h5 class="text-white fw-bold mb-0"><i class="fas fa-motorcycle me-2"></i> Add New Vehicle</h5>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body p-4">
                @if($errors->any())
                <div class="alert alert-danger rounded-3">
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
                @endif
                <form action="{{ route('vehicles.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Vehicle Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Honda PCX 160" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="">— Select Type —</option>
                            <option value="scooter" {{ old('type') == 'scooter' ? 'selected' : '' }}>Scooter</option>
                            <option value="sport"   {{ old('type') == 'sport'   ? 'selected' : '' }}>Sport</option>
                            <option value="trail"   {{ old('type') == 'trail'   ? 'selected' : '' }}>Trail</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Plate Number</label>
                        <input type="text" name="plate_number" id="addPlateInput" class="form-control text-uppercase"
                               placeholder="e.g. DK 1234 ABC" value="{{ old('plate_number') }}" required>
                        <small class="text-muted">Format: DK 1234 ABC</small>
                        <div id="add-plate-error" style="color:#dc2626;font-size:13px;display:none;">⚠️ Invalid plate format.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Price Per Day (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="price_per_day" class="form-control" placeholder="e.g. 85000" value="{{ old('price_per_day') }}" min="0" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Vehicle Photo</label>
                        <input type="file" name="image" class="form-control" accept="image/*" id="addImageInput" required>
                        <small class="text-muted">JPG, JPEG, PNG, WEBP — max 2MB</small>
                        <div class="d-flex align-items-center gap-1 mt-1"
                             style="font-size:12.5px;color:#b45309;background:#fffbeb;border:1px solid #fde68a;border-radius:6px;padding:5px 10px;">
                            <i class="fas fa-exclamation-triangle" style="font-size:11px;"></i>
                            <span>Make sure the uploaded photo is a <strong>vehicle photo</strong>.</span>
                        </div>
                    </div>
                    <button type="submit" class="btn w-100 fw-bold text-white"
                        style="background:linear-gradient(135deg,#1e3a8a,#2563eb);border:none;border-radius:10px;padding:12px;">
                        <i class="fas fa-save me-2"></i> Save Vehicle
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- EDIT VEHICLE MODAL --}}
<div class="modal fade" id="editVehicleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <div class="modal-header-blue">
                <h5 class="text-white fw-bold mb-0"><i class="fas fa-edit me-2"></i> Edit Vehicle</h5>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body p-4">
                <form id="editVehicleForm" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Vehicle Name</label>
                        <input type="text" name="name" id="ev-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Type</label>
                        <select name="type" id="ev-type" class="form-select" required>
                            <option value="scooter">Scooter</option>
                            <option value="sport">Sport</option>
                            <option value="trail">Trail</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Plate Number</label>
                        <input type="text" name="plate_number" id="ev-plate" class="form-control text-uppercase"
                               placeholder="e.g. DK 1234 ABC" required>
                        <small class="text-muted">Format: DK 1234 ABC</small>
                        <div id="ev-plate-error" style="color:#dc2626;font-size:13px;display:none;">⚠️ Invalid plate format.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Price Per Day (Rp)</label>
                        <input type="number" name="price_per_day" id="ev-price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Photo</label>
                        <div id="ev-image-preview" class="mb-2"></div>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Leave blank to keep the current photo.</small>
                    </div>
                    <button type="submit" class="btn w-100 fw-bold text-white"
                        style="background:linear-gradient(135deg,#1e3a8a,#2563eb);border:none;border-radius:10px;padding:12px;">
                        <i class="fas fa-save me-2"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- DELETE VEHICLE MODAL --}}
<div class="modal fade" id="deleteVehicleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <div class="modal-header-red">
                <h5 class="text-white fw-bold mb-0"><i class="fas fa-trash me-2"></i> Delete Vehicle</h5>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div style="width:64px;height:64px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="fas fa-motorcycle" style="font-size:28px;color:#ef4444;"></i>
                </div>
                <p class="fw-semibold mb-1" style="font-size:16px;">Are you sure?</p>
                <p class="text-muted mb-4" style="font-size:14px;">
                    You are about to delete <strong id="delete-vehicle-name"></strong>. This action cannot be undone.
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" id="btn-confirm-delete" class="btn fw-bold text-white px-4"
                        style="background:linear-gradient(135deg,#ef4444,#dc2626);border:none;border-radius:10px;padding:10px 24px;">
                        <i class="fas fa-trash me-2"></i> Yes, Delete
                    </button>
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius:10px;">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
/* ─── Select2 type filter ─── */
$(document).ready(function () {
    $('#typeFilter').select2({
        placeholder: 'All Types', allowClear: true,
        minimumResultsForSearch: 0, dropdownParent: $('body'),
    }).on('change', function () { document.getElementById('filterForm').submit(); });
});

/* ─── Edit vehicle modal ─── */
document.getElementById('editVehicleModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('editVehicleForm').action = '/vehicles/' + btn.dataset.id;
    document.getElementById('ev-name').value          = btn.dataset.name;
    document.getElementById('ev-type').value          = btn.dataset.type;
    document.getElementById('ev-plate').value         = btn.dataset.plate;
    document.getElementById('ev-price').value         = btn.dataset.price;
    document.getElementById('ev-plate-error').style.display = 'none';
    document.getElementById('ev-plate').style.borderColor   = '';
    const preview = document.getElementById('ev-image-preview');
    preview.innerHTML = btn.dataset.image
        ? `<img src="${btn.dataset.image}" style="width:100px;border-radius:8px;border:1px solid #e5e7eb;">`
        : '';
});

/* ─── Delete vehicle modal ─── */
document.getElementById('deleteVehicleModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('delete-vehicle-name').textContent = btn.dataset.name;
    document.getElementById('deleteVehicleForm').action        = '/vehicles/' + btn.dataset.id;
});

document.getElementById('btn-confirm-delete').addEventListener('click', function () {
    document.getElementById('deleteVehicleForm').submit();
});

/* ─── Plate number validation ─── */
function autoFormatPlate(inputId, errorId) {
    document.getElementById(inputId).addEventListener('input', function () {
        let val = this.value.toUpperCase().replace(/\s+/g, '');

        // Auto insert spaces: letters, then digits, then letters
        let formatted = '';
        let i = 0;

        // Part 1: leading letters (max 3)
        let part1 = '';
        while (i < val.length && /[A-Z]/.test(val[i]) && part1.length < 3) {
            part1 += val[i++];
        }
        formatted = part1;

        // Part 2: digits (max 4)
        let part2 = '';
        while (i < val.length && /\d/.test(val[i]) && part2.length < 4) {
            part2 += val[i++];
        }
        if (part2) formatted += ' ' + part2;

        // Part 3: trailing letters (max 3)
        let part3 = '';
        while (i < val.length && /[A-Z]/.test(val[i]) && part3.length < 3) {
            part3 += val[i++];
        }
        if (part3) formatted += ' ' + part3;

        this.value = formatted;

        const valid = /^[A-Z]{1,3} \d{1,4} [A-Z]{1,3}$/.test(formatted);
        const errEl = document.getElementById(errorId);
        errEl.style.display    = formatted.length > 0 && !valid ? 'block' : 'none';
        this.style.borderColor = formatted.length > 0 && !valid ? '#dc2626' : '';
    });
}
autoFormatPlate('ev-plate', 'ev-plate-error');
autoFormatPlate('addPlateInput', 'add-plate-error');

/* ─── Image preview (edit) ─── */
document.querySelector('#editVehicleModal input[name="image"]').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) { alert('Maximum file size is 2MB!'); this.value = ''; return; }
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('ev-image-preview').innerHTML =
            `<img src="${e.target.result}" style="width:100px;border-radius:8px;border:1px solid #e5e7eb;">`;
    };
    reader.readAsDataURL(file);
});

/* ─── Image preview (add) ─── */
document.getElementById('addImageInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) { alert('Maximum file size is 2MB!'); this.value = ''; return; }
});

@if($errors->any() && old('name'))
    var addModal = new bootstrap.Modal(document.getElementById('addVehicleModal'));
    addModal.show();
@endif
</script>
@endpush