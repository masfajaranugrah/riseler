@extends('layouts/layoutMaster')

@section('title', 'Tambah Barang - Inventory')

@section('vendor-style')
<style>
/* ========================================= */
/* MODERN CLEAN UI STYLES 2025 - ADD FORM */
/* ========================================= */

:root {
  --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
  --card-hover-shadow: 0 4px 16px rgba(0,0,0,0.12);
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --primary-gradient: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%);
  --success-gradient: linear-gradient(135deg, #71dd37 0%, #5cb82e 100%);
}

/* Page Container */
.app-ecommerce {
  padding: 2rem;
  animation: fadeIn 0.3s ease-out;
}

/* Clean Card Design */
.card {
  border: none;
  border-radius: var(--border-radius);
  box-shadow: var(--card-shadow);
  transition: var(--transition);
  overflow: hidden;
  background: #ffffff;
}

.card:hover {
  box-shadow: var(--card-hover-shadow);
}

.card-header {
  border-radius: 16px 16px 0 0;
  padding: 1.5rem;
  border-bottom: none;
  background: linear-gradient(135deg, #696cff 0%, #5a5dc9 100%) !important;
}

.card-header .card-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #ffffff !important;
  margin-bottom: 0;
}

.card-body {
  padding: 2rem;
}

/* Page Header */
h4 {
  font-size: 1.5rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.text-muted {
  font-size: 0.875rem;
  color: #6c757d !important;
}

/* Modern Buttons */
.btn {
  border-radius: 8px;
  padding: 0.5rem 1.25rem;
  font-weight: 500;
  transition: var(--transition);
  border: none;
  font-size: 0.875rem;
}

.btn-primary {
  background: var(--primary-gradient);
  box-shadow: 0 2px 8px rgba(105, 108, 255, 0.3);
  color: #ffffff;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #5a5dc9 0%, #4a4db9 100%);
  box-shadow: 0 4px 12px rgba(105, 108, 255, 0.4);
  transform: translateY(-1px);
}

.btn-label-secondary {
  background: #e0e0e0;
  color: #5a5f7d;
  border: none;
}

.btn-label-secondary:hover {
  background: #d0d0d0;
  color: #5a5f7d;
  transform: translateY(-1px);
}

/* Modern Form Controls */
.form-label {
  font-weight: 600;
  color: #5a5f7d;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.form-control,
.form-select {
  border-radius: 8px;
  border: 1.5px solid #e0e0e0;
  padding: 0.625rem 1rem;
  transition: var(--transition);
  font-size: 0.875rem;
}

.form-control:focus,
.form-select:focus {
  border-color: #696cff;
  box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.1);
  outline: none;
}

.form-control::placeholder {
  color: #b8bac5;
}

textarea.form-control {
  min-height: 100px;
  resize: vertical;
}

/* Spacing Utilities */
.mb-6 {
  margin-bottom: 1.5rem;
}

.gap-3 {
  gap: 1rem;
}

/* Flex Utilities */
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

/* Shadow */
.shadow-sm {
  box-shadow: var(--card-shadow) !important;
}

/* Icons */
.ri {
  vertical-align: middle;
}

/* Smooth Animations */
@keyframes fadeIn {
  from { 
    opacity: 0; 
    transform: translateY(10px); 
  }
  to { 
    opacity: 1; 
    transform: translateY(0); 
  }
}

/* Required Field Indicator */
.form-label.required::after {
  content: ' *';
  color: #ff3e1d;
}

/* Input Group with Icon - Icon Hilang Saat Focus */
.input-icon {
  position: relative;
}

.input-icon i {
  position: absolute;
  left: 1rem;
  top: 0.75rem;
  color: #b8bac5;
  pointer-events: none;
  transition: var(--transition);
  z-index: 5;
  font-size: 1.125rem;
  opacity: 1;
}

/* Icon untuk textarea di top */
.input-icon textarea + i {
  top: 1rem;
}

/* HILANGKAN ICON SAAT INPUT DI-FOCUS */
.input-icon .form-control:focus ~ i {
  opacity: 0;
  transform: translateX(-10px);
}

/* Padding left untuk input default */
.input-icon .form-control {
  padding-left: 2.75rem;
  transition: padding 0.3s ease;
}

/* Kurangi padding saat focus (icon hilang) */
.input-icon .form-control:focus {
  padding-left: 1rem;
}

/* Help Text */
.form-text {
  font-size: 0.8125rem;
  color: #6c757d;
  margin-top: 0.25rem;
  display: block;
}

/* Responsive */
@media (max-width: 768px) {
  .app-ecommerce {
    padding: 1rem;
  }
  
  .card-body {
    padding: 1.5rem;
  }
  
  .d-flex.flex-column.flex-md-row {
    flex-direction: column !important;
    align-items: flex-start !important;
  }
  
  .d-flex.flex-column.flex-md-row > div:last-child {
    margin-top: 1rem;
    width: 100%;
  }
  
  .d-flex.gap-3 {
    flex-direction: column;
    width: 100%;
  }
  
  .d-flex.gap-3 .btn {
    width: 100%;
  }

  h4 {
    font-size: 1.25rem;
  }
}

/* Additional Form Styling */
.form-control[type="number"]::-webkit-inner-spin-button,
.form-control[type="number"]::-webkit-outer-spin-button {
  opacity: 1;
}

/* Background Colors */
.bg-light {
  background-color: transparent !important;
}
</style>
@endsection

@section('content')
<div class="app-ecommerce">
    <form action="{{ route('post-barang') }}" method="POST">
        @csrf

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6">
            <div>
                <h4 class="mb-1">
                    <i class="ri-add-box-line me-2 text-primary"></i>
                    Tambah Barang Baru
                </h4>
                <p class="text-muted mb-0">Isi data barang dengan lengkap dan benar</p>
            </div>

            <div class="d-flex gap-3">
                <a href="{{ route('barangs') }}" class="btn btn-label-secondary">
                    <i class="ri-arrow-left-line me-1"></i>
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i>
                    Simpan Data
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">

                <div class="card mb-6 shadow-sm">
                    <div class="card-header bg-light mb-4">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="ri-information-line me-2"></i>
                            Informasi Barang
                        </h5>
                    </div>
                    <div class="card-body">

                        {{-- Nama Barang --}}
                        <div class="mb-4">
                            <label for="nama_barang" class="form-label required">Nama Barang</label>
                            <div class="input-icon">
                                <input
                                    type="text"
                                    class="form-control"
                                    id="nama_barang"
                                    name="nama_barang"
                                    placeholder="Masukkan nama barang"
                                    required
                                    value="{{ old('nama_barang') }}"
                                >
                                <i class="ri-box-3-line"></i>
                            </div>
                            <small class="form-text">Nama lengkap barang yang akan disimpan</small>
                        </div>

                        {{-- Stok Awal --}}
                        <div class="mb-4">
                            <label for="stok" class="form-label">Stok Awal</label>
                            <div class="input-icon">
                                <input
                                    type="number"
                                    class="form-control"
                                    id="stok"
                                    name="stok"
                                    min="0"
                                    placeholder="0"
                                    value="{{ old('stok', 0) }}"
                                >
                                <i class="ri-stack-line"></i>
                            </div>
                            <small class="form-text">Jumlah stok awal barang (default: 0)</small>
                        </div>

                        {{-- Keterangan --}}
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <div class="input-icon">
                                <textarea
                                    class="form-control"
                                    id="keterangan"
                                    name="keterangan"
                                    rows="4"
                                    placeholder="Tambahkan keterangan atau deskripsi barang (opsional)"
                                >{{ old('keterangan') }}</textarea>
                                <i class="ri-file-text-line"></i>
                            </div>
                            <small class="form-text">Informasi tambahan mengenai barang (opsional)</small>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </form>
</div>
@endsection
