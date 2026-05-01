@extends('layouts/layoutMaster')

@section('title', 'Edit Pemasukan')

{{-- VENDOR STYLE --}}
@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
<style>
  .form-card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    transition: all 0.3s;
  }
  .form-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.12); }
  .card-header-custom {
    background: #18181b;
    border-radius: 12px 12px 0 0;
    padding: 1.25rem 1.5rem;
    border: none;
  }
  .card-title-custom {
    color: #fafafa;
    font-weight: 600;
    font-size: 1.125rem;
    margin: 0;
    display: flex;
    align-items: center;
  }
  .card-title-custom i { margin-right: 0.75rem; font-size: 1.5rem; color: #fafafa; }
  .form-label {
    font-weight: 600;
    color: #18181b;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
  }
  .form-label i { margin-right: 0.5rem; color: #18181b; font-size: 1.1rem; }
  .form-control, .form-select {
    border-radius: 8px;
    border: 1.5px solid #e4e4e7;
    padding: 0.75rem 1rem;
    transition: all 0.3s;
    font-size: 0.9375rem;
  }
  .form-control:focus, .form-select:focus {
    border-color: #18181b;
    box-shadow: 0 0 0 0.2rem rgba(24,24,27,0.1);
  }
  .form-control::placeholder { color: #a1a1aa; font-size: 0.875rem; }
  .input-group-text {
    border-radius: 8px 0 0 8px;
    border: 1.5px solid #e4e4e7;
    border-right: none;
    background: #18181b;
    color: #fafafa;
    font-weight: 700;
    padding: 0.75rem 1rem;
  }
  .input-group .form-control { border-left: none; border-radius: 0 8px 8px 0; }
  .input-group .form-control:focus { border-color: #e4e4e7; box-shadow: none; }
  .input-group:focus-within .input-group-text { border-color: #18181b; }
  .input-group:focus-within .form-control {
    border-color: #18181b;
    box-shadow: 0 0 0 0.2rem rgba(24,24,27,0.1);
  }
  .btn-save, .btn-primary {
    padding: 0.625rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
    background: #18181b !important;
    color: #fafafa !important;
    border: none;
    box-shadow: 0 4px 12px rgba(24,24,27,0.2);
  }
  .btn-save:hover, .btn-primary:hover {
    transform: translateY(-2px);
    background: #27272a !important;
    box-shadow: 0 6px 20px rgba(24,24,27,0.3);
  }
  .btn-cancel {
    padding: 0.625rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
  }
  .btn-cancel:hover { transform: translateY(-2px); }
  .page-header {
    background: #f4f4f5;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid #e4e4e7;
  }
  .page-header h4 { color: #18181b; font-weight: 700; margin-bottom: 0.25rem; }
  .page-header p { color: #71717a; margin: 0; font-size: 0.875rem; }
  .form-text-muted { color: #a1a1aa; font-size: 0.8125rem; margin-top: 0.25rem; display: block; }
  /* Select2 Custom Styling */
  .select2-container--default .select2-selection--single {
    border: 1.5px solid #e4e4e7 !important;
    border-radius: 8px !important;
    height: auto !important;
    padding: 0.625rem 1rem !important;
    transition: all 0.3s;
  }
  .select2-container--default .select2-selection--single:focus,
  .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #18181b !important;
    box-shadow: 0 0 0 0.2rem rgba(24,24,27,0.1) !important;
    outline: none !important;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 1.5 !important;
    padding: 0 !important;
    color: #18181b;
    font-size: 0.9375rem;
  }
  .select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #a1a1aa !important;
    font-size: 0.875rem;
  }
  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100% !important;
    right: 1rem !important;
  }
  .select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: #18181b transparent transparent transparent !important;
    border-width: 6px 5px 0 5px !important;
  }
  .select2-dropdown {
    border: 1.5px solid #e4e4e7 !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
  }
  .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
    background-color: #18181b !important;
  }
  .kategori-dll-wrapper { animation: slideDown 0.3s ease-out; }
  @keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>
@endsection

{{-- VENDOR SCRIPT --}}
@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
@endsection

{{-- PAGE SCRIPT --}}
@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi Select2
    $('#kategori').select2({
        placeholder: '-- Pilih Kategori --',
        width: '100%',
        language: {
            noResults: function() {
                return "Tidak ada kategori ditemukan";
            }
        }
    });

    const kategoriSelect = document.getElementById('kategori');
    const dllInputWrapper = document.getElementById('kategori_dll_wrapper');
    const dllInput = document.getElementById('kategori_dll');
    const jumlahInput = document.getElementById('jumlah');

    // Cek apakah kategori sudah DLL saat load
    if(kategoriSelect.value === 'DLL') {
        dllInputWrapper.style.display = 'block';
        dllInput.required = true;
    }

    // Tampilkan input kategori DLL jika dipilih
    kategoriSelect.addEventListener('change', () => {
        if(kategoriSelect.value === 'DLL') {
            dllInputWrapper.style.display = 'block';
            dllInputWrapper.classList.add('kategori-dll-wrapper');
            dllInput.required = true;
        } else {
            dllInputWrapper.style.display = 'none';
            dllInput.required = false;
        }
    });

    // Inisialisasi Flatpickr untuk tanggal & jam masuk
    flatpickr("#tanggal_masuk", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        locale: {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            },
            months: {
                shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            },
        },
        altInput: true,
        altFormat: "d M Y, H:i"
    });

    // ===== PERBAIKAN: Cegah Double Formatting =====
    let isFormatted = false; // Flag untuk tracking apakah sudah diformat
    
    // Format nilai awal HANYA SEKALI saat load
    if(jumlahInput.value && !isFormatted) {
        let rawValue = jumlahInput.value.toString().replace(/\D/g, '');
        if(rawValue) {
            jumlahInput.value = new Intl.NumberFormat('id-ID').format(rawValue);
            isFormatted = true;
        }
    }

    // Format input jumlah menjadi Rupiah saat diketik
    jumlahInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if(value === '') value = '0';
        this.value = new Intl.NumberFormat('id-ID').format(value);
        isFormatted = true;
    });

    // Agar saat submit tetap mengirim angka murni
    jumlahInput.form.addEventListener('submit', function(e) {
        // Hapus semua titik (separator ribuan) sebelum submit
        jumlahInput.value = jumlahInput.value.replace(/\./g, '');
    });
});
</script>
@endsection

{{-- CONTENT --}}
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="app-income-edit">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div>
                    <h4>
                        <i class="ri-edit-box-line me-2"></i>Edit Pemasukan
                    </h4>
                    <p class="text-muted mb-0">Perbarui data pemasukan keuangan dengan lengkap dan benar</p>
                </div>
                <div class="d-flex gap-2 mt-3 mt-md-0">
                    <a href="{{ route('income.index') }}" class="btn btn-label-secondary btn-cancel">
                        <i class="ri-close-line me-1"></i>Batal
                    </a>
                    <button type="submit" form="form-income" class="btn btn-primary btn-save">
                        <i class="ri-save-line me-1"></i>Update Pemasukan
                    </button>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form id="form-income" action="{{ route('income.update', $income->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Informasi Pemasukan -->
            <div class="card form-card mb-4">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="ri-file-list-3-line"></i>
                        Informasi Pemasukan
                    </h5>
                </div>
                <div class="card-body p-4">

                    <!-- Kategori -->
                    <div class="mb-4">
                        <label for="kategori" class="form-label">
                            <i class="ri-folder-line"></i>Kategori Pemasukan
                        </label>
                        <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategori_default as $k)
                                <option value="{{ $k }}" {{ old('kategori', $income->kategori) == $k ? 'selected' : '' }}>{{ $k }}</option>
                            @endforeach
                        </select>
                        <small class="form-text-muted">
                            <i class="ri-information-line me-1"></i>Pilih kategori pemasukan yang sesuai
                        </small>
                        @error('kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Kategori DLL (Conditional) -->
                    <div class="mb-4" id="kategori_dll_wrapper" style="display:{{ old('kategori', $income->kategori) == 'DLL' ? 'block' : 'none' }};">
                        <label for="kategori_dll" class="form-label">
                            <i class="ri-edit-line"></i>Nama Kategori Lainnya
                        </label>
                        <input 
                            type="text" 
                            class="form-control @error('kategori_dll') is-invalid @enderror" 
                            id="kategori_dll" 
                            name="kategori_dll" 
                            placeholder="Masukkan nama kategori baru"
                            value="{{ old('kategori_dll', $income->kategori_dll) }}">
                        <small class="form-text-muted">
                            <i class="ri-information-line me-1"></i>Isi dengan nama kategori spesifik Anda
                        </small>
                        @error('kategori_dll')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tipe Pembayaran -->
                    <div class="mb-4">
                        <label for="tipe_pembayaran" class="form-label">
                            <i class="ri-wallet-3-line"></i>Tipe Pembayaran
                        </label>
                        <select 
                            class="form-select @error('tipe_pembayaran') is-invalid @enderror" 
                            id="tipe_pembayaran" 
                            name="tipe_pembayaran" 
                            required>
                            <option value="" disabled>-- Pilih tipe --</option>
                            <option value="cash" {{ old('tipe_pembayaran', $income->tipe_pembayaran ?? '') === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ old('tipe_pembayaran', $income->tipe_pembayaran ?? '') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                        <small class="form-text-muted">
                            <i class="ri-information-line me-1"></i>Pilih metode pembayaran yang diterima
                        </small>
                        @error('tipe_pembayaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Nominal Harga -->
                    <div class="mb-4">
                        <label for="jumlah" class="form-label">
                            <i class="ri-money-dollar-box-line"></i>Nominal Pemasukan
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input 
                                type="text" 
                                class="form-control @error('jumlah') is-invalid @enderror" 
                                id="jumlah" 
                                name="jumlah" 
                                placeholder="0"
                                value="{{ old('jumlah', number_format($income->jumlah, 0, ',', '')) }}"
                                required>
                        </div>
                        <small class="form-text-muted">
                            <i class="ri-information-line me-1"></i>Masukkan nominal dalam angka (otomatis terformat)
                        </small>
                        @error('jumlah')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Tanggal & Jam Masuk -->
                    <div class="mb-4">
                        <label for="tanggal_masuk" class="form-label">
                            <i class="ri-calendar-event-line"></i>Tanggal & Jam Masuk
                        </label>
                        <input 
                            type="text" 
                            class="form-control @error('tanggal_masuk') is-invalid @enderror" 
                            id="tanggal_masuk" 
                            name="tanggal_masuk" 
                            placeholder="Pilih tanggal & jam"
                            value="{{ old('tanggal_masuk', \Carbon\Carbon::parse($income->tanggal_masuk)->format('Y-m-d H:i')) }}"
                            required>
                        <small class="form-text-muted">
                            <i class="ri-information-line me-1"></i>Waktu pemasukan diterima
                        </small>
                        @error('tanggal_masuk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-0">
                        <label for="keterangan" class="form-label">
                            <i class="ri-file-text-line"></i>Keterangan (Opsional)
                        </label>
                        <textarea 
                            class="form-control @error('keterangan') is-invalid @enderror" 
                            id="keterangan" 
                            name="keterangan" 
                            rows="4" 
                            placeholder="Tambahkan catatan atau keterangan tambahan mengenai pemasukan ini...">{{ old('keterangan', $income->keterangan) }}</textarea>
                        <small class="form-text-muted">
                            <i class="ri-information-line me-1"></i>Detail atau catatan tambahan (opsional)
                        </small>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Action Buttons (Mobile) -->
            <div class="d-md-none">
                <div class="d-flex gap-2 mb-4">
                    <a href="{{ route('income.index') }}" class="btn btn-label-secondary btn-cancel flex-fill">
                        <i class="ri-close-line me-1"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary btn-save flex-fill">
                        <i class="ri-save-line me-1"></i>Update
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection
