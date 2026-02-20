@extends('layouts.app')

@push('styles')
<style>
/* ===============================
 VEHICLE - BLACK & WHITE THEME
================================*/
.form-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.10);
    border: 1px solid #d1d5db;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.form-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18);
}

.card-header {
    background: linear-gradient(135deg, #1f2937, #111827); /* hitam-abu gelap */
    color: white;
    padding: 1.25rem 1.5rem;
    border-bottom: none;
}

.card-header h5 {
    margin: 0;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.card-body {
    padding: 2rem !important;
}

/* Form labels & inputs */
.form-label {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
    display: block;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #9ca3af;
    padding: 0.75rem 1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
    background-color: #f9fafb;
}

.form-control:focus, .form-select:focus {
    border-color: #374151;
    box-shadow: 0 0 0 3px rgba(55, 65, 81, 0.15);
    outline: none;
}

.form-control::placeholder {
    color: #9ca3af;
}

/* File input & preview */
#imageInput {
    padding: 0.5rem;
}

#previewWrapper {
    margin-top: 1rem;
    background: #f3f4f6;
    padding: 1rem;
    border-radius: 10px;
    border: 1px dashed #9ca3af;
}

#previewImg {
    width: 100%;
    max-height: 220px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

/* Buttons */
.btn-save {
    background: linear-gradient(135deg, #374151, #1f2937); /* abu tua → hitam */
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.2s ease;
}

.btn-save:hover {
    background: linear-gradient(135deg, #1f2937, #111827);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(31, 41, 55, 0.35);
}

.btn-cancel {
    background: white;
    color: #1f2937;
    border: 1px solid #9ca3af;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.2s ease;
}

.btn-cancel:hover {
    background: #f3f4f6;
    color: #111827;
    border-color: #6b7280;
    transform: translateY(-2px);
}

/* Error messages */
.alert-danger {
    border-radius: 10px;
    background: #fee2e2;
    border-color: #fecaca;
    color: #991b1b;
}

/* Responsive */
@media (max-width: 576px) {
    .card-body {
        padding: 1.5rem !important;
    }
    .d-flex.gap-2 {
        flex-direction: column;
    }
    .btn-save, .btn-cancel {
        width: 100%;
    }
}
</style>
@endpush

@section('content')
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="form-card">
                <div class="card-header">
                    <h5><i class="fas fa-motorcycle me-2"></i> Tambah Motor Baru</h5>
                </div>
                <div class="card-body">

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Perhatian!</strong> Ada beberapa kesalahan:
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('vehicles.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">Nama Motor</label>
                            <input type="text" name="name" class="form-control" 
                                   placeholder="Contoh: Honda PCX 160" 
                                   value="{{ old('name') }}" required autofocus>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Tipe Motor</label>
                            <select name="type" class="form-select" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="skuter" {{ old('type') == 'scooter' ? 'selected' : '' }}>scooter</option>
                                <option value="sport" {{ old('type') == 'sport' ? 'selected' : '' }}>Sport</option>
                                <option value="trail" {{ old('type') == 'trail' ? 'selected' : '' }}>Adventure / Trail</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Nomor Plat</label>
                            <input type="text" name="plate_number" class="form-control text-uppercase" 
                                   placeholder="Contoh: DK 1234 ABC" 
                                   value="{{ old('plate_number') }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Harga per Hari (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="price_per_day" class="form-control" 
                                       placeholder="Contoh: 85000" 
                                       value="{{ old('price_per_day') }}" min="0" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Foto Motor</label>
                            <input type="file" name="image" class="form-control" accept="image/*" id="imageInput">
                            <small class="text-muted d-block mt-1">
                                Format: jpg, jpeg, png, webp • Maksimal 2 MB • Opsional
                            </small>
                            <div id="previewWrapper" style="display: none; margin-top: 1.25rem;">
                                <small class="text-muted mb-2 d-block">Pratinjau:</small>
                                <img id="previewImg" src="#" alt="Pratinjau Foto Motor" 
                                     style="width: 100%; max-height: 240px; object-fit: cover; border-radius: 12px; border: 1px solid #d1d5db; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-5">
                            <button type="submit" class="btn btn-save flex-fill">
                                <i class="fas fa-save me-2"></i> Simpan Motor
                            </button>
                            <a href="{{ route('vehicles.index') }}" class="btn btn-cancel flex-fill text-center">
                                <i class="fas fa-arrow-left me-2"></i> Batal
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal 2MB!');
                this.value = '';
                document.getElementById('previewWrapper').style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('previewImg');
                previewImg.src = e.target.result;
                document.getElementById('previewWrapper').style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            document.getElementById('previewWrapper').style.display = 'none';
        }
    });
</script>
@endsection