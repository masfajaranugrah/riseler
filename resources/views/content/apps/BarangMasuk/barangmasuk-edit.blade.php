@extends('layouts/layoutMaster')

@section('title', 'Edit Barang Masuk - Inventory')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
<style>
/* ========================================= */
/* MODERN PROFESSIONAL UI 2025              */
/* ========================================= */

:root {
  --primary-color: #6366f1;
  --primary-hover: #4f46e5;
  --primary-light: #eef2ff;
  --success-color: #10b981;
  --danger-color: #ef4444;
  --gray-50: #f9fafb;
  --gray-100: #f3f4f6;
  --gray-200: #e5e7eb;
  --gray-300: #d1d5db;
  --gray-400: #9ca3af;
  --gray-500: #6b7280;
  --gray-600: #4b5563;
  --gray-700: #374151;
  --gray-800: #1f2937;
  --gray-900: #111827;
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* ====================================== */
/* PAGE LAYOUT                           */
/* ====================================== */

.app-ecommerce {
  padding: 2rem 1.5rem;
  max-width: 1200px;
  margin: 0 auto;
  animation: fadeIn 0.4s ease-out;
}

@keyframes fadeIn {
  from { 
    opacity: 0; 
    transform: translateY(20px); 
  }
  to { 
    opacity: 1; 
    transform: translateY(0); 
  }
}

/* ====================================== */
/* CARD DESIGN                           */
/* ====================================== */

.card {
  border: 1px solid var(--gray-200);
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-sm);
  transition: var(--transition);
  overflow: hidden;
  background: #ffffff;
}

.card:hover {
  box-shadow: var(--shadow-md);
}

.card-header {
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
  border: none;
  padding: 1.75rem 2rem;
  border-radius: 0;
}

.card-header .card-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #ffffff !important;
  margin-bottom: 0;
  display: flex;
  align-items: center;
  letter-spacing: -0.01em;
}

.card-header .card-title i {
  font-size: 1.25rem;
  margin-right: 0.625rem;
}

.card-body {
  padding: 2.5rem 2rem;
  background: #ffffff;
}

/* ====================================== */
/* TYPOGRAPHY                            */
/* ====================================== */

h4 {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--gray-900);
  margin-bottom: 0.5rem;
  letter-spacing: -0.02em;
  line-height: 1.2;
}

.text-muted {
  font-size: 0.9375rem;
  color: var(--gray-600) !important;
  line-height: 1.5;
}

/* ====================================== */
/* BUTTONS                               */
/* ====================================== */

.btn {
  border-radius: 10px;
  padding: 0.625rem 1.5rem;
  font-weight: 600;
  font-size: 0.875rem;
  transition: var(--transition);
  border: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  letter-spacing: 0.01em;
  box-shadow: var(--shadow-sm);
}

.btn i {
  font-size: 1rem;
}

.btn-primary {
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
  color: #ffffff;
}

.btn-primary:hover {
  background: linear-gradient(135deg, var(--primary-hover) 0%, #3730a3 100%);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
  color: #ffffff;
}

.btn-primary:active {
  transform: translateY(0);
}

.btn-label-secondary {
  background: var(--gray-100);
  color: var(--gray-700);
  border: 1px solid var(--gray-300);
}

.btn-label-secondary:hover {
  background: var(--gray-200);
  border-color: var(--gray-400);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
  color: var(--gray-800);
}

/* ====================================== */
/* FORM CONTROLS                         */
/* ====================================== */

.form-label {
  font-weight: 600;
  color: var(--gray-700);
  margin-bottom: 0.625rem;
  font-size: 0.875rem;
  letter-spacing: 0.01em;
  display: block;
}

.form-label.required::after {
  content: ' *';
  color: var(--danger-color);
  margin-left: 2px;
}

.form-control,
.form-select {
  border-radius: 10px;
  border: 1.5px solid var(--gray-300);
  padding: 0.75rem 1rem;
  transition: var(--transition);
  font-size: 0.875rem;
  background: #ffffff;
  color: var(--gray-900);
  line-height: 1.5;
}

.form-control:focus,
.form-select:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 4px var(--primary-light);
  outline: none;
  background: #ffffff;
}

.form-control::placeholder {
  color: var(--gray-400);
}

textarea.form-control {
  min-height: 120px;
  resize: vertical;
  line-height: 1.6;
}

/* ====================================== */
/* INPUT WITH ICON PREFIX                */
/* ====================================== */

.input-icon {
  position: relative;
  display: flex;
  align-items: stretch;
  width: 100%;
}

.input-icon .icon-prefix {
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
  border: 1.5px solid var(--gray-300);
  border-right: none;
  border-radius: 10px 0 0 10px;
  padding: 0 1.125rem;
  min-width: 52px;
  transition: var(--transition);
}

.input-icon .icon-prefix i {
  font-size: 1.25rem;
  color: var(--primary-color);
  transition: var(--transition);
}

.input-icon .form-control,
.input-icon .form-select {
  border-radius: 0 10px 10px 0;
  border-left: none;
  flex: 1;
}

/* Hover State */
.input-icon:hover .icon-prefix {
  background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
  border-color: var(--gray-400);
}

.input-icon:hover .icon-prefix i {
  color: var(--primary-hover);
}

/* Focus State */
.input-icon .form-control:focus ~ .icon-prefix,
.input-icon .form-select:focus ~ .icon-prefix {
  border-color: var(--primary-color);
  background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
}

.input-icon .form-control:focus ~ .icon-prefix i,
.input-icon .form-select:focus ~ .icon-prefix i {
  color: var(--primary-hover);
  transform: scale(1.1);
}

.input-icon .form-control:focus,
.input-icon .form-select:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 4px var(--primary-light);
}

/* Textarea Wrapper */
.input-icon.textarea-wrapper {
  align-items: stretch;
}

.input-icon.textarea-wrapper .icon-prefix {
  align-items: flex-start;
  padding-top: 0.875rem;
  padding-bottom: 0.875rem;
  min-height: 120px;
}

.input-icon.textarea-wrapper .icon-prefix i {
  margin-top: 0.125rem;
}

.input-icon.textarea-wrapper .form-control {
  min-height: 120px;
  padding-top: 0.875rem;
  padding-bottom: 0.875rem;
}

.input-icon.textarea-wrapper:hover .icon-prefix {
  background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%);
  border-color: var(--gray-400);
}

.input-icon.textarea-wrapper:hover .icon-prefix i {
  color: var(--primary-hover);
}

.input-icon.textarea-wrapper .form-control:focus ~ .icon-prefix {
  border-color: var(--primary-color);
  background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
}

.input-icon.textarea-wrapper .form-control:focus ~ .icon-prefix i {
  color: var(--primary-hover);
  transform: scale(1.1);
}

/* Select2 in Input Icon */
.input-icon .select2-container {
  flex: 1;
}

.input-icon .select2-container .select2-selection--single {
  border-radius: 0 10px 10px 0 !important;
  border-left: none !important;
  border: 1.5px solid var(--gray-300) !important;
  height: 100% !important;
  min-height: 46px;
  padding: 0.75rem 2.75rem 0.75rem 1rem !important;
  background: #ffffff !important;
  transition: var(--transition);
}

.input-icon:hover .select2-container .select2-selection--single {
  border-color: var(--gray-400) !important;
}

.input-icon .select2-container--open .select2-selection--single,
.input-icon .select2-container--focus .select2-selection--single {
  border-color: var(--primary-color) !important;
  box-shadow: 0 0 0 4px var(--primary-light) !important;
}

.input-icon.select2-active .icon-prefix {
  border-color: var(--primary-color);
  background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
}

.input-icon.select2-active .icon-prefix i {
  color: var(--primary-hover);
  transform: scale(1.1);
}

/* Flatpickr Active */
.input-icon.flatpickr-active .icon-prefix {
  border-color: var(--primary-color);
  background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
}

.input-icon.flatpickr-active .icon-prefix i {
  color: var(--primary-hover);
  transform: scale(1.1);
}

/* ====================================== */
/* SELECT2 STYLING                       */
/* ====================================== */

.select2-container--default .select2-selection--single .select2-selection__rendered {
  color: var(--gray-900);
  line-height: 1.5;
  padding: 0 !important;
  font-size: 0.875rem;
}

.select2-container--default .select2-selection--single .select2-selection__placeholder {
  color: var(--gray-400);
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 100%;
  right: 12px;
  top: 0;
}

.select2-container--default .select2-selection--single .select2-selection__arrow b {
  border-color: var(--gray-500) transparent transparent transparent;
  border-width: 6px 5px 0 5px;
  margin-left: -5px;
  margin-top: -3px;
}

.select2-dropdown {
  border: 1px solid var(--gray-200) !important;
  border-radius: 10px;
  box-shadow: var(--shadow-lg);
  margin-top: 6px;
  z-index: 9999;
  overflow: hidden;
}

.select2-container--default .select2-search--dropdown {
  padding: 0.75rem;
  background: var(--gray-50);
}

.select2-container--default .select2-search--dropdown .select2-search__field {
  border: 1.5px solid var(--gray-300);
  border-radius: 8px;
  padding: 0.625rem 0.875rem;
  font-size: 0.875rem;
  transition: var(--transition);
}

.select2-container--default .select2-search--dropdown .select2-search__field:focus {
  border-color: var(--primary-color);
  outline: none;
  box-shadow: 0 0 0 3px var(--primary-light);
}

.select2-results {
  padding: 0.5rem;
}

.select2-container--default .select2-results__option {
  padding: 0.75rem 1rem;
  font-size: 0.875rem;
  transition: var(--transition);
  border-radius: 6px;
  margin-bottom: 0.25rem;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
  background: var(--primary-color) !important;
  color: #ffffff;
}

.select2-container--default .select2-results__option[aria-selected=true] {
  background-color: var(--primary-light);
  color: var(--primary-hover);
  font-weight: 500;
}

/* ====================================== */
/* FLATPICKR STYLING                     */
/* ====================================== */

.flatpickr-input[readonly] {
  background: #ffffff !important;
  cursor: pointer;
}

.flatpickr-calendar {
  border-radius: 12px;
  box-shadow: var(--shadow-lg);
  border: 1px solid var(--gray-200);
}

.flatpickr-months {
  border-radius: 12px 12px 0 0;
}

.flatpickr-day.selected {
  background: var(--primary-color);
  border-color: var(--primary-color);
}

.flatpickr-day.selected:hover {
  background: var(--primary-hover);
  border-color: var(--primary-hover);
}

/* ====================================== */
/* HELPER TEXT                           */
/* ====================================== */

.form-text {
  font-size: 0.8125rem;
  color: var(--gray-500);
  margin-top: 0.5rem;
  display: block;
  line-height: 1.5;
}

/* ====================================== */
/* SPACING                               */
/* ====================================== */

.mb-6 {
  margin-bottom: 2rem;
}

.mb-4 {
  margin-bottom: 1.75rem;
}

.gap-3 {
  gap: 1rem;
}

/* ====================================== */
/* FLEX UTILITIES                        */
/* ====================================== */

.d-flex {
  display: flex;
}

.flex-column {
  flex-direction: column;
}

.justify-content-between {
  justify-content: space-between;
}

.align-items-start {
  align-items: flex-start;
}

.align-items-md-center {
  align-items: center;
}

/* ====================================== */
/* RESPONSIVE                            */
/* ====================================== */

@media (max-width: 768px) {
  .app-ecommerce {
    padding: 1.5rem 1rem;
  }
  
  .card-body {
    padding: 1.75rem 1.5rem;
  }
  
  .card-header {
    padding: 1.5rem;
  }
  
  .d-flex.flex-column.flex-md-row {
    flex-direction: column !important;
    align-items: flex-start !important;
  }
  
  .d-flex.flex-column.flex-md-row > div:last-child {
    margin-top: 1.25rem;
    width: 100%;
  }
  
  .d-flex.gap-3 {
    flex-direction: column;
    width: 100%;
  }
  
  .d-flex.gap-3 .btn {
    width: 100%;
    justify-content: center;
  }

  h4 {
    font-size: 1.5rem;
  }
  
  .input-icon .icon-prefix {
    min-width: 48px;
    padding: 0 1rem;
  }
  
  .input-icon .icon-prefix i {
    font-size: 1.125rem;
  }
  
  .input-icon.textarea-wrapper .icon-prefix {
    min-height: 100px;
  }
  
  .input-icon.textarea-wrapper .form-control {
    min-height: 100px;
  }
}

/* ====================================== */
/* ADDITIONAL POLISH                     */
/* ====================================== */

.shadow-sm {
  box-shadow: var(--shadow-sm) !important;
}

.ri {
  vertical-align: middle;
}

.form-control[type="number"]::-webkit-inner-spin-button,
.form-control[type="number"]::-webkit-outer-spin-button {
  opacity: 1;
}

.bg-light {
  background-color: transparent !important;
}

*:focus-visible {
  outline: 2px solid var(--primary-color);
  outline-offset: 2px;
}

.btn:focus-visible,
.form-control:focus-visible,
.form-select:focus-visible {
  outline: none;
}

.btn:disabled {
  opacity: 0.65;
  cursor: not-allowed;
  transform: none !important;
}
</style>
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
@endsection

@section('content')
<div class="app-ecommerce">
    <form action="{{ route('edit.barangmasuk', $barangMasuk->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6">
            <div>
                <h4 class="mb-1">
                    <i class="ri-edit-line me-2" style="color: var(--primary-color);"></i>
                    Edit Barang Masuk
                </h4>
                <p class="text-muted mb-0">Perbarui informasi barang masuk inventory</p>
            </div>

            <div class="d-flex gap-3">
                <a href="{{ route('index.barangmasuk') }}" class="btn btn-label-secondary">
                    <i class="ri-arrow-left-line"></i>
                    Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i>
                    Simpan Perubahan
                </button>
            </div>
        </div>

        <!-- Form Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    
                    <!-- Card Header -->
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="ri-file-list-3-line"></i>
                            Informasi Barang Masuk
                        </h5>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">

                        <!-- Nama Barang (Select2) -->
                        <div class="mb-4">
                            <label for="barang_id" class="form-label required">Nama Barang</label>
                            <div class="input-icon" id="barang-icon-wrapper">
                                <span class="icon-prefix">
                                    <i class="ri-box-3-line"></i>
                                </span>
                                <select class="form-select" id="barang_id" name="barang_id" required>
                                    <option value="">Pilih barang dari daftar</option>
                                    @foreach($barangs as $barang)
                                        <option value="{{ $barang->id }}" {{ old('barang_id', $barangMasuk->barang_id) == $barang->id ? 'selected' : '' }}>
                                            {{ $barang->nama_barang }} (Stok: {{ $barang->stok }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="form-text">Pilih barang yang akan diupdate</small>
                        </div>

                        <!-- Jumlah -->
                        <div class="mb-4">
                            <label for="jumlah" class="form-label required">Jumlah</label>
                            <div class="input-icon">
                                <span class="icon-prefix">
                                    <i class="ri-stack-line"></i>
                                </span>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="jumlah"
                                    name="jumlah"
                                    min="1"
                                    placeholder="Masukkan jumlah barang"
                                    required
                                    value="{{ old('jumlah', $barangMasuk->jumlah) }}"
                                >
                            </div>
                            <small class="form-text">Jumlah unit barang yang masuk (minimal 1 unit)</small>
                        </div>

                        <!-- Jenis Barang Masuk -->
                        <div class="mb-4">
                            <label for="jenis" class="form-label required">Jenis Transaksi</label>
                            <div class="input-icon">
                                <span class="icon-prefix">
                                    <i class="ri-exchange-line"></i>
                                </span>
                                <select class="form-select" id="jenis" name="jenis" required>
                                    <option value="">Pilih jenis transaksi</option>
                                    <option value="pembelian" {{ old('jenis', $barangMasuk->jenis) == 'pembelian' ? 'selected' : '' }}>Pembelian</option>
                                    <option value="pengembalian_barang" {{ old('jenis', $barangMasuk->jenis) == 'pengembalian_barang' ? 'selected' : '' }}>Pengembalian Barang</option>
                                </select>
                            </div>
                            <small class="form-text">Tentukan sumber atau jenis transaksi barang masuk</small>
                        </div>

                        <!-- Tanggal Masuk -->
                        <div class="mb-4">
                            <label for="tanggal_masuk" class="form-label required">Tanggal Masuk</label>
                            <div class="input-icon" id="tanggal-icon-wrapper">
                                <span class="icon-prefix">
                                    <i class="ri-calendar-line"></i>
                                </span>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="tanggal_masuk"
                                    name="tanggal_masuk"
                                    placeholder="Pilih tanggal"
                                    required
                                    readonly
                                    value="{{ old('tanggal_masuk', $barangMasuk->tanggal_masuk) }}"
                                >
                            </div>
                            <small class="form-text">Tanggal barang masuk ke dalam inventory</small>
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <div class="input-icon textarea-wrapper">
                                <span class="icon-prefix">
                                    <i class="ri-file-text-line"></i>
                                </span>
                                <textarea
                                    class="form-control"
                                    id="keterangan"
                                    name="keterangan"
                                    rows="4"
                                    placeholder="Tambahkan catatan atau keterangan tambahan (opsional)"
                                >{{ old('keterangan', $barangMasuk->keterangan) }}</textarea>
                            </div>
                            <small class="form-text">Informasi tambahan tentang barang masuk (opsional)</small>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // SELECT2 INITIALIZATION
    // ============================================
    
    const $barangSelect = $('#barang_id');
    const $barangIconWrapper = $('#barang-icon-wrapper');
    
    // Initialize Select2
    $barangSelect.select2({
        placeholder: "Pilih barang dari daftar",
        allowClear: true,
        width: '100%'
    });
    
    // Handle icon prefix active state untuk Select2
    $barangSelect.on('select2:open', function() {
        $barangIconWrapper.addClass('select2-active');
    });
    
    $barangSelect.on('select2:close', function() {
        $barangIconWrapper.removeClass('select2-active');
    });
    
    // ============================================
    // FLATPICKR INITIALIZATION
    // ============================================
    
    const $tanggalIconWrapper = $('#tanggal-icon-wrapper');
    
    flatpickr("#tanggal_masuk", {
        dateFormat: "Y-m-d",
        defaultDate: "{{ old('tanggal_masuk', $barangMasuk->tanggal_masuk) }}",
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
        disableMobile: true,
        onOpen: function() {
            $tanggalIconWrapper.addClass('flatpickr-active');
        },
        onClose: function() {
            $tanggalIconWrapper.removeClass('flatpickr-active');
        }
    });
    
});
</script>
@endsection
