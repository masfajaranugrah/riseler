@extends('layouts/layoutMaster')

@section('title', 'Edit Pelanggan')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
<style>
  /* ========== SHADCN B&W THEME ========== */
  :root {
    --c-bg: #f8fafc;
    --c-card: #ffffff;
    --c-border: #e2e8f0;
    --c-text: #0f172a;
    --c-text-secondary: #64748b;
    --c-text-muted: #94a3b8;
    --c-primary: #0f172a;
    --c-primary-hover: #1e293b;
    --c-focus-ring: rgba(15, 23, 42, 0.08);
    --radius: 0.5rem;
    --radius-lg: 0.75rem;
  }

  body { background: var(--c-bg); }

  /* Page wrapper */
  .form-page { width: 100%; }

  /* Page header */
  .form-page-header {
    margin-bottom: 1.5rem;
  }

  .form-page-header h4 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--c-text);
    margin-bottom: 0.125rem;
    letter-spacing: -0.025em;
  }

  .form-page-header p {
    font-size: 0.8125rem;
    color: var(--c-text-secondary);
    margin: 0;
  }

  /* Card */
  .form-card {
    background: var(--c-card);
    border: 1px solid var(--c-border);
    border-radius: var(--radius-lg);
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    margin-bottom: 1.25rem;
    overflow: hidden;
  }

  .form-card-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--c-border);
    background: var(--c-bg);
  }

  .form-card-header h5 {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--c-text);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .form-card-header h5 i {
    font-size: 1.125rem;
    color: var(--c-text-secondary);
  }

  .form-card-body {
    padding: 1.5rem;
  }

  /* Section divider */
  .section-divider {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin: 0.5rem 0 1.25rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--c-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .section-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--c-border);
  }

  /* Labels */
  .form-label {
    font-weight: 500;
    color: var(--c-text);
    margin-bottom: 0.375rem;
    font-size: 0.8125rem;
  }

  /* Inputs */
  .form-control {
    border-radius: var(--radius);
    border: 1px solid var(--c-border);
    padding: 0.5rem 0.75rem;
    font-size: 0.8125rem;
    color: var(--c-text);
    min-height: 38px;
    transition: border-color 0.15s, box-shadow 0.15s;
  }

  .form-select {
    border-radius: var(--radius);
    border: 1px solid var(--c-border);
    padding: 0.4375rem 2.25rem 0.4375rem 0.75rem;
    font-size: 0.8125rem;
    color: var(--c-text);
    min-height: 38px;
    line-height: 1.5;
    transition: border-color 0.15s, box-shadow 0.15s;
  }

  textarea.form-control {
    height: auto;
  }

  .form-control:focus, .form-select:focus {
    border-color: var(--c-primary);
    box-shadow: 0 0 0 3px var(--c-focus-ring);
  }

  .form-control::placeholder {
    color: var(--c-text-muted);
    font-size: 0.8125rem;
  }

  /* Display field (read-only) */
  .display-field {
    background: var(--c-bg);
    border: 1px solid var(--c-border);
    border-radius: var(--radius);
    padding: 0.5rem 0.75rem;
    font-weight: 600;
    color: var(--c-text);
    font-size: 0.8125rem;
    min-height: 38px;
    display: flex;
    align-items: center;
  }

  /* Hint text */
  .form-hint {
    color: var(--c-text-muted);
    font-size: 0.75rem;
    margin-top: 0.25rem;
  }

  /* File input */
  .file-upload-area {
    border: 2px dashed var(--c-border);
    border-radius: var(--radius);
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.15s, background 0.15s;
    position: relative;
  }

  .file-upload-area:hover {
    border-color: var(--c-text-muted);
    background: var(--c-bg);
  }

  .file-upload-area input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
  }

  .file-upload-area i {
    font-size: 1.5rem;
    color: var(--c-text-muted);
    display: block;
    margin-bottom: 0.375rem;
  }

  .file-upload-area span {
    font-size: 0.8125rem;
    color: var(--c-text-secondary);
  }

  .file-upload-area .upload-hint {
    font-size: 0.6875rem;
    color: var(--c-text-muted);
    display: block;
    margin-top: 0.25rem;
  }

  .preview-image {
    max-width: 250px;
    border-radius: var(--radius);
    border: 1px solid var(--c-border);
    margin-top: 0.75rem;
  }

  /* Buttons */
  .btn-shadcn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
    padding: 0 1rem;
    min-height: 38px;
    font-size: 0.8125rem;
    font-weight: 500;
    border-radius: var(--radius);
    border: 1px solid var(--c-border);
    background: var(--c-card);
    color: var(--c-text);
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
    white-space: nowrap;
  }

  .btn-shadcn:hover {
    background: var(--c-bg);
    color: var(--c-text);
  }

  .btn-shadcn-primary {
    background: var(--c-primary);
    border-color: var(--c-primary);
    color: #fff;
  }

  .btn-shadcn-primary:hover {
    background: var(--c-primary-hover);
    border-color: var(--c-primary-hover);
    color: #fff;
  }

  /* Select2 Override for Shadcn B&W */
  .select2-container--default .select2-selection--single {
    height: 38px !important;
    border: 1px solid var(--c-border) !important;
    border-radius: var(--radius) !important;
    padding: 0.25rem 0.5rem !important;
    background: var(--c-card) !important;
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered {
    color: var(--c-text) !important;
    font-size: 0.8125rem !important;
    line-height: 28px !important;
    padding-left: 0.25rem !important;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
  }

  .select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: var(--c-text-muted) !important;
  }

  .select2-container--default.select2-container--open .select2-selection--single {
    border-color: var(--c-primary) !important;
    box-shadow: 0 0 0 3px var(--c-focus-ring) !important;
  }

  .select2-dropdown {
    border: 1px solid var(--c-border) !important;
    border-radius: var(--radius) !important;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1) !important;
    overflow: hidden;
    margin-top: 4px !important;
  }

  .select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1px solid var(--c-border) !important;
    border-radius: var(--radius) !important;
    padding: 0.5rem 0.75rem !important;
    font-size: 0.8125rem !important;
    outline: none !important;
  }

  .select2-container--default .select2-search--dropdown .select2-search__field:focus {
    border-color: var(--c-primary) !important;
    box-shadow: 0 0 0 3px var(--c-focus-ring) !important;
  }

  .select2-container--default .select2-results__option {
    padding: 0.5rem 0.75rem !important;
    font-size: 0.8125rem !important;
    color: var(--c-text) !important;
  }

  .select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: var(--c-bg) !important;
    color: var(--c-text) !important;
  }

  .select2-container--default .select2-results__option[aria-selected=true] {
    background-color: var(--c-primary) !important;
    color: #fff !important;
  }

  /* Alert */
  .alert-custom {
    border: 1px solid #fecaca;
    background: #fef2f2;
    color: #991b1b;
    border-radius: var(--radius);
    padding: 0.75rem 1rem;
    font-size: 0.8125rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.25rem;
  }

  /* Compression Progress */
  .compression-progress {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 2rem;
    border-radius: var(--radius-lg);
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    z-index: 10000;
    text-align: center;
    min-width: 320px;
    border: 1px solid var(--c-border);
  }

  .compression-progress-bar {
    width: 100%;
    height: 6px;
    background: var(--c-border);
    border-radius: 10px;
    overflow: hidden;
    margin: 1rem 0;
  }

  .compression-progress-fill {
    height: 100%;
    background: var(--c-primary);
    width: 0%;
    transition: width 0.3s ease;
  }

  .compression-info {
    font-size: 0.8125rem;
    color: var(--c-text-secondary);
    margin-top: 0.5rem;
  }

  /* Responsive */
  @media (max-width: 767px) {
    .form-card-body { padding: 1rem; }
    .form-page-header { padding: 0; }
    .btn-actions-top { display: none !important; }
  }

  @media (min-width: 768px) {
    .btn-actions-bottom { display: none !important; }
  }
</style>
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/moment/moment.js'
])
@endsection

@section('page-script')
<!-- Image Compression Library -->
<script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ========== PAKET & TANGGAL LOGIC ==========
    const paketSelect = document.getElementById('paket_id');
    const hargaDisplay = document.getElementById('harga_display');
    const masaDisplay = document.getElementById('masa_display');
    const tanggalMulai = document.getElementById('tanggal_mulai');
    const tanggalBerakhir = document.getElementById('tanggal_berakhir');
    const paketData = @json($paket);
    let manualOverride = false;

    tanggalMulai.value = "{{ old('tanggal_mulai', $pelanggan->tanggal_mulai) }}";
    tanggalBerakhir.value = "{{ old('tanggal_berakhir', $pelanggan->tanggal_berakhir) }}";

    flatpickr(tanggalMulai, {
        dateFormat: 'Y-m-d',
        defaultDate: tanggalMulai.value,
        onChange: function(selectedDates, dateStr) {
            const selected = paketData.find(p => p.id == paketSelect.value);
            if(selected && !manualOverride){
                updateTanggalBerakhir(selected.masa_pembayaran);
            }
        }
    });

    flatpickr(tanggalBerakhir, {
        dateFormat: 'Y-m-d',
        defaultDate: tanggalBerakhir.value,
        allowInput: true,
        onChange: function(selectedDates, dateStr){
            manualOverride = true;
        }
    });

    const formatDate = (date) => {
        const d = new Date(date);
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${d.getFullYear()}-${month}-${day}`;
    };

    function updateTanggalBerakhir(masaHari) {
        if(!masaHari) return tanggalBerakhir._flatpickr.clear();
        const start = new Date(tanggalMulai.value);
        start.setDate(start.getDate() + parseInt(masaHari));
        tanggalBerakhir._flatpickr.setDate(formatDate(start));
    }

    // Initialize Select2 with search
    $('#paket_id').select2({
        placeholder: 'Ketik untuk mencari paket...',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() { return 'Paket tidak ditemukan'; },
            searching: function() { return 'Mencari...'; }
        }
    });

    // Set initial paket values
    const initialPaket = paketData.find(p => p.id == "{{ old('paket_id', $pelanggan->paket_id) }}");
    if(initialPaket){
        hargaDisplay.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(initialPaket.harga);
        masaDisplay.textContent = `${initialPaket.masa_pembayaran} Hari`;
        $('#paket_id').val(initialPaket.id).trigger('change.select2');
    }

    // Handle Select2 change
    $('#paket_id').on('change', function() {
        const selected = paketData.find(p => p.id == this.value);
        if(selected){
            hargaDisplay.textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(selected.harga);
            masaDisplay.textContent = `${selected.masa_pembayaran} Hari`;
            if(!manualOverride){
                updateTanggalBerakhir(selected.masa_pembayaran);
            }
        } else {
            hargaDisplay.textContent = '-';
            masaDisplay.textContent = '-';
            if(!manualOverride) tanggalBerakhir._flatpickr.clear();
        }
    });

    // ========== IMAGE COMPRESSION ==========
    async function compressImageFile(file) {
        const progressDiv = document.createElement('div');
        progressDiv.className = 'compression-progress';
        progressDiv.innerHTML = `
            <div style="margin-bottom: 1rem;">
                <i class="ri-image-line" style="font-size: 2rem; color: var(--c-text-secondary);"></i>
            </div>
            <h6 style="margin-bottom: 0.5rem; color: var(--c-text); font-weight: 700;">Mengompress Gambar...</h6>
            <div class="compression-progress-bar">
                <div class="compression-progress-fill"></div>
            </div>
            <div class="compression-info">
                <span id="compression-status">Memproses...</span>
            </div>
        `;
        document.body.appendChild(progressDiv);
        progressDiv.style.display = 'block';

        try {
            const originalSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            const originalSizeKB = (file.size / 1024).toFixed(2);

            document.getElementById('compression-status').innerHTML = `Ukuran asli: <strong>${originalSizeMB} MB</strong> (${originalSizeKB} KB)`;
            document.querySelector('.compression-progress-fill').style.width = '20%';

            // STEP 1: Initial compression
            const initialOptions = {
                maxSizeMB: 1.8,
                maxWidthOrHeight: 1920,
                useWebWorker: true,
                fileType: 'image/webp',
                initialQuality: 0.8,
                alwaysKeepResolution: false,
                preserveExif: false,
                onProgress: (progress) => {
                    const percent = Math.min(progress * 50 + 20, 70);
                    document.querySelector('.compression-progress-fill').style.width = percent + '%';
                }
            };

            let compressedFile = await imageCompression(file, initialOptions);
            document.querySelector('.compression-progress-fill').style.width = '75%';
            document.getElementById('compression-status').textContent = 'Optimizing...';

            // STEP 2: Force if still > 2MB
            let attempts = 0;
            const maxAttempts = 3;
            const targetSizeMB = 2.0;

            while (compressedFile.size > (targetSizeMB * 1024 * 1024) && attempts < maxAttempts) {
                attempts++;
                const currentSizeMB = (compressedFile.size / (1024 * 1024)).toFixed(2);

                document.getElementById('compression-status').innerHTML = `
                    Optimizing (${attempts}/${maxAttempts})...<br>
                    <small>Current: ${currentSizeMB} MB → Target: < ${targetSizeMB} MB</small>
                `;

                const aggressiveOptions = {
                    maxSizeMB: 1.5 - (attempts * 0.2),
                    maxWidthOrHeight: 1920 - (attempts * 128),
                    useWebWorker: true,
                    fileType: 'image/webp',
                    initialQuality: 0.75 - (attempts * 0.1),
                    alwaysKeepResolution: false,
                    preserveExif: false
                };

                compressedFile = await imageCompression(compressedFile, aggressiveOptions);
                const progress = 75 + (attempts * 7);
                document.querySelector('.compression-progress-fill').style.width = progress + '%';
            }

            document.querySelector('.compression-progress-fill').style.width = '95%';

            // STEP 3: Extreme mode
            if (compressedFile.size > (2 * 1024 * 1024)) {
                document.getElementById('compression-status').innerHTML = `<strong>Extreme Mode...</strong><br><small>Forcing under 2MB</small>`;

                const extremeOptions = {
                    maxSizeMB: 1.5,
                    maxWidthOrHeight: 1280,
                    useWebWorker: true,
                    fileType: 'image/webp',
                    initialQuality: 0.6,
                    alwaysKeepResolution: false,
                    preserveExif: false
                };

                compressedFile = await imageCompression(compressedFile, extremeOptions);
            }

            // Final result
            const finalSizeMB = (compressedFile.size / (1024 * 1024)).toFixed(2);
            const reduction = ((1 - compressedFile.size / file.size) * 100).toFixed(1);

            document.querySelector('.compression-progress-fill').style.width = '100%';
            document.getElementById('compression-status').innerHTML = `
                <strong style="color: #16a34a;">Selesai!</strong><br>
                <div style="margin-top: 0.5rem; font-size: 0.75rem;">
                    <span>${originalSizeMB} MB</span> → <span style="font-weight: 700;">${finalSizeMB} MB</span>
                    <br><span style="color: #16a34a; font-weight: 600;">(${reduction}% lebih kecil)</span>
                </div>
            `;

            setTimeout(() => { progressDiv.remove(); }, 2000);
            return compressedFile;

        } catch (error) {
            console.error('Compression error:', error);
            progressDiv.remove();
            alert('Terjadi kesalahan saat mengompress gambar. Gambar asli akan digunakan.');
            return file;
        }
    }

    // AUTO COMPRESS ON FILE SELECT
    const fotoInput = document.getElementById('foto_ktp');
    const preview = document.getElementById('preview_ktp');

    fotoInput.addEventListener('change', async function(e) {
        const file = e.target.files[0];

        if (!file) {
            preview.style.display = 'none';
            return;
        }

        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/heic'];
        const fileType = file.type || 'image/' + file.name.split('.').pop().toLowerCase();

        if (!validTypes.some(type => fileType.includes(type))) {
            alert('Format tidak didukung! Gunakan JPG, PNG, WebP, atau HEIC');
            fotoInput.value = '';
            preview.style.display = 'none';
            return;
        }

        const maxUploadSize = 50 * 1024 * 1024;
        if (file.size > maxUploadSize) {
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            alert(`File terlalu besar! Ukuran: ${sizeMB} MB. Maksimal: 50 MB`);
            fotoInput.value = '';
            preview.style.display = 'none';
            return;
        }

        const compressedFile = await compressImageFile(file);

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(compressedFile);
        fotoInput.files = dataTransfer.files;

        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(compressedFile);
    });

});
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1">
    <div class="form-page">

        <!-- Header -->
        <div class="form-page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-4">
            <div>
                <h4>Edit Pelanggan</h4>
                <p>Perbarui data pelanggan dengan lengkap dan benar</p>
            </div>
            <div class="d-flex gap-2 btn-actions-top">
                <a href="{{ route('marketing.pelanggan') }}" class="btn-shadcn">
                    <i class="ri-arrow-left-line"></i> Kembali
                </a>
                <button type="submit" form="form-pelanggan" class="btn-shadcn btn-shadcn-primary">
                    <i class="ri-save-line"></i> Simpan Perubahan
                </button>
            </div>
        </div>

        @if(session('error'))
        <div class="alert-custom">
            <i class="ri-error-warning-line"></i> {{ session('error') }}
        </div>
        @endif

        <!-- Form -->
        <form id="form-pelanggan" action="{{ route('marketing.pelanggan.update', $pelanggan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Card: Informasi Pelanggan -->
            <div class="form-card">
                <div class="form-card-header">
                    <h5><i class="ri-user-line"></i> Informasi Pelanggan</h5>
                </div>
                <div class="form-card-body">

                    <!-- Nama Lengkap -->
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('nama_lengkap') is-invalid @enderror"
                               id="nama_lengkap"
                               name="nama_lengkap"
                               placeholder="Masukkan nama lengkap"
                               value="{{ old('nama_lengkap', $pelanggan->nama_lengkap) }}"
                               required>
                        @error('nama_lengkap')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Kontak -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="no_whatsapp" class="form-label">No. WhatsApp</label>
                            <input type="text"
                                   class="form-control @error('no_whatsapp') is-invalid @enderror"
                                   id="no_whatsapp"
                                   name="no_whatsapp"
                                   placeholder="08xxxxxxxxxx"
                                   value="{{ old('no_whatsapp', $pelanggan->no_whatsapp) }}">
                            @error('no_whatsapp')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="no_telp" class="form-label">No. Telepon</label>
                            <input type="text"
                                   class="form-control @error('no_telp') is-invalid @enderror"
                                   id="no_telp"
                                   name="no_telp"
                                   placeholder="08xxxxxxxxxx"
                                   value="{{ old('no_telp', $pelanggan->no_telp) }}">
                            @error('no_telp')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Section: Alamat -->
                    <div class="section-divider"><i class="ri-map-pin-line"></i> Alamat</div>

                    <!-- Alamat Jalan -->
                    <div class="mb-3">
                        <label for="alamat_jalan" class="form-label">Alamat Jalan</label>
                        <input type="text"
                               class="form-control @error('alamat_jalan') is-invalid @enderror"
                               id="alamat_jalan"
                               name="alamat_jalan"
                               placeholder="Jl. Merdeka No. 123"
                               value="{{ old('alamat_jalan', $pelanggan->alamat_jalan) }}">
                        @error('alamat_jalan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- RT, RW, Kode Pos -->
                    <div class="row">
                        <div class="col-4 col-md-3 mb-3">
                            <label for="rt" class="form-label">RT</label>
                            <input type="text"
                                   class="form-control @error('rt') is-invalid @enderror"
                                   id="rt" name="rt" placeholder="001"
                                   value="{{ old('rt', $pelanggan->rt) }}">
                            @error('rt')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-4 col-md-3 mb-3">
                            <label for="rw" class="form-label">RW</label>
                            <input type="text"
                                   class="form-control @error('rw') is-invalid @enderror"
                                   id="rw" name="rw" placeholder="001"
                                   value="{{ old('rw', $pelanggan->rw) }}">
                            @error('rw')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-4 col-md-6 mb-3">
                            <label for="kode_pos" class="form-label">Kode Pos</label>
                            <input type="text"
                                   class="form-control @error('kode_pos') is-invalid @enderror"
                                   id="kode_pos" name="kode_pos" placeholder="12345"
                                   value="{{ old('kode_pos', $pelanggan->kode_pos) }}">
                            @error('kode_pos')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Desa, Kecamatan -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="desa" class="form-label">Desa / Kelurahan</label>
                            <input type="text"
                                   class="form-control @error('desa') is-invalid @enderror"
                                   id="desa" name="desa"
                                   placeholder="Nama desa atau kelurahan"
                                   value="{{ old('desa', $pelanggan->desa) }}">
                            @error('desa')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="kecamatan" class="form-label">Kecamatan</label>
                            <input type="text"
                                   class="form-control @error('kecamatan') is-invalid @enderror"
                                   id="kecamatan" name="kecamatan"
                                   placeholder="Nama kecamatan"
                                   value="{{ old('kecamatan', $pelanggan->kecamatan) }}">
                            @error('kecamatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Kabupaten, Provinsi -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="kabupaten" class="form-label">Kabupaten / Kota</label>
                            <input type="text"
                                   class="form-control @error('kabupaten') is-invalid @enderror"
                                   id="kabupaten" name="kabupaten"
                                   placeholder="Nama kabupaten atau kota"
                                   value="{{ old('kabupaten', $pelanggan->kabupaten) }}">
                            @error('kabupaten')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="provinsi" class="form-label">Provinsi</label>
                            <input type="text"
                                   class="form-control @error('provinsi') is-invalid @enderror"
                                   id="provinsi" name="provinsi"
                                   placeholder="Nama provinsi"
                                   value="{{ old('provinsi', $pelanggan->provinsi) }}">
                            @error('provinsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi (Opsional)</label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                  id="deskripsi" name="deskripsi" rows="3"
                                  placeholder="Catatan tambahan tentang pelanggan">{{ old('deskripsi', $pelanggan->deskripsi) }}</textarea>
                        @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Persetujuan Admin</label>
                        @php
                            $statusLabel = match(strtolower($pelanggan->status ?? 'proses')) {
                                'approve' => 'Disetujui Admin',
                                'reject' => 'Ditolak Admin',
                                default => 'Pelanggan Baru (Menunggu Approval Admin)',
                            };
                        @endphp
                        <input type="text" class="form-control bg-light" value="{{ $statusLabel }}" readonly>
                        <div class="form-hint">Status ini ditentukan oleh admin melalui menu approval.</div>
                    </div>

                    <!-- Progres Pelanggan -->
                    <div class="mb-3">
                        <label for="progres" class="form-label">Progres Pemasangan</label>
                        <select class="form-select @error('progres') is-invalid @enderror" id="progres" name="progres">
                            @foreach(\App\Models\Pelanggan::PROGRES_STAGES as $stage)
                            <option value="{{ $stage }}" {{ old('progres', $pelanggan->progres ?? \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES) == $stage ? 'selected' : '' }}>{{ $stage }}</option>
                            @endforeach
                        </select>
                        <div class="form-hint">Pilih salah satu dari 5 tahap progres pelanggan (termasuk Pending saat ada kendala).</div>
                        @error('progres')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Catatan Progres -->
                    <div class="mb-3">
                        <label for="progress_note" class="form-label">Catatan Progres (Opsional)</label>
                        <textarea class="form-control @error('progress_note') is-invalid @enderror"
                                  id="progress_note" name="progress_note" rows="3"
                                  placeholder="Catatan terkait progres pelanggan untuk dicek admin">{{ old('progress_note', $pelanggan->progress_note) }}</textarea>
                        @error('progress_note')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Upload Foto KTP -->
                    <div>
                        <label class="form-label">Foto KTP</label>
                        <div class="file-upload-area">
                            <input type="file"
                                   class="@error('foto_ktp') is-invalid @enderror"
                                   id="foto_ktp" name="foto_ktp"
                                   accept="image/jpeg,image/jpg,image/png,image/webp,image/heic">
                            <i class="ri-upload-cloud-2-line"></i>
                            <span>Klik atau seret foto KTP ke sini</span>
                            <span class="upload-hint">Format: JPG, PNG, WebP, HEIC — Otomatis compress < 2MB</span>
                        </div>
                        @error('foto_ktp')
                        <div class="text-danger mt-1" style="font-size: 0.8125rem;">{{ $message }}</div>
                        @enderror
                        @if($pelanggan->foto_ktp)
                            <img id="preview_ktp" src="{{ asset('storage/' . $pelanggan->foto_ktp) }}" alt="Foto KTP" class="preview-image">
                        @else
                            <img id="preview_ktp" src="#" alt="Preview" class="preview-image" style="display:none;">
                        @endif
                    </div>

                </div>
            </div>

            <!-- Card: Paket Internet -->
            <div class="form-card">
                <div class="form-card-header">
                    <h5><i class="ri-wifi-line"></i> Paket Internet</h5>
                </div>
                <div class="form-card-body">

                    <!-- Nomor ID -->
                    <div class="mb-3">
                        <label for="nomer_id" class="form-label">Nomor ID Pelanggan</label>
                        <input type="text"
                               class="form-control bg-light @error('nomer_id') is-invalid @enderror"
                               id="nomer_id" name="nomer_id"
                               placeholder="Contoh: PLG001"
                               value="{{ old('nomer_id', $pelanggan->nomer_id) }}"
                               readonly>
                        <div class="form-hint">ID unik untuk pelanggan ini (Dibuat otomatis)</div>
                        @error('nomer_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Pilih Paket (Select2 searchable) -->
                    <div class="mb-3">
                        <label for="paket_id" class="form-label">Pilih Paket Internet <span class="text-danger">*</span></label>
                        <select class="form-select @error('paket_id') is-invalid @enderror"
                                id="paket_id" name="paket_id" required>
                            <option value="">-- Pilih Paket --</option>
                            @foreach($paket as $p)
                            <option value="{{ $p->id }}" {{ (old('paket_id', $pelanggan->paket_id) == $p->id) ? 'selected' : '' }}>
                                {{ $p->nama_paket }} — {{ $p->kecepatan }} Mbps — Rp {{ number_format($p->harga, 0, ',', '.') }}
                            </option>
                            @endforeach
                        </select>
                        <div class="form-hint">Ketik nama paket atau kecepatan untuk mencari</div>
                        @error('paket_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Harga & Masa Aktif -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Paket</label>
                            <div class="display-field" id="harga_display">-</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Masa Aktif</label>
                            <div class="display-field" id="masa_display">-</div>
                        </div>
                    </div>

                    <!-- Tanggal -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                   id="tanggal_mulai" name="tanggal_mulai"
                                   required>
                            @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_berakhir" class="form-label">Tanggal Berakhir <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('tanggal_berakhir') is-invalid @enderror"
                                   id="tanggal_berakhir" name="tanggal_berakhir"
                                   required>
                            <div class="form-hint">Otomatis terisi sesuai masa aktif paket</div>
                            @error('tanggal_berakhir')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

            <!-- Mobile buttons -->
            <div class="d-flex gap-2 mb-4 btn-actions-bottom">
                <a href="{{ route('marketing.pelanggan') }}" class="btn-shadcn" style="flex: 1;">
                    <i class="ri-arrow-left-line"></i> Kembali
                </a>
                <button type="submit" class="btn-shadcn btn-shadcn-primary" style="flex: 1;">
                    <i class="ri-save-line"></i> Simpan
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
