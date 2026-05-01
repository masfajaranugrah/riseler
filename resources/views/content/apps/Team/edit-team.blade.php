@extends('layouts/layoutMaster')

@section('title', 'Edit User')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.scss',
])
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
  .btn-primary:focus {
    box-shadow: 0 0 0 0.25rem rgba(24, 24, 27, 0.25) !important;
  }

  /* Form Modern Wrapper */
  .form-modern {
    border-radius: 12px;
    border: 1px solid var(--gray-border);
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  }
  
  /* Header Custom */
  .card-header-custom {
    color: var(--text-primary);
    border-radius: 12px 12px 0 0 !important;
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-border);
    background: #ffffff;
  }
  
  .form-label {
    font-weight: 500;
    font-size: 0.875rem;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
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
    height: 46px; /* Explicit height */
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
  .input-with-icon select {
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
  .input-with-icon select::placeholder {
    color: #a1a1aa;
  }
  
  .input-with-icon select {
    cursor: pointer;
    background-color: transparent;
  }
  
  /* Select2 Custom Styling */
  .select2-container {
    width: 100% !important;
    display: block !important;
  }
  .select2-container--default .select2-selection--single {
      border: none !important;
      height: 44px !important; /* Match parent height minus borders approximately or fill */
      background: transparent !important;
      display: flex !important;
      align-items: center !important;
      border-radius: 0 !important; /* Reset radius since wrapper governs it */
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered {
      color: var(--text-primary) !important;
      padding-left: 1rem !important; /* Visual padding matching inputs */
      line-height: normal !important;
      width: 100%;
  }
  .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 100% !important;
      right: 0.5rem !important;
      position: absolute !important;
      top: 0 !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      width: 2rem !important;
  }
  .select2-container--default .select2-selection--single .select2-selection__arrow b {
      margin-top: 0 !important; /* Fix arrow vertical centering */
      border-color: #a1a1aa transparent transparent transparent !important;
  }
  
  /* Make placeholder look right */
  .select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #a1a1aa !important;
    font-size: 0.875rem;
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
  
  /* Buttons */
  .btn-submit {
    padding: 0.625rem 1.5rem;
    border-radius: var(--border-radius);
    font-weight: 500;
    transition: all 0.2s;
    background: var(--primary-color);
    border: 1px solid var(--primary-color);
    color: white;
  }
  
  .btn-submit:hover {
    background: var(--primary-hover);
    border-color: var(--primary-hover);
    transform: translateY(-1px);
    color: white;
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

  /* Helper Text */
  .text-muted {
    color: var(--text-secondary) !important;
    font-size: 0.75rem;
    margin-top: 0.25rem;
    display: block;
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

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js',
])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi Select2
    $('.select2').each(function() {
        $(this).select2({
            placeholder: $(this).data('placeholder'),
            width: '100%'
        });
    });

    $('#role').select2({
        placeholder: '-- Pilih Peran Pengguna --',
        width: '100%'
    });

    $('#employee_id').select2({
        placeholder: '-- Pilih Karyawan --',
        width: '100%'
    });

    // Toggle Password Visibility (Fix)
    const toggles = document.querySelectorAll('.password-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation(); // prevent closing if inside clickable area
            const inputWrapper = this.closest('.input-with-icon');
            const input = inputWrapper.querySelector('input');
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ri-eye-off-line');
                icon.classList.add('ri-eye-line');
            } else {
                input.type = 'password';
                icon.classList.remove('ri-eye-line');
                icon.classList.add('ri-eye-off-line');
            }
        });
    });
    
    // Form submission
    const form = document.querySelector('form');
    if(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading overlay
            $('.loading-overlay').css('display', 'flex');
            
            // Disable submit button
            const submitBtn = document.querySelector('.btn-submit');
            if(submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Menyimpan...';
            }
            
            // Submit form after brief delay
            setTimeout(() => {
                this.submit();
            }, 500);
        });
    }
});
</script>
@endsection

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
        <i class="ri-edit-box-line"></i>Edit User
    </h4>
    <p class="page-subtitle">Perbarui data user dan akun login</p>
</div>

<form id="form-user" action="{{ route('users.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card form-modern border-0 shadow-sm">
        <div class="card-header-custom">
            <h5 class="mb-0 fw-bold">
                <i class="ri-user-settings-line me-2"></i>Informasi User
            </h5>
        </div>

        <div class="card-body p-4">
            
            <div class="form-section">
                <h6 class="form-section-title">
                    <i class="ri-account-circle-line"></i>Data Akun
                </h6>

                <!-- Karyawan -->
                <div class="mb-4">
                    <label for="employee_id" class="form-label">Pilih Karyawan</label>
                    <div class="input-with-icon">
                        <div class="input-icon">
                            <i class="ri-user-search-line"></i>
                        </div>
                        <div class="flex-grow-1">
                            <select name="employee_id" id="employee_id" class="form-select" required style="border: none;">
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" 
                                        {{ (old('employee_id') == $emp->id) || ($user->name == $emp->full_name) ? 'selected' : '' }}>
                                        {{ $emp->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @error('employee_id')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-with-icon">
                        <div class="input-icon">
                            <i class="ri-mail-line"></i>
                        </div>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="contoh@email.com"
                            value="{{ old('email', $user->email) }}"
                            required>
                    </div>
                    @error('email')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Role -->
                <div class="mb-4">
                    <label for="role" class="form-label">Peran (Role)</label>
                    <div class="input-with-icon">
                        <div class="input-icon">
                            <i class="ri-shield-user-line"></i>
                        </div>
                        <div class="flex-grow-1">
                            <select id="role" name="role" required class="form-select" style="border: none;">
                                <option value="">-- Pilih Role --</option>
                                <option value="administrator" {{ old('role', $user->role) == 'administrator' ? 'selected' : '' }}>Administrator</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="marketing" {{ old('role', $user->role) == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="directur" {{ old('role', $user->role) == 'directur' ? 'selected' : '' }}>Directur</option>
                                <option value="koordinator" {{ old('role', $user->role) == 'koordinator' ? 'selected' : '' }}>Koordinator</option>
                                <option value="customer_service" {{ old('role', $user->role) == 'customer_service' ? 'selected' : '' }}>Customer Service</option>
                                <option value="team" {{ old('role', $user->role) == 'team' ? 'selected' : '' }}>Team</option>
                                <option value="teknisi" {{ old('role', $user->role) == 'teknisi' ? 'selected' : '' }}>Teknisi</option>
                                <option value="karyawan" {{ old('role', $user->role) == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                                <option value="logistic" {{ old('role', $user->role) == 'logistic' ? 'selected' : '' }}>Logistic</option>
                                <option value="verifikasi" {{ old('role', $user->role) == 'verifikasi' ? 'selected' : '' }}>Verifikasi Tagihan</option>
                            </select>
                        </div>
                    </div>
                    @error('role')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="form-section">
                <h6 class="form-section-title">
                    <i class="ri-lock-password-line"></i>Keamanan
                </h6>
                
                <div class="row">
                    <!-- Password -->
                    <div class="col-md-6 mb-4">
                        <label for="password" class="form-label">Password Baru (Opsional)</label>
                        <div class="input-with-icon">
                            <div class="input-icon">
                                <i class="ri-key-2-line"></i>
                            </div>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                autocomplete="new-password"
                                placeholder="Kosongkan jika tidak ubah">
                            <span class="password-toggle p-2 me-2" style="cursor: pointer;">
                                <i class="ri-eye-off-line"></i>
                            </span>
                        </div>
                        <small class="text-muted">Minimal 8 karakter jika diisi</small>
                        @error('password')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="col-md-6 mb-4">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <div class="input-with-icon">
                            <div class="input-icon">
                                <i class="ri-lock-line"></i>
                            </div>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                autocomplete="new-password"
                                placeholder="Ulangi password baru">
                            <span class="password-toggle p-2 me-2" style="cursor: pointer;">
                                <i class="ri-eye-off-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-3 mt-4">
                <a href="{{ route('users.index') }}" class="btn btn-secondary btn-cancel">
                    <i class="ri-close-line me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary btn-submit">
                    <i class="ri-save-line me-1"></i>Simpan Perubahan
                </button>
            </div>

        </div>
    </div>
</form>
@endsection
