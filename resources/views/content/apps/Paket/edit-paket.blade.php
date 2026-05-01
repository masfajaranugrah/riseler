@extends('layouts/layoutMaster')

@section('title', 'Edit Paket Internet')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/quill/typography.scss',
    'resources/assets/vendor/libs/quill/katex.scss',
    'resources/assets/vendor/libs/quill/editor.scss',
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/dropzone/dropzone.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/tagify/tagify.scss',
    'resources/assets/vendor/libs/highlight/highlight.scss'
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
    padding: 0 1rem; /* Horizontal only */
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
    'resources/assets/vendor/libs/quill/katex.js',
    'resources/assets/vendor/libs/quill/quill.js',
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/dropzone/dropzone.js',
    'resources/assets/vendor/libs/jquery-repeater/jquery-repeater.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/tagify/tagify.js',
    'resources/assets/vendor/libs/highlight/highlight.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/forms-editors.js'])

<script>
document.addEventListener('DOMContentLoaded', function () {
    const hargaInput = document.getElementById('harga');

    // Format input harga Rupiah realtime (Edit Mode)
    function formatRupiah(value) {
        if(!value) return '';
        // hapus semua selain angka
        let number = value.replace(/\D/g, '');
        return new Intl.NumberFormat('id-ID').format(number);
    }
    
    // Initial Format if value exists
    if(hargaInput.value) {
        let raw = hargaInput.value.replace(/\D/g, '');
        if(raw) hargaInput.value = formatRupiah(raw);
    }

    hargaInput.addEventListener('input', function(e) {
        e.target.value = formatRupiah(e.target.value);
    });

    // Form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading overlay
        $('.loading-overlay').css('display', 'flex');
        
        // Clean currency input before submit
        const rawValue = hargaInput.value.replace(/\D/g, '');
        hargaInput.value = rawValue;
        
        // Disable submit button
        const submitBtn = document.querySelector('.btn-submit');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Menyimpan...';
        
        // Submit form after brief delay
        setTimeout(() => {
            this.submit();
        }, 500);
    });
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
        <i class="ri-edit-2-line"></i>Edit Paket Internet
    </h4>
    <p class="page-subtitle">Perbarui informasi paket: <strong class="text-primary">{{ $paket->nama_paket }}</strong></p>
</div>

<form action="{{ route('paket.update', $paket->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="card form-modern border-0 shadow-sm">
        <div class="card-header-custom">
            <h5 class="mb-0 fw-bold">
                <i class="ri-file-edit-line me-2"></i>Formulir Edit Paket
            </h5>
        </div>

        <div class="card-body p-4">
            
            <!-- Informasi Dasar Paket -->
            <div class="form-section">
                <h6 class="form-section-title">
                    <i class="ri-information-line"></i>Informasi Dasar Paket
                </h6>
                
                <div class="mb-4">
                    <label class="form-label" for="namaTitle">Nama Paket</label>
                    <div class="input-with-icon">
                        <div class="input-icon">
                            <i class="ri-price-tag-3-line"></i>
                        </div>
                        <input 
                            type="text" 
                            id="namaTitle" 
                            name="namaTitle"
                            placeholder="Contoh: Paket Hemat 1 Bulan" 
                            required
                            value="{{ old('namaTitle', $paket->nama_paket) }}">
                    </div>
                    <small class="text-muted">Masukkan nama paket yang mudah diingat dan deskriptif</small>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="harga">Harga Paket (IDR)</label>
                        <div class="input-with-icon">
                            <div class="input-icon">
                                <i class="ri-money-dollar-circle-line"></i>
                            </div>
                            <input 
                                type="text" 
                                id="harga" 
                                name="harga" 
                                placeholder="Contoh: 50.000" 
                                required
                                value="{{ old('harga', $paket->harga) }}">
                        </div>
                        <small class="text-muted">Harga akan diformat otomatis</small>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="masaPembayaran">Masa Aktif (Hari)</label>
                        <div class="input-with-icon">
                            <div class="input-icon">
                                <i class="ri-calendar-check-line"></i>
                            </div>
                            <input 
                                type="number" 
                                id="masaPembayaran" 
                                name="masaPembayaran"
                                placeholder="Contoh: 30" 
                                required
                                value="{{ old('masaPembayaran', $paket->masa_pembayaran) }}">
                        </div>
                        <small class="text-muted">Durasi paket dalam satuan hari</small>
                    </div>
                </div>
            </div>

            <!-- Spesifikasi Teknis -->
            <div class="form-section">
                <h6 class="form-section-title">
                    <i class="ri-settings-4-line"></i>Spesifikasi Teknis
                </h6>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="kecepatan">Kecepatan Internet (Mbps)</label>
                        <div class="input-with-icon">
                            <div class="input-icon">
                                <i class="ri-speed-line"></i>
                            </div>
                            <input 
                                type="number" 
                                id="kecepatan" 
                                name="kecepatan"
                                placeholder="Contoh: 20" 
                                required
                                value="{{ old('kecepatan', $paket->kecepatan) }}">
                        </div>
                        <small class="text-muted">Kecepatan maksimal yang ditawarkan</small>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label" for="cycle">Siklus Pembayaran</label>
                        <div class="input-with-icon">
                            <div class="input-icon">
                                <i class="ri-refresh-line"></i>
                            </div>
                            <select id="cycle" name="cycle" required>
                                <option value="">-- Pilih Siklus --</option>
                                <option value="daily" {{ old('cycle', $paket->cycle) === 'daily' ? 'selected' : '' }}>Harian</option>
                                <option value="weekly" {{ old('cycle', $paket->cycle) === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                                <option value="monthly" {{ old('cycle', $paket->cycle) === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                <option value="yearly" {{ old('cycle', $paket->cycle) === 'yearly' ? 'selected' : '' }}>Tahunan</option>
                            </select>
                        </div>
                        <small class="text-muted">Periode berulang untuk pembayaran</small>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-3 mt-4">
                <a href="{{ route('paket.index') }}" class="btn btn-secondary btn-cancel">
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
