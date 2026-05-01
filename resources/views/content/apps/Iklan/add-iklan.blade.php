@extends('layouts/layoutMaster')

@section('title', 'Buat Notifikasi Baru')

@section('content')
<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner-border spinner-border-custom text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- Page Header -->
<div class="page-header">
    <h4 class="page-title">
        <i class="ri-notification-3-line"></i>Buat Notifikasi Baru
    </h4>
    <p class="page-subtitle">Buat dan kirim notifikasi informasi, maintenance, atau iklan ke pelanggan</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-12">
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <h5 class="alert-heading mb-2">
                <i class="ri-error-warning-line me-2"></i>Terjadi Kesalahan!
            </h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('iklan.store') }}" method="POST" enctype="multipart/form-data" id="iklanForm">
            @csrf

            <div class="card form-modern border-0 shadow-sm">
                <div class="card-header-custom">
                    <h5 class="mb-0 fw-bold">
                        <i class="ri-article-line me-2"></i>Form Notifikasi
                    </h5>
                </div>

                <div class="card-body p-4">
                    
                    <!-- Form Section: Tipe -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="ri-list-settings-line"></i>Jenis Notifikasi
                        </h6>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="type-card" data-type="informasi">
                                    <input type="radio" name="type" value="informasi" id="type-informasi" {{ old('type') === 'informasi' ? 'checked' : '' }} required>
                                    <label for="type-informasi" class="type-label">
                                        <div class="type-icon bg-label-info">
                                            <i class="ri-information-line"></i>
                                        </div>
                                        <div class="type-title">Informasi</div>
                                        <small class="text-muted">Info umum ke pelanggan</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="type-card" data-type="maintenance">
                                    <input type="radio" name="type" value="maintenance" id="type-maintenance" {{ old('type') === 'maintenance' ? 'checked' : '' }}>
                                    <label for="type-maintenance" class="type-label">
                                        <div class="type-icon bg-label-warning">
                                            <i class="ri-tools-line"></i>
                                        </div>
                                        <div class="type-title">Maintenance</div>
                                        <small class="text-muted">Pemberitahuan maintenance</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="type-card" data-type="iklan">
                                    <input type="radio" name="type" value="iklan" id="type-iklan" {{ old('type') === 'iklan' ? 'checked' : '' }}>
                                    <label for="type-iklan" class="type-label">
                                        <div class="type-icon bg-label-success">
                                            <i class="ri-megaphone-line"></i>
                                        </div>
                                        <div class="type-title">Iklan/Promosi</div>
                                        <small class="text-muted">Promosi & penawaran</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @error('type')
                            <small class="text-danger d-block mt-2">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Form Section: Konten -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="ri-edit-2-line"></i>Konten Notifikasi
                        </h6>

                        <!-- Judul -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Judul Notifikasi <span class="text-danger">*</span></label>
                            <div class="input-with-icon">
                                <div class="input-icon">
                                    <i class="ri-text"></i>
                                </div>
                                <input type="text" 
                                       class="@error('title') is-invalid @enderror" 
                                       name="title" 
                                       value="{{ old('title') }}"
                                       required 
                                       maxlength="255"
                                       placeholder="Contoh: Promo Spesial Akhir Tahun!">
                            </div>
                            @error('title')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Pesan -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Pesan Notifikasi <span class="text-danger">*</span></label>
                            <div class="input-with-icon" style="height: auto;">
                                <div class="input-icon" style="align-self: flex-start; height: auto; min-height: 46px; border-right: 1px solid #18181b; padding-top: 10px; padding-bottom: 10px;">
                                    <i class="ri-chat-1-line"></i>
                                </div>
                                <textarea class="@error('message') is-invalid @enderror py-2" 
                                          name="message" 
                                          rows="6" 
                                          required 
                                          minlength="10"
                                          maxlength="1000"
                                          placeholder="Tulis pesan notifikasi Anda di sini..."
                                          style="border: none; outline: none; width: 100%; resize: vertical; padding-left: 1rem; color: #18181b;">{{ old('message') }}</textarea>
                            </div>
                            @error('message')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Minimal 10 karakter</small>
                                <small class="text-muted">
                                    <span id="charCount">0</span>/1000 karakter
                                </small>
                            </div>
                        </div>

                        <!-- Gambar -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Gambar (Opsional)</label>
                            <div class="input-with-icon">
                                <div class="input-icon">
                                    <i class="ri-image-add-line"></i>
                                </div>
                                <input type="file" 
                                       class="@error('image') is-invalid @enderror" 
                                       name="image" 
                                       accept="image/*" 
                                       id="imageInput"
                                       style="padding-top: 10px;">
                            </div>
                            @error('image')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                            <small class="text-muted d-block mt-1">Format: JPG, PNG, GIF (Max 2MB)</small>
                            
                            <div id="imagePreview" class="mt-3 position-relative" style="display: none;">
                                <img id="preview" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 8px; border: 2px solid #e4e4e7;">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2" id="removeImage">
                                    <i class="ri-close-line text-white"></i>
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end gap-3 mt-4">
                        <a href="{{ route('iklan.index') }}" class="btn btn-secondary btn-cancel">
                            <i class="ri-close-line me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary btn-submit" id="submitBtn">
                            <i class="ri-save-line me-1 text-white"></i>Simpan Notifikasi
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-style')
<style>
/* ========================================= */
/* SHADCN UI STYLE - BLACK & WHITE */
/* ========================================= */
:root {
  --primary-color: #18181b;
  --primary-hover: #27272a;
  --gray-bg: #fafafa;
  --gray-border: #e4e4e7;
  --text-primary: #18181b;
  --text-secondary: #71717a;
  --border-radius: 8px;
}

/* Override Theme Colors */
.text-primary { color: #18181b !important; }
.bg-primary { background-color: #18181b !important; }
.btn-primary { 
  background-color: #18181b !important; 
  border-color: #18181b !important;
  color: #fff !important;
}
.btn-primary:hover { 
  background-color: #27272a !important; 
  border-color: #27272a !important; 
}

/* Page Header */
.page-header {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  border: 1px solid var(--gray-border);
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.page-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
  display: flex;
  align-items: center;
}

.page-title i {
  margin-right: 0.75rem;
  color: var(--text-primary);
}

.page-subtitle {
  color: var(--text-secondary);
  font-size: 0.875rem;
  margin-bottom: 0;
}

/* Form Modern Wrapper */
.form-modern {
  border-radius: 12px;
  border: 1px solid var(--gray-border);
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  background: white;
}

/* Header Custom */
.card-header-custom {
  color: var(--text-primary);
  border-radius: 12px 12px 0 0 !important;
  padding: 1.5rem;
  border-bottom: 1px solid var(--gray-border);
  background: #ffffff;
}

/* Form Section */
.form-section {
  background: #ffffff;
  border: 1px solid var(--gray-border);
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
}

.form-section-title {
  color: var(--text-primary);
  font-weight: 600;
  margin-bottom: 1.25rem;
  font-size: 0.9rem;
  text-transform: uppercase;
  display: flex;
  align-items: center;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid var(--gray-border);
}

.form-section-title i {
  margin-right: 0.5rem;
  font-size: 1.1rem;
}

/* Input with Icon */
.input-with-icon {
  position: relative;
  display: flex;
  align-items: stretch;
  width: 100%;
  border: 1px solid var(--gray-border);
  border-radius: var(--border-radius);
  transition: all 0.2s;
  overflow: hidden;
  background: white;
  height: 46px;
}

.input-with-icon:hover {
  border-color: var(--text-primary);
}

.input-with-icon:focus-within {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 2px rgba(24, 24, 27, 0.1);
}

.input-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 45px;
  background: #18181b;
  color: #ffffff;
  font-size: 1.1rem;
  flex-shrink: 0;
  border-right: 1px solid #18181b;
}

.input-with-icon input,
.input-with-icon textarea {
  flex: 1;
  border: none;
  outline: none;
  padding: 0 1rem;
  font-size: 0.875rem;
  background: transparent;
  color: var(--text-primary);
  height: 100%;
}

.input-with-icon input::placeholder,
.input-with-icon textarea::placeholder {
  color: #a1a1aa;
}

/* Type Cards */
.type-card {
    position: relative;
    cursor: pointer;
    transition: all 0.3s;
    height: 100%;
}

.type-card input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.type-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem 1rem;
    border: 2px solid #e4e4e7;
    border-radius: 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    height: 100%;
    background: #ffffff;
}

.type-card:hover .type-label {
    border-color: #18181b;
    background: #fafafa;
    transform: translateY(-2px);
}

.type-card input[type="radio"]:checked + .type-label {
    border-color: #18181b;
    background: #f4f4f5;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.type-title {
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 0.25rem;
    color: #18181b;
}

.type-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    font-size: 1.5rem;
    background: #18181b !important;
    color: white !important;
}

/* Badge Labels within Type Card - Override */
.bg-label-info, .bg-label-warning, .bg-label-success {
    background: #18181b !important;
    color: white !important;
}

/* Buttons */
.btn-submit {
  padding: 0.625rem 1.5rem;
  border-radius: var(--border-radius);
  font-weight: 500;
  transition: all 0.2s;
}

.btn-cancel {
  padding: 0.625rem 1.5rem;
  border-radius: var(--border-radius);
  font-weight: 500;
  transition: all 0.2s;
  border: 1px solid var(--gray-border);
  background: white;
  color: var(--text-primary);
}

.btn-cancel:hover {
  background: #f4f4f5;
  color: var(--text-primary);
}

/* Loading */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(2px);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.spinner-border-custom {
  color: var(--primary-color);
  width: 3rem;
  height: 3rem;
}
</style>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter
    const messageTextarea = document.querySelector('[name="message"]');
    const charCount = document.getElementById('charCount');
    
    if (messageTextarea && charCount) {
        messageTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
            if (this.value.length > 900) {
                charCount.classList.add('text-danger');
            } else {
                charCount.classList.remove('text-danger');
            }
        });
        charCount.textContent = messageTextarea.value.length;
    }

    // Image preview
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const preview = document.getElementById('preview');
    const removeImageBtn = document.getElementById('removeImage');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2048000) {
                    alert('Ukuran file terlalu besar! Maksimal 2MB');
                    this.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    alert('File harus berupa gambar!');
                    this.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    }

    if (removeImageBtn) {
        removeImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            imagePreview.style.display = 'none';
            preview.src = '';
        });
    }

    // Form submission
    const form = document.getElementById('iklanForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form) {
        form.addEventListener('submit', function(e) {
            
            const title = form.querySelector('[name="title"]').value.trim();
            const message = form.querySelector('[name="message"]').value.trim();
            const type = form.querySelector('[name="type"]:checked');

            let errors = [];

            if (!type) errors.push('Pilih tipe notifikasi!');
            if (!title) errors.push('Judul wajib diisi!');
            if (!message || message.length < 10) errors.push('Pesan minimal 10 karakter!');

            if (errors.length > 0) {
                e.preventDefault();
                alert(errors.join('\n'));
                return false;
            }

            if (submitBtn) {
                // Show loading
                $('.loading-overlay').css('display', 'flex');
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

                // Allow form submit to continue
            }
        });
    }
});
</script>
@endsection
