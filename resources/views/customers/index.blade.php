@extends('layouts.app')

@push('styles')
<style>
/* ─── PAGE HEADER ─── */
.page-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 60%, #1d4ed8 100%);
    border-radius: 16px; padding: 22px 28px; margin-bottom: 20px;
    box-shadow: 0 8px 32px rgba(37,99,235,0.25);
}
.page-header h2 { color: #fff; font-weight: 700; margin: 0; font-size: 1.5rem; }
.page-header p  { color: rgba(255,255,255,0.7); margin: 4px 0 0; font-size: 0.9rem; }

/* ─── TABLE CARD ─── */
.table-card {
    background: #fff; border-radius: 14px; overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07); border: 1px solid #e5e7eb;
}
.table-card thead th {
    background: linear-gradient(90deg, #1e3a8a, #1d4ed8);
    color: #fff; border: none; font-weight: 600;
    font-size: 13px; text-transform: uppercase; padding: 13px 16px; text-align: center;
}
.table-card td { vertical-align: middle; font-size: 14px; padding: 13px 16px; text-align: center; }
.table-card tbody tr:hover { background: #eff6ff; }

/* ─── SEARCH BAR ─── */
.search-box input { border-radius: 8px 0 0 8px; padding: 10px 14px; }
.search-box button {
    border-radius: 0 8px 8px 0;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff; font-weight: 600; border: none;
}

/* ─── MODAL HEADER ─── */
.modal-header-blue {
    background: linear-gradient(135deg, #1e3a8a, #1d4ed8);
    padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;
}
.modal-header-red {
    background: linear-gradient(135deg, #991b1b, #dc2626);
    padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;
}
.modal-close-btn {
    background: rgba(255,255,255,0.2); color: #fff;
    border: 1px solid rgba(255,255,255,0.4);
    border-radius: 8px; padding: 4px 10px; font-size: 14px; cursor: pointer;
}
</style>
@endpush

@section('content')
<div class="container mt-4" style="max-width:1200px;">

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <h2><i class="fas fa-users me-2"></i> Customer List</h2>
        <p>Manage all registered customers</p>
    </div>

    {{-- TOOLBAR --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('customers.index') }}" class="search-box w-50">
            <div class="input-group">
                <input type="text" name="search" class="form-control"
                       placeholder="Search by name or email…" value="{{ request('search') }}">
                <button type="submit" class="btn px-4">Search</button>
            </div>
        </form>
        <button type="button" class="btn text-white fw-600"
            data-bs-toggle="modal" data-bs-target="#addModal"
            style="background:linear-gradient(135deg,#1e3a8a,#1e40af);font-weight:600;border-radius:8px;padding:9px 20px;">
            <i class="fas fa-plus me-1"></i> Add Customer
        </button>
    </div>

    {{-- CUSTOMERS TABLE --}}
    <div class="table-card">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-semibold">{{ $customer->customer_name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone_number }}</td>
                    <td>{{ $customer->address ?? '—' }}</td>
                    <td>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-sm btn-warning"
                                data-bs-toggle="modal" data-bs-target="#editModal"
                                data-id="{{ $customer->id }}"
                                data-name="{{ $customer->customer_name }}"
                                data-email="{{ $customer->email }}"
                                data-phone="{{ $customer->phone_number }}"
                                data-address="{{ $customer->address }}">
                                <i class="fas fa-pen me-1"></i> Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-danger"
                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                data-id="{{ $customer->id }}"
                                data-name="{{ $customer->customer_name }}">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted fst-italic">
                        No customers found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- ADD MODAL --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <div class="modal-header-blue">
                <h5 class="text-white fw-bold mb-0"><i class="fas fa-user-plus me-2"></i> Add Customer</h5>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body p-4">
                @if($errors->any())
                <div class="alert alert-danger rounded-3">
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
                @endif
                <form action="{{ route('customers.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Customer Name</label>
                        <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Email</label>
                        <input type="email" name="email" class="form-control"
                               pattern="[a-zA-Z0-9._%+\-]+@gmail\.com"
                               title="Email must use @gmail.com"
                               placeholder="example@gmail.com"
                               value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Phone Number</label>
                        <input type="text" name="phone_number" class="form-control"
                               value="{{ old('phone_number') }}"
                               inputmode="numeric" pattern="[0-9]+"
                               title="Phone number must be numeric"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Address</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Enter address…" required>{{ old('address') }}</textarea>
                    </div>
                    <button type="submit" class="btn w-100 fw-bold text-white"
                        style="background:linear-gradient(135deg,#1e3a8a,#2563eb);border:none;border-radius:10px;padding:12px;">
                        <i class="fas fa-save me-2"></i> Save
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <div class="modal-header-blue">
                <h5 class="text-white fw-bold mb-0"><i class="fas fa-user-edit me-2"></i> Edit Customer</h5>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body p-4">
                <form id="editForm" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Customer Name</label>
                        <input type="text" name="customer_name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Email</label>
                        <input type="email" name="email" id="edit-email" class="form-control"
                               pattern="[a-zA-Z0-9._%+\-]+@gmail\.com"
                               title="Email must use @gmail.com"
                               placeholder="example@gmail.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Phone Number</label>
                        <input type="text" name="phone_number" id="edit-phone" class="form-control"
                               inputmode="numeric" pattern="[0-9]+"
                               title="Phone number must be numeric"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Address</label>
                        <textarea name="address" id="edit-address" class="form-control" rows="3" required></textarea>
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

{{-- DELETE MODAL --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <div class="modal-header-red">
                <h5 class="text-white fw-bold mb-0"><i class="fas fa-trash me-2"></i> Delete Customer</h5>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div style="width:64px;height:64px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="fas fa-user-times" style="font-size:28px;color:#ef4444;"></i>
                </div>
                <p class="fw-semibold mb-1" style="font-size:16px;">Are you sure?</p>
                <p class="text-muted mb-4" style="font-size:14px;">
                    You are about to delete <strong id="delete-customer-name"></strong>.
                    This action cannot be undone.
                </p>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="submit" class="btn fw-bold text-white px-4"
                            style="background:linear-gradient(135deg,#ef4444,#dc2626);border:none;border-radius:10px;padding:10px 24px;">
                            <i class="fas fa-trash me-2"></i> Yes, Delete
                        </button>
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius:10px;">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('editModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('editForm').action    = '/customers/' + btn.dataset.id;
    document.getElementById('edit-name').value    = btn.dataset.name;
    document.getElementById('edit-email').value   = btn.dataset.email;
    document.getElementById('edit-phone').value   = btn.dataset.phone;
    document.getElementById('edit-address').value = btn.dataset.address ?? '';
});

document.getElementById('deleteModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('delete-customer-name').textContent = btn.dataset.name;
    document.getElementById('deleteForm').action = '/customers/' + btn.dataset.id;
});
</script>
@endpush

@endsection