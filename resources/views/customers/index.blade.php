@extends('layouts.app')

@push('styles')
<style>
    @keyframes popIn {
    0%   { opacity: 0; transform: scale(.85) translateY(-20px); }
    70%  { transform: scale(1.03) translateY(2px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-30px); }
    to   { opacity: 1; transform: translateY(0); }
}
.modal.show .modal-dialog      { animation: popIn .32s cubic-bezier(.34, 1.56, .64, 1) both; }
.modal.show .modal-header-blue,
.modal.show .modal-header-red  { animation: slideDown .28s ease both .05s; }
.modal-content                 { border: none; border-radius: 16px !important; overflow: hidden; }
.page-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 60%, #1d4ed8 100%);
    border-radius: 16px; padding: 22px 28px; margin-bottom: 20px;
    box-shadow: 0 8px 32px rgba(37,99,235,0.25);
}
.page-header h2 { color: #fff; font-weight: 700; margin: 0; font-size: 1.5rem; }
.page-header p  { color: rgba(255,255,255,0.7); margin: 4px 0 0; font-size: 0.9rem; }

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

.search-box input { border-radius: 8px 0 0 8px; padding: 10px 14px; }
.search-box button {
    border-radius: 0 8px 8px 0;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff; font-weight: 600; border: none;
}

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

.ktp-thumb {
    width: 72px; height: 46px; object-fit: cover;
    border-radius: 6px; border: 1px solid #e5e7eb;
    cursor: pointer; transition: transform 0.15s, box-shadow 0.15s;
}
.ktp-thumb:hover { transform: scale(1.06); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

#ktpLightbox {
    display: none; position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,0.78); align-items: center; justify-content: center;
    flex-direction: column; gap: 16px;
}
#ktpLightbox.show { display: flex; }
#ktpLightbox img { max-width: 88vw; max-height: 82vh; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); }
#ktpLightbox button {
    background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3);
    color: #fff; border-radius: 8px; padding: 8px 20px; font-size: 14px; cursor: pointer;
}
#ktpLightbox button:hover { background: rgba(255,255,255,0.25); }

.ktp-preview-box {
    width: 100%; height: 130px; border: 2px dashed #cbd5e1; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    overflow: hidden; background: #f8fafc; cursor: pointer; transition: border-color 0.2s;
}
.ktp-preview-box:hover { border-color: #3b82f6; }
.ktp-preview-box img { width: 100%; height: 100%; object-fit: cover; }
.ktp-preview-box .ktp-placeholder { text-align: center; color: #94a3b8; font-size: 13px; pointer-events: none; }
.ktp-preview-box .ktp-placeholder i { font-size: 28px; display: block; margin-bottom: 6px; }

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
<div class="container mt-4" style="max-width:1200px;">

    {{-- Flash messages --}}
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

    <div class="page-header">
        <h2><i class="fas fa-users me-2"></i> Customer List</h2>
        <p>Manage all registered customers</p>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('customers.index') }}" class="search-box w-50">
            <div class="input-group">
                <input type="text" name="search" class="form-control"
                       placeholder="Search by name or email…" value="{{ request('search') }}">
                <button type="submit" class="btn px-4">Search</button>
            </div>
        </form>
        <button type="button" class="btn text-white"
            data-bs-toggle="modal" data-bs-target="#addModal"
            style="background:linear-gradient(135deg,#1e3a8a,#1e40af);font-weight:600;border-radius:8px;padding:9px 20px;">
            <i class="fas fa-plus me-1"></i> Add Customer
        </button>
    </div>

    <div class="table-card">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Card</th>
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
                    <td>{{ $customers->firstItem() + $loop->index }}</td>
                    <td>
                        @if($customer->ktp_photo)
                            <img src="{{ asset('ktp/' . $customer->ktp_photo) }}"
                                 class="ktp-thumb"
                                 onclick="openKtp('{{ asset('ktp/' . $customer->ktp_photo) }}')"
                                 title="Click to view ID Card">
                        @else
                            <span class="text-muted" style="font-size:12px;">—</span>
                        @endif
                    </td>
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
                                data-address="{{ $customer->address }}"
                                data-ktp="{{ $customer->ktp_photo ? asset('ktp/' . $customer->ktp_photo) : '' }}">
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
                    <td colspan="7" class="text-center py-5 text-muted fst-italic">No customers found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($customers->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size:14px;">
                Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} results
            </span>
            <div class="d-flex gap-1">
                @if($customers->onFirstPage())
                    <button class="page-btn" disabled>&lsaquo;</button>
                @else
                    <a href="{{ $customers->previousPageUrl() }}" class="page-btn">&lsaquo;</a>
                @endif
                @for($page = 1; $page <= $customers->lastPage(); $page++)
                    @if($page == $customers->currentPage())
                        <button class="page-btn active">{{ $page }}</button>
                    @else
                        <a href="{{ $customers->url($page) }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endfor
                @if($customers->hasMorePages())
                    <a href="{{ $customers->nextPageUrl() }}" class="page-btn">&rsaquo;</a>
                @else
                    <button class="page-btn" disabled>&rsaquo;</button>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

{{-- KTP LIGHTBOX --}}
<div id="ktpLightbox" onclick="closeKtp()">
    <img id="ktpLightboxImg" src="" alt="ID Card">
    <button onclick="event.stopPropagation(); closeKtp()"><i class="fas fa-times me-1"></i> Close</button>
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
                <form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data" id="addCustomerForm">
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
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Enter address…" required>{{ old('address') }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">ID Card Photo <span class="text-danger">*</span></label>
                        <div class="ktp-preview-box" id="addKtpBox" onclick="document.getElementById('addKtpInput').click()">
                            <div class="ktp-placeholder" id="addKtpPlaceholder">
                                <i class="fas fa-id-card"></i>
                                Click to upload ID Card photo
                                <div style="font-size:11px;margin-top:4px;color:#b0bec5;">JPG, PNG, WEBP — max 2MB</div>
                            </div>
                            <img id="addKtpPreview" src="" alt="" style="display:none;width:100%;height:100%;object-fit:cover;">
                        </div>
                        <input type="file" name="ktp_photo" id="addKtpInput"
                               accept="image/jpg,image/jpeg,image/png,image/webp"
                               style="display:none;">
                        <div id="addKtpError" style="color:#dc2626;font-size:12.5px;margin-top:5px;display:none;">
                            <i class="fas fa-exclamation-circle me-1"></i> ID Card photo is required.
                        </div>
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
                <form id="editForm" method="POST" enctype="multipart/form-data">
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
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Address</label>
                        <textarea name="address" id="edit-address" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">ID Card Photo</label>
                        <div class="ktp-preview-box" id="editKtpBox" onclick="document.getElementById('editKtpInput').click()">
                            <div class="ktp-placeholder" id="editKtpPlaceholder">
                                <i class="fas fa-id-card"></i>
                                Click to change ID Card photo
                                <div style="font-size:11px;margin-top:4px;color:#b0bec5;">Leave unchanged to keep current</div>
                            </div>
                            <img id="editKtpPreview" src="" alt="" style="display:none;width:100%;height:100%;object-fit:cover;">
                        </div>
                        <input type="file" name="ktp_photo" id="editKtpInput"
                               accept="image/jpg,image/jpeg,image/png,image/webp"
                               style="display:none;">
                        <small class="text-muted">Leave blank to keep current ID Card photo</small>
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
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
/* ─── Lightbox ─── */
function openKtp(src) {
    document.getElementById('ktpLightboxImg').src = src;
    document.getElementById('ktpLightbox').classList.add('show');
}
function closeKtp() {
    document.getElementById('ktpLightbox').classList.remove('show');
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeKtp(); });

/* ─── KTP Preview helper ─── */
function setupKtpPreview(inputId, previewId, placeholderId) {
    document.getElementById(inputId).addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) {
            alert('Maximum file size is 2MB!');
            this.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            preview.src = e.target.result;
            preview.style.display = 'block';
            document.getElementById(placeholderId).style.display = 'none';
        };
        reader.readAsDataURL(file);
    });
}

setupKtpPreview('addKtpInput', 'addKtpPreview', 'addKtpPlaceholder');
setupKtpPreview('editKtpInput', 'editKtpPreview', 'editKtpPlaceholder');

/* ─── Reset error saat file dipilih ─── */
document.getElementById('addKtpInput').addEventListener('change', function () {
    if (this.files.length > 0) {
        document.getElementById('addKtpError').style.display = 'none';
        document.getElementById('addKtpBox').style.borderColor = '';
    }
});

/* ─── Validasi: ID Card wajib di Add ─── */
document.getElementById('addCustomerForm').addEventListener('submit', function (e) {
    const input = document.getElementById('addKtpInput');
    const errEl = document.getElementById('addKtpError');
    const box   = document.getElementById('addKtpBox');
    if (!input.files || input.files.length === 0) {
        e.preventDefault();
        errEl.style.display = 'block';
        box.style.borderColor = '#ef4444';
        box.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

/* ─── Edit modal populate ─── */
document.getElementById('editModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('editForm').action    = '/customers/' + btn.dataset.id;
    document.getElementById('edit-name').value    = btn.dataset.name;
    document.getElementById('edit-email').value   = btn.dataset.email;
    document.getElementById('edit-phone').value   = btn.dataset.phone;
    document.getElementById('edit-address').value = btn.dataset.address ?? '';

    const ktpUrl      = btn.dataset.ktp;
    const preview     = document.getElementById('editKtpPreview');
    const placeholder = document.getElementById('editKtpPlaceholder');
    if (ktpUrl) {
        preview.src           = ktpUrl;
        preview.style.display = 'block';
        placeholder.style.display = 'none';
    } else {
        preview.src           = '';
        preview.style.display = 'none';
        placeholder.style.display = 'block';
    }
    document.getElementById('editKtpInput').value = '';
});

/* ─── Delete modal ─── */
document.getElementById('deleteModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('delete-customer-name').textContent = btn.dataset.name;
    document.getElementById('deleteForm').action = '/customers/' + btn.dataset.id;
});
</script>
@endpush

@endsection