@extends('layouts.app')

@push('styles')
<style>
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

.badge-you {
    background: #dbeafe; color: #1d4ed8; font-size: 11px;
    font-weight: 700; padding: 2px 8px; border-radius: 20px;
    vertical-align: middle; margin-left: 6px;
}

.pw-wrapper { position: relative; }
.pw-wrapper input { padding-right: 40px; }
.pw-toggle {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    background: none; border: none; color: #9ca3af; cursor: pointer;
    font-size: 14px; padding: 2px;
}
.pw-toggle:hover { color: #1e40af; }

/* Delete confirmation popup */
.delete-popup-overlay {
    display: none; position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,0.55); backdrop-filter: blur(3px);
    align-items: center; justify-content: center;
}
.delete-popup-overlay.show { display: flex; }
.delete-popup {
    background: #fff; border-radius: 20px; padding: 0;
    width: 100%; max-width: 420px; margin: 16px;
    box-shadow: 0 24px 64px rgba(0,0,0,0.25);
    overflow: hidden; animation: popIn 0.25s ease;
}
@keyframes popIn {
    from { transform: scale(0.88); opacity: 0; }
    to   { transform: scale(1);    opacity: 1; }
}
@keyframes popOut {
    from { transform: scale(1);    opacity: 1; }
    to   { transform: scale(0.88); opacity: 0; }
}
.delete-popup.closing { animation: popOut 0.2s ease forwards; }
.delete-popup-top {
    background: linear-gradient(135deg, #7f1d1d, #dc2626);
    padding: 24px 28px 20px;
    text-align: center;
    position: relative;
}
.delete-popup-close {
    position: absolute; top: 12px; right: 14px;
    background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.35);
    color: #fff; width: 30px; height: 30px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 13px; transition: background 0.2s;
}
.delete-popup-close:hover { background: rgba(255,255,255,0.35); }
.delete-popup-icon {
    width: 68px; height: 68px; background: rgba(255,255,255,0.15);
    border-radius: 50%; display: flex; align-items: center;
    justify-content: center; margin: 0 auto 12px;
    border: 2px solid rgba(255,255,255,0.3);
}
.delete-popup-icon i { font-size: 28px; color: #fff; }
.delete-popup-title { color: #fff; font-size: 1.2rem; font-weight: 700; margin: 0; }
.delete-popup-body { padding: 24px 28px 28px; text-align: center; }
.delete-popup-body p { margin: 0 0 6px; color: #374151; font-size: 15px; }
.delete-popup-body .admin-name {
    font-weight: 700; color: #1e3a8a; font-size: 16px;
    background: #eff6ff; padding: 4px 14px; border-radius: 8px;
    display: inline-block; margin: 6px 0 16px;
}
.delete-popup-body .warning-text {
    font-size: 13px; color: #6b7280; margin-bottom: 24px;
}
.delete-popup-actions { display: flex; gap: 10px; }
.delete-popup-actions .btn-cancel {
    flex: 1; padding: 11px; border-radius: 10px;
    border: 1.5px solid #e5e7eb; background: #fff;
    color: #374151; font-weight: 600; font-size: 14px;
    cursor: pointer; transition: all 0.2s;
}
.delete-popup-actions .btn-cancel:hover { background: #f9fafb; border-color: #d1d5db; }
.delete-popup-actions .btn-confirm {
    flex: 1; padding: 11px; border-radius: 10px;
    border: none; background: linear-gradient(135deg, #ef4444, #dc2626);
    color: #fff; font-weight: 700; font-size: 14px;
    cursor: pointer; transition: all 0.2s;
    box-shadow: 0 4px 14px rgba(220,38,38,0.35);
}
.delete-popup-actions .btn-confirm:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    box-shadow: 0 6px 18px rgba(220,38,38,0.45);
    transform: translateY(-1px);
}

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
<div class="container mt-4" style="max-width:1100px;">

    <div class="page-header">
        <h2><i class="fas fa-user-shield me-2"></i> Admin List</h2>
        <p>Manage all admin accounts</p>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('admin.index') }}" class="search-box w-50">
            <div class="input-group">
                <input type="text" name="search" class="form-control"
                       placeholder="Search by name or email…" value="{{ request('search') }}">
                <button type="submit" class="btn px-4">Search</button>
            </div>
        </form>
        <button type="button" class="btn text-white"
            data-bs-toggle="modal" data-bs-target="#addModal"
            style="background:linear-gradient(135deg,#1e3a8a,#1e40af);font-weight:600;border-radius:8px;padding:9px 20px;">
            <i class="fas fa-plus me-1"></i> Add Admin
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="table-card">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $admin)
                <tr>
                    <td>{{ $admins->firstItem() + $loop->index }}</td>
                    <td class="fw-semibold">
                        {{ $admin->name }}
                        @if($admin->id === auth()->id())
                            <span class="badge-you">You</span>
                        @endif
                    </td>
                    <td>{{ $admin->email }}</td>
                    <td class="text-muted" style="font-size:13px;">
                        {{ $admin->created_at->format('d M Y') }}
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-sm btn-warning"
                                data-bs-toggle="modal" data-bs-target="#editModal"
                                data-id="{{ $admin->id }}"
                                data-name="{{ $admin->name }}"
                                data-email="{{ $admin->email }}">
                                <i class="fas fa-pen me-1"></i> Edit
                            </button>
                            @if($admin->id !== auth()->id())
                            <button type="button" class="btn btn-sm btn-danger"
                                onclick="showDeletePopup({{ $admin->id }}, '{{ addslashes($admin->name) }}')">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                            @else
                            <button class="btn btn-sm btn-danger"
                                style="opacity:0.45;cursor:not-allowed;pointer-events:none;">
                                <i class="fas fa-lock me-1"></i> Delete
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted fst-italic">No admins found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($admins->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size:14px;">
                Showing {{ $admins->firstItem() }} to {{ $admins->lastItem() }} of {{ $admins->total() }} results
            </span>
            <div class="d-flex gap-1">
                @if($admins->onFirstPage())
                    <button class="page-btn" disabled>&lsaquo;</button>
                @else
                    <a href="{{ $admins->previousPageUrl() }}" class="page-btn">&lsaquo;</a>
                @endif
                @for($page = 1; $page <= $admins->lastPage(); $page++)
                    @if($page == $admins->currentPage())
                        <button class="page-btn active">{{ $page }}</button>
                    @else
                        <a href="{{ $admins->url($page) }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endfor
                @if($admins->hasMorePages())
                    <a href="{{ $admins->nextPageUrl() }}" class="page-btn">&rsaquo;</a>
                @else
                    <button class="page-btn" disabled>&rsaquo;</button>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

{{-- CUSTOM DELETE POPUP --}}
<div class="delete-popup-overlay" id="deletePopupOverlay" onclick="hideDeletePopup(event)">
    <div class="delete-popup" onclick="event.stopPropagation()">
        <div class="delete-popup-top">
            <button type="button" class="delete-popup-close" onclick="hideDeletePopup()">
                <i class="fas fa-times"></i>
            </button>
            <div class="delete-popup-icon">
                <i class="fas fa-user-times"></i>
            </div>
            <p class="delete-popup-title">Delete Admin Account</p>
        </div>
        <div class="delete-popup-body">
            <p>You are about to permanently delete</p>
            <div class="admin-name" id="popupAdminName">—</div>
            <p class="warning-text"><i class="fas fa-triangle-exclamation me-1 text-warning"></i> This action cannot be undone. The admin will lose all access immediately.</p>
            <form id="deletePopupForm" method="POST">
                @csrf @method('DELETE')
                <div class="delete-popup-actions">
                    <button type="submit" class="btn-confirm">
                        <i class="fas fa-trash me-1"></i> Yes, Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ADD MODAL --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <div class="modal-header-blue">
                <h5 class="text-white fw-bold mb-0"><i class="fas fa-user-plus me-2"></i> Add Admin</h5>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body p-4">
                @if($errors->any())
                <div class="alert alert-danger rounded-3">
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
                @endif
                <form action="{{ route('admin.store') }}" method="POST" id="addAdminForm" autocomplete="off">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Name</label>
                        <input type="text" name="name" class="form-control"
                               placeholder="Admin name" value="{{ old('name') }}"
                               autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Email</label>
                        <input type="email" name="email" id="addEmail" class="form-control"
                               pattern="[a-zA-Z0-9._%+\-]+@gmail\.com"
                               title="Email must use @gmail.com"
                               placeholder="admin@example.com" value="{{ old('email') }}"
                               autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Password</label>
                        <div class="pw-wrapper">
                            <input type="password" name="password" id="addPw" class="form-control"
                                   placeholder="Min. 6 characters"
                                   autocomplete="new-password" required>
                            <button type="button" class="pw-toggle" onclick="togglePw('addPw','addPwIcon')">
                                <i class="fas fa-eye" id="addPwIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Confirm Password</label>
                        <div class="pw-wrapper">
                            <input type="password" name="password_confirmation" id="addPwC" class="form-control"
                                   placeholder="Repeat password"
                                   autocomplete="new-password" required>
                            <button type="button" class="pw-toggle" onclick="togglePw('addPwC','addPwCIcon')">
                                <i class="fas fa-eye" id="addPwCIcon"></i>
                            </button>
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
                <h5 class="text-white fw-bold mb-0"><i class="fas fa-user-edit me-2"></i> Edit Admin</h5>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body p-4">
                <form id="editForm" method="POST" autocomplete="off">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Name</label>
                        <input type="text" name="name" id="edit-name" class="form-control" autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Email</label>
                        <input type="email" name="email" id="edit-email" class="form-control"
                               pattern="[a-zA-Z0-9._%+\-]+@gmail\.com"
                               title="Email must use @gmail.com"
                               autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">
                            New Password
                            <small class="text-muted fw-normal">(leave blank to keep current)</small>
                        </label>
                        <div class="pw-wrapper">
                            <input type="password" name="password" id="editPw" class="form-control"
                                   placeholder="Min. 6 characters" autocomplete="new-password">
                            <button type="button" class="pw-toggle" onclick="togglePw('editPw','editPwIcon')">
                                <i class="fas fa-eye" id="editPwIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="color:#1e3a8a;">Confirm New Password</label>
                        <div class="pw-wrapper">
                            <input type="password" name="password_confirmation" id="editPwC" class="form-control"
                                   placeholder="Repeat new password" autocomplete="new-password">
                            <button type="button" class="pw-toggle" onclick="togglePw('editPwC','editPwCIcon')">
                                <i class="fas fa-eye" id="editPwCIcon"></i>
                            </button>
                        </div>
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

@push('scripts')
<script>
/* ─── Toggle password visibility ─── */
function togglePw(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

/* ─── Clear Add Admin form saat modal dibuka (bypass autofill) ─── */
document.getElementById('addModal').addEventListener('show.bs.modal', function () {
    document.getElementById('addAdminForm').reset();
    // Force clear via JS setelah browser autofill
    setTimeout(function () {
        document.getElementById('addEmail').value = '';
        document.getElementById('addPw').value    = '';
        document.getElementById('addPwC').value   = '';
        // Reset pw type & icon
        ['addPw','addPwC'].forEach(id => {
            document.getElementById(id).type = 'password';
        });
        document.getElementById('addPwIcon').className  = 'fas fa-eye';
        document.getElementById('addPwCIcon').className = 'fas fa-eye';
    }, 50);
});

/* ─── Edit modal populate ─── */
document.getElementById('editModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('editForm').action  = '/admin/' + btn.dataset.id;
    document.getElementById('edit-name').value  = btn.dataset.name;
    document.getElementById('edit-email').value = btn.dataset.email;
    document.getElementById('editPw').value     = '';
    document.getElementById('editPwC').value    = '';
    document.getElementById('editPwIcon').className  = 'fas fa-eye';
    document.getElementById('editPwCIcon').className = 'fas fa-eye';
    document.getElementById('editPw').type  = 'password';
    document.getElementById('editPwC').type = 'password';
});

/* ─── Custom Delete Popup ─── */
function showDeletePopup(id, name) {
    document.getElementById('popupAdminName').textContent = name;
    document.getElementById('deletePopupForm').action = '/admin/' + id;
    document.getElementById('deletePopupOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function hideDeletePopup(event) {
    if (event && event.target !== document.getElementById('deletePopupOverlay')) return;
    const popup = document.querySelector('.delete-popup');
    popup.classList.add('closing');
    setTimeout(function() {
        document.getElementById('deletePopupOverlay').classList.remove('show');
        popup.classList.remove('closing');
        document.body.style.overflow = '';
    }, 200);
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') hideDeletePopup();
});
</script>
@endpush

@endsection