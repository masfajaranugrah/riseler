@extends('layouts/layoutMaster')

@section('title', 'Tambah Karyawan')

{{-- VENDOR STYLE --}}
@section('vendor-style')
@vite([
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
  .btn-save, .btn-primary {
    padding: 0.625rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
    background: #18181b !important;
    color: #fafafa !important;
    border: 1px solid #18181b !important;
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
  .section-divider {
    height: 2px;
    background: linear-gradient(90deg, #18181b 0%, transparent 100%);
    margin: 1.5rem 0;
  }
</style>
@endsection

{{-- VENDOR SCRIPT --}}
@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
@endsection

{{-- PAGE SCRIPT --}}
@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi datepicker
    flatpickr("#date_of_birth", {
        dateFormat: "Y-m-d",
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
        altFormat: "d M Y"
    });
    
    flatpickr("#tanggal_masuk", {
        dateFormat: "Y-m-d",
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
        altFormat: "d M Y",
        defaultDate: "today"
    });
});
</script>
@endsection

{{-- CONTENT --}}
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="app-employee-add">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div>
                    <h4>
                        <i class="ri-user-add-line me-2"></i>Tambah Karyawan Baru
                    </h4>
                    <p class="text-muted mb-0">Isi data karyawan dengan lengkap dan benar</p>
                </div>
                <div class="d-flex gap-2 mt-3 mt-md-0">
                    <a href="" class="btn btn-label-secondary btn-cancel">
                        <i class="ri-close-line me-1"></i>Batal
                    </a>
                    <button type="submit" form="form-employee" class="btn btn-primary btn-save">
                        <i class="ri-save-line me-1"></i>Simpan Data
                    </button>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form id="form-employee" action="{{ route('employees.create.post') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Informasi Pribadi -->
            <div class="card form-card mb-4">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="ri-user-3-line"></i>
                        Informasi Pribadi
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <!-- NIK -->
                        <div class="col-md-6 mb-4">
                            <label for="nik" class="form-label">
                                <i class="ri-id-card-line"></i>NIK
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('nik') is-invalid @enderror" 
                                id="nik" 
                                name="nik" 
                                placeholder="Masukkan NIK karyawan"
                                value="{{ old('nik') }}"
                                required>
                            <small class="form-text-muted">
                                <i class="ri-information-line me-1"></i>Nomor Induk Karyawan (unik)
                            </small>
                            @error('nik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nama Lengkap -->
                        <div class="col-md-6 mb-4">
                            <label for="full_name" class="form-label">
                                <i class="ri-user-line"></i>Nama Lengkap
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('full_name') is-invalid @enderror" 
                                id="full_name" 
                                name="full_name" 
                                placeholder="Masukkan nama lengkap"
                                value="{{ old('full_name') }}"
                                required>
                            <small class="form-text-muted">
                                <i class="ri-information-line me-1"></i>Nama sesuai identitas resmi
                            </small>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Alamat Lengkap -->
                    <div class="mb-4">
                        <label for="full_address" class="form-label">
                            <i class="ri-map-pin-line"></i>Alamat Lengkap
                        </label>
                        <textarea 
                            class="form-control @error('full_address') is-invalid @enderror" 
                            id="full_address" 
                            name="full_address" 
                            rows="3" 
                            placeholder="Masukkan alamat lengkap (Jalan, RT/RW, Kelurahan, Kecamatan, Kota, Provinsi)"
                            required>{{ old('full_address') }}</textarea>
                        <small class="form-text-muted">
                            <i class="ri-information-line me-1"></i>Alamat domisili saat ini
                        </small>
                        @error('full_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Tempat Lahir -->
                        <div class="col-md-6 mb-4">
                            <label for="place_of_birth" class="form-label">
                                <i class="ri-map-pin-2-line"></i>Tempat Lahir
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('place_of_birth') is-invalid @enderror" 
                                id="place_of_birth" 
                                name="place_of_birth" 
                                placeholder="Contoh: Jakarta"
                                value="{{ old('place_of_birth') }}"
                                required>
                            @error('place_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="col-md-6 mb-4">
                            <label for="date_of_birth" class="form-label">
                                <i class="ri-calendar-line"></i>Tanggal Lahir
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('date_of_birth') is-invalid @enderror" 
                                id="date_of_birth" 
                                name="date_of_birth" 
                                placeholder="Pilih tanggal lahir"
                                value="{{ old('date_of_birth') }}"
                                required>
                            <small class="form-text-muted">
                                <i class="ri-information-line me-1"></i>Sesuai identitas resmi
                            </small>
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Nomor HP -->
                    <div class="mb-0">
                        <label for="no_hp" class="form-label">
                            <i class="ri-smartphone-line"></i>Nomor HP
                        </label>
                        <input 
                            type="text" 
                            class="form-control @error('no_hp') is-invalid @enderror" 
                            id="no_hp" 
                            name="no_hp" 
                            placeholder="Contoh: 08123456789"
                            value="{{ old('no_hp') }}"
                            required>
                        <small class="form-text-muted">
                            <i class="ri-information-line me-1"></i>Nomor yang aktif dan dapat dihubungi
                        </small>
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Informasi Kepegawaian -->
            <div class="card form-card mb-4">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="ri-briefcase-line"></i>
                        Informasi Kepegawaian
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Tanggal Masuk -->
                        <div class="col-md-6 mb-4">
                            <label for="tanggal_masuk" class="form-label">
                                <i class="ri-calendar-check-line"></i>Tanggal Masuk
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('tanggal_masuk') is-invalid @enderror" 
                                id="tanggal_masuk" 
                                name="tanggal_masuk" 
                                placeholder="Pilih tanggal masuk"
                                value="{{ old('tanggal_masuk') }}"
                                required>
                            <small class="form-text-muted">
                                <i class="ri-information-line me-1"></i>Tanggal mulai bekerja
                            </small>
                            @error('tanggal_masuk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Jabatan -->
                        <div class="col-md-6 mb-0">
                            <label for="jabatan" class="form-label">
                                <i class="ri-award-line"></i>Jabatan
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('jabatan') is-invalid @enderror" 
                                id="jabatan" 
                                name="jabatan" 
                                placeholder="Contoh: Staff Marketing"
                                value="{{ old('jabatan') }}"
                                required>
                            <small class="form-text-muted">
                                <i class="ri-information-line me-1"></i>Posisi/jabatan karyawan
                            </small>
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Rekening Bank -->
            <div class="card form-card mb-4">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="ri-bank-card-line"></i>
                        Informasi Rekening Bank
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Bank -->
                        <div class="col-md-4 mb-4">
                            <label for="bank" class="form-label">
                                <i class="ri-bank-line"></i>Nama Bank
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('bank') is-invalid @enderror" 
                                id="bank" 
                                name="bank" 
                                placeholder="Contoh: BCA, Mandiri, BRI"
                                value="{{ old('bank') }}"
                                required>
                            @error('bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nomor Rekening -->
                        <div class="col-md-4 mb-4">
                            <label for="no_rekening" class="form-label">
                                <i class="ri-bank-card-2-line"></i>Nomor Rekening
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('no_rekening') is-invalid @enderror" 
                                id="no_rekening" 
                                name="no_rekening" 
                                placeholder="Masukkan nomor rekening"
                                value="{{ old('no_rekening') }}"
                                required>
                            @error('no_rekening')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Atas Nama -->
                        <div class="col-md-4 mb-4">
                            <label for="atas_nama" class="form-label">
                                <i class="ri-user-3-line"></i>Atas Nama
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('atas_nama') is-invalid @enderror" 
                                id="atas_nama" 
                                name="atas_nama" 
                                placeholder="Nama pemilik rekening"
                                value="{{ old('atas_nama') }}"
                                required>
                            <small class="form-text-muted">
                                <i class="ri-information-line me-1"></i>Sesuai buku rekening
                            </small>
                            @error('atas_nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dokumen Karyawan -->
            <div class="card form-card mb-4">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="ri-file-list-3-line"></i>
                        Dokumen Karyawan (Terenkripsi)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Foto -->
                        <div class="col-md-6 mb-4">
                            <label for="foto" class="form-label">
                                <i class="ri-image-line"></i>Foto Karyawan
                            </label>
                            <input 
                                type="file" 
                                class="form-control @error('foto') is-invalid @enderror" 
                                id="foto" 
                                name="foto" 
                                accept="image/*">
                            <small class="form-text-muted">
                                <i class="ri-information-line me-1"></i>Format: JPG, PNG, JPEG (Maks. 2MB). File akan dienkripsi untuk keamanan.
                            </small>
                            @error('foto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Foto KTP -->
                        <div class="col-md-6 mb-4">
                            <label for="foto_ktp" class="form-label">
                                <i class="ri-id-card-line"></i>Foto KTP
                            </label>
                            <input 
                                type="file" 
                                class="form-control @error('foto_ktp') is-invalid @enderror" 
                                id="foto_ktp" 
                                name="foto_ktp" 
                                accept="image/*">
                            <small class="form-text-muted">
                                <i class="ri-information-line me-1"></i>Format: JPG, PNG, JPEG (Maks. 2MB). File akan dienkripsi untuk keamanan.
                            </small>
                            @error('foto_ktp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons (Mobile) -->
            <div class="d-md-none">
                <div class="d-flex gap-2 mb-4">
                    <a href="" class="btn btn-label-secondary btn-cancel flex-fill">
                        <i class="ri-close-line me-1"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary btn-save flex-fill">
                        <i class="ri-save-line me-1"></i>Simpan
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection
