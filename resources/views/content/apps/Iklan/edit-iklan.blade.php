@extends('layouts/layoutMaster')

@section('title', 'Buat Iklan Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        {{-- ? TAMPILKAN ERROR --}}
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

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom" style="background: white; padding: 1.5rem;">
                <h5 class="mb-0 fw-bold" style="color: #18181b;">
                    <i class="ri-megaphone-line me-2"></i>Buat Iklan/Promosi Baru
                </h5>
            </div>

            <form action="{{ route('iklan.store') }}" method="POST" enctype="multipart/form-data" id="iklanForm">
                @csrf
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">
                            Judul Iklan <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control form-control-lg @error('title') is-invalid @enderror" 
                               name="title" 
                               value="{{ old('title') }}"
                               required 
                               maxlength="255"
                               placeholder="Contoh: Promo Spesial Akhir Tahun - Diskon 50%!">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Buat judul yang menarik perhatian</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">
                            Pesan Iklan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('message') is-invalid @enderror" 
                                  name="message" 
                                  rows="6" 
                                  required 
                                  minlength="10"
                                  maxlength="1000"
                                  placeholder="Tulis detail iklan/promosi Anda di sini...">{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">Minimal 10 karakter</small>
                            <small class="text-muted">
                                <span id="charCount">0</span>/1000 karakter
                            </small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">Gambar Iklan (Opsional)</label>
                        <input type="file" 
                               class="form-control @error('image') is-invalid @enderror" 
                               name="image" 
                               accept="image/*" 
                               id="imageInput">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1">Format: JPG, PNG, GIF (Max 2MB)</small>
                        
                        <!-- Preview Image -->
                        <div id="imagePreview" class="mt-3 position-relative" style="display: none;">
                            <img id="preview" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 8px; border: 2px solid #e8e8e8;">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2" id="removeImage">
                                <i class="ri-close-line text-white"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white border-top p-4 d-flex gap-2 justify-content-end">
                    <a href="{{ route('iklan.index') }}" class="btn btn-secondary">
                        <i class="ri-close-line me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="ri-save-line me-2 text-white"></i>Simpan Iklan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('page-style')
<style>
/* Black & White Theme */
:root {
  --primary-color: #18181b;
  --gray-border: #e4e4e7;
}

.card {
    border-radius: 12px;
}

.form-control, .form-control-lg {
    border-radius: 8px;
    border: 1px solid var(--gray-border);
    padding: 10px 16px;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(24, 24, 27, 0.1);
}

.btn-primary {
    background: #18181b !important;
    border: 1px solid #18181b !important;
    color: white !important;
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 600;
}

.btn-primary:hover {
    background: #27272a !important;
    transform: translateY(-2px);
}

.btn-secondary {
    background: white !important;
    border: 1px solid #e4e4e7 !important;
    color: #18181b !important;
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 600;
}

.btn-secondary:hover {
    background: #f4f4f5 !important;
    border-color: #18181b !important;
}
</style>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ? CHARACTER COUNTER
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
        // Set initial count
        charCount.textContent = messageTextarea.value.length;
    }

    // ? IMAGE PREVIEW
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const preview = document.getElementById('preview');
    const removeImageBtn = document.getElementById('removeImage');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validasi size
                if (file.size > 2048000) { // 2MB
                    alert('Ukuran file terlalu besar! Maksimal 2MB');
                    this.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }

                // Validasi type
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

    // Remove image preview
    if (removeImageBtn) {
        removeImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            imagePreview.style.display = 'none';
            preview.src = '';
        });
    }

    // ? FORM VALIDATION
    const form = document.getElementById('iklanForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form) {
        form.addEventListener('submit', function(e) {
            const title = form.querySelector('[name="title"]').value.trim();
            const message = form.querySelector('[name="message"]').value.trim();

            if (!title) {
                e.preventDefault();
                alert('Judul wajib diisi!');
                return false;
            }

            if (!message || message.length < 10) {
                e.preventDefault();
                alert('Pesan minimal 10 karakter!');
                return false;
            }

            // Disable submit button untuk prevent double submit
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
            }
        });
    }
});
</script>
@endsection
