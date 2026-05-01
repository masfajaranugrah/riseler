@extends('layouts/layoutMaster')

@section('title', 'Edit Pelanggan')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
<style>
/* ========================================= */
/* SHADCN UI STYLE - BLACK & WHITE */
/* ========================================= */
:root {
  --primary-color: #18181b;
  --gray-bg: #fafafa;
  --gray-border: #e4e4e7;
  --text-muted: #71717a;
}

.form-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
  transition: all 0.3s;
  background: white;
}

.form-card:hover {
  box-shadow: 0 4px 20px rgba(0,0,0,0.12);
}

.card-header-custom {
  background: #18181b !important;
  border-radius: 12px 12px 0 0;
  padding: 1.5rem 2rem;
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

.card-title-custom i {
  margin-right: 0.75rem;
  font-size: 1.5rem;
  color: #fafafa;
}

.form-label {
  font-weight: 600;
  color: #18181b;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
}

.form-label i {
  margin-right: 0.5rem;
  color: #18181b;
  font-size: 1.1rem;
}

.form-control, .form-select {
  border-radius: 8px;
  border: 1.5px solid #e4e4e7;
  padding: 0.75rem 1rem;
  transition: all 0.3s;
  font-size: 0.9375rem;
  color: #18181b;
}

.form-control:focus, .form-select:focus {
  border-color: #18181b;
  box-shadow: 0 0 0 0.2rem rgba(24, 24, 27, 0.1);
}

.form-control::placeholder {
  color: #a1a1aa;
  font-size: 0.875rem;
}

/* Buttons */
.btn-save {
  background: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
  padding: 0.625rem 2rem;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s;
  box-shadow: 0 4px 12px rgba(24, 24, 27, 0.25);
}

.btn-save:hover {
  background: #27272a !important;
  border-color: #27272a !important;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(24, 24, 27, 0.35);
}

.btn-cancel {
  background: transparent !important;
  color: #18181b !important;
  border: 1px solid #e4e4e7 !important;
  padding: 0.625rem 2rem;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-cancel:hover {
  background: #18181b !important;
  color: #fafafa !important;
  border-color: #18181b !important;
  transform: translateY(-2px);
}

.btn-primary {
  background: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn-primary:hover {
  background: #27272a !important;
  border-color: #27272a !important;
}

.page-header {
  background: #ffffff;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  border: 1px solid #e4e4e7;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.page-header h4 {
  color: #18181b;
  font-weight: 700;
  margin-bottom: 0.25rem;
}

.page-header h4 i {
  color: #18181b;
}

.page-header p {
  color: #71717a;
  margin: 0;
  font-size: 0.875rem;
}

.preview-image {
  max-width: 300px;
  border-radius: 12px;
  border: 2px solid #e4e4e7;
  margin-top: 1rem;
}

.section-header {
  color: #18181b;
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 1.5rem;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #18181b;
  display: flex;
  align-items: center;
}

.section-header i {
  margin-right: 0.75rem;
  font-size: 1.25rem;
  color: #18181b;
}

.form-text-muted {
  color: #71717a;
  font-size: 0.8125rem;
  margin-top: 0.25rem;
  display: block;
}

.display-field {
  background: #fafafa;
  border: 1.5px solid #e4e4e7;
  border-radius: 8px;
  padding: 0.75rem 1rem;
  font-weight: 600;
  color: #18181b;
}

/* Select2 Custom Styling - Black Theme */
.select2-container--default .select2-selection--single {
  border-radius: 8px !important;
  border: 1.5px solid #e4e4e7 !important;
  height: auto !important;
  padding: 0.75rem 1rem !important;
  transition: all 0.3s !important;
  background: #fff !important;
}

.select2-container--default .select2-selection--single:hover {
  border-color: #18181b !important;
}

.select2-container--default .select2-selection--single:focus,
.select2-container--default.select2-container--open .select2-selection--single {
  border-color: #18181b !important;
  box-shadow: 0 0 0 0.2rem rgba(24, 24, 27, 0.1) !important;
  background: #fff !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
  color: #18181b !important;
  line-height: 1.5 !important;
  padding: 0 !important;
  font-size: 0.9375rem !important;
}

.select2-container--default .select2-selection--single .select2-selection__placeholder {
  color: #a1a1aa !important;
  font-size: 0.875rem !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 100% !important;
  right: 10px !important;
  color: #18181b !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow b {
  border-color: #18181b transparent transparent transparent !important;
}

.select2-dropdown {
  border: 1.5px solid #e4e4e7 !important;
  border-radius: 8px !important;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
  background: #fff !important;
  color: #18181b !important;
}

.select2-container--default .select2-search--dropdown .select2-search__field {
  border: 1.5px solid #e4e4e7 !important;
  border-radius: 6px !important;
  padding: 0.625rem !important;
  font-size: 0.875rem !important;
  color: #18181b !important;
  background: #fff !important;
}

.select2-container--default .select2-search--dropdown .select2-search__field:focus {
  border-color: #18181b !important;
  outline: none !important;
  box-shadow: 0 0 0 0.15rem rgba(24, 24, 27, 0.1) !important;
  background: #fff !important;
}

.select2-container--default .select2-results__option--highlighted[aria-selected],
.select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
  background-color: #18181b !important;
  color: #fafafa !important;
}

.select2-container--default .select2-results__option[aria-selected=true],
.select2-container--default .select2-results__option--selected {
  background-color: #f4f4f5 !important;
  color: #18181b !important;
}

.select2-results__option {
  padding: 0.625rem 1rem !important;
  font-size: 0.9375rem !important;
  color: #18181b !important;
  background: #fff !important;
}

.select2-container--default .select2-results > .select2-results__options {
  background: #fff !important;
}

/* Select2 Clear Button */
.select2-container--default .select2-selection--single .select2-selection__clear {
  color: #71717a;
  font-size: 1.2rem;
  margin-right: 10px;
}

.select2-container--default .select2-selection--single .select2-selection__clear:hover {
  color: #18181b;
}

/* Alert Styling */
.alert-danger {
  background: #fef2f2 !important;
  border: 1px solid #fecaca !important;
  color: #dc2626 !important;
  border-radius: 8px;
}

/* Animation */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.form-card {
  animation: fadeIn 0.3s ease-out;
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
<script>
document.addEventListener('DOMContentLoaded', function () {

    const paketData = @json($paket);
    let manualOverride = false;

    // ? INISIALISASI SELECT2 UNTUK PAKET
    $('#paket_id').select2({
        placeholder: '?? Cari paket berdasarkan nama atau kecepatan...',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "Paket tidak ditemukan";
            },
            searching: function() {
                return "Mencari paket...";
            }
        },
        templateResult: formatPaket,
        templateSelection: formatPaketSelection
    });

    // ? FORMAT TAMPILAN OPSI PAKET DI DROPDOWN
    function formatPaket(paket) {
        if (!paket.id) {
            return paket.text;
        }

        const selectedPaket = paketData.find(p => p.id == paket.id);
        if (!selectedPaket) return paket.text;

        const hargaFormatted = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(selectedPaket.harga);

        const $paket = $(
            '<div class="select2-paket-option">' +
                '<div style="font-weight: 500; margin-bottom: 2px;">' +
                    '<i class="ri-wifi-line" style="margin-right: 6px;"></i>' +
                    paket.text +
                '</div>' +

            '</div>'
        );
        return $paket;
    }

    // ? FORMAT TAMPILAN PAKET YANG DIPILIH
    function formatPaketSelection(paket) {
        if (!paket.id) {
            return paket.text;
        }
        const selectedPaket = paketData.find(p => p.id == paket.id);
        if (!selectedPaket) return paket.text;

        return paket.text + ' - ' + selectedPaket.kecepatan + ' Mbps';
    }

    const hargaDisplay = document.getElementById('harga_display');
    const masaDisplay = document.getElementById('masa_display');
    const tanggalMulai = document.getElementById('tanggal_mulai');
    const tanggalBerakhir = document.getElementById('tanggal_berakhir');

    // Set nilai awal dari database
    tanggalMulai.value = "{{ old('tanggal_mulai', $pelanggan->tanggal_mulai) }}";
    tanggalBerakhir.value = "{{ old('tanggal_berakhir', $pelanggan->tanggal_berakhir) }}";

    // Flatpickr tanggal mulai
    flatpickr(tanggalMulai, {
        dateFormat: 'Y-m-d',
        defaultDate: tanggalMulai.value,
        onChange: function(selectedDates, dateStr) {
            const paketId = $('#paket_id').val();
            const selected = paketData.find(p => p.id == paketId);
            if(selected && !manualOverride){
                updateTanggalBerakhir(selected.masa_pembayaran);
            }
        }
    });

    // Flatpickr tanggal berakhir
    flatpickr(tanggalBerakhir, {
        dateFormat: 'Y-m-d',
        defaultDate: tanggalBerakhir.value,
        allowInput: true,
        onChange: function(selectedDates, dateStr){
            manualOverride = true;
        }
    });

    // Format tanggal
    const formatDate = (date) => {
        const d = new Date(date);
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${d.getFullYear()}-${month}-${day}`;
    };

    // Update tanggal berakhir otomatis
    function updateTanggalBerakhir(masaHari) {
        if(!masaHari) return tanggalBerakhir._flatpickr.clear();
        const start = new Date(tanggalMulai.value);
        start.setDate(start.getDate() + parseInt(masaHari));
        tanggalBerakhir._flatpickr.setDate(formatDate(start));
    }

    // Tampilkan harga & masa aktif paket saat load page
    const initialPaketId = "{{ old('paket_id', $pelanggan->paket_id) }}";
    const initialPaket = paketData.find(p => p.id == initialPaketId);
    if(initialPaket){
        hargaDisplay.textContent = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(initialPaket.harga);
        masaDisplay.textContent = `${initialPaket.masa_pembayaran} Hari`;
        $('#paket_id').val(initialPaket.id).trigger('change');
    }

    // ? EVENT PILIH PAKET (GUNAKAN SELECT2 EVENT)
    $('#paket_id').on('select2:select', function (e) {
        const paketId = e.params.data.id;
        const selected = paketData.find(p => p.id == paketId);

        if(selected){
            hargaDisplay.textContent = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(selected.harga);
            masaDisplay.textContent = `${selected.masa_pembayaran} Hari`;

            if(!manualOverride){
                updateTanggalBerakhir(selected.masa_pembayaran);
            }
        }
    });

    // ? HANDLE CLEAR SELECT2
    $('#paket_id').on('select2:clear', function (e) {
        hargaDisplay.textContent = '-';
        masaDisplay.textContent = '-';
        if(!manualOverride) tanggalBerakhir._flatpickr.clear();
    });

                    // Preview foto KTP
    const fotoInput = document.getElementById('foto_ktp');
    const preview = document.getElementById('preview_ktp');
    fotoInput.addEventListener('change', function() {
        const file = this.files[0];
        if(!file){
            preview.style.display = 'none';
            return;
        }
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });

    const statusSelect = document.getElementById('status');
    const progressNoteWrapper = document.getElementById('progress_note_wrapper');
    const progressNoteInput = document.getElementById('progress_note');

    const toggleProgressNote = () => {
        const isProgress = statusSelect && statusSelect.value === 'pending';
        if (!progressNoteWrapper) return;
        progressNoteWrapper.style.display = isProgress ? 'block' : 'none';
        if (progressNoteInput) {
            progressNoteInput.required = Boolean(isProgress);
        }
    };

    if (statusSelect) {
        statusSelect.addEventListener('change', toggleProgressNote);
        toggleProgressNote();
    }

});
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="app-pelanggan-edit">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div>
                    <h4>
                        <i class="ri-edit-box-line me-2"></i>Edit Pelanggan
                    </h4>
                    <p class="text-muted mb-0">Perbarui data pelanggan dengan lengkap dan benar</p>
                </div>
                <div class="d-flex gap-2 mt-3 mt-md-0">
                    <a href="{{ route('pelanggan') }}" class="btn btn-label-secondary btn-cancel">
                        <i class="ri-close-line me-1"></i>Batal
                    </a>
                    <button type="submit" form="form-pelanggan" class="btn btn-primary btn-save">
                        <i class="ri-save-line me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form id="form-pelanggan" action="{{ route('pelanggan.update', $pelanggan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Informasi Pelanggan -->
            <div class="card form-card mb-4">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="ri-user-line"></i>
                        Informasi Pelanggan
                    </h5>
                </div>
                <div class="card-body p-4">

                    <!-- Nama Lengkap -->
                    <div class="mb-4">
                        <label for="nama_lengkap" class="form-label">
                            <i class="ri-user-3-line"></i>Nama Lengkap
                        </label>
                        <input
                            type="text"
                            class="form-control @error('nama_lengkap') is-invalid @enderror"
                            id="nama_lengkap"
                            name="nama_lengkap"
                            placeholder="Masukkan nama lengkap pelanggan"
                            value="{{ old('nama_lengkap', $pelanggan->nama_lengkap) }}"
                            required>
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- No KTP, WhatsApp, Telepon -->
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label for="no_whatsapp" class="form-label">
                                <i class="ri-whatsapp-line"></i>Nomor WhatsApp
                            </label>
                            <input
                                type="text"
                                class="form-control @error('no_whatsapp') is-invalid @enderror"
                                id="no_whatsapp"
                                name="no_whatsapp"
                                placeholder="08xxxxxxxxxx"
                                value="{{ old('no_whatsapp', $pelanggan->no_whatsapp) }}">
                            @error('no_whatsapp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-4">
                            <label for="no_telp" class="form-label">
                                <i class="ri-phone-line"></i>Nomor Telepon
                            </label>
                            <input
                                type="text"
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

                    <!-- Section Header Alamat -->
                    <div class="section-header">
                        <i class="ri-map-pin-line"></i>
                        Alamat Lengkap
                    </div>

                    <!-- Jalan -->
                    <div class="mb-4">
                        <label for="alamat_jalan" class="form-label">
                            <i class="ri-road-map-line"></i>Alamat Jalan
                        </label>
                        <input
                            type="text"
                            class="form-control @error('alamat_jalan') is-invalid @enderror"
                            id="alamat_jalan"
                            name="alamat_jalan"
                            placeholder="Contoh: Jl. Merdeka No. 123"
                            value="{{ old('alamat_jalan', $pelanggan->alamat_jalan) }}">
                        @error('alamat_jalan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- RT, RW, Kode Pos -->
                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <label for="rt" class="form-label">
                                <i class="ri-community-line"></i>RT
                            </label>
                            <input
                                type="text"
                                class="form-control @error('rt') is-invalid @enderror"
                                id="rt"
                                name="rt"
                                placeholder="001"
                                value="{{ old('rt', $pelanggan->rt) }}">
                            @error('rt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-4">
                            <label for="rw" class="form-label">
                                <i class="ri-community-line"></i>RW
                            </label>
                            <input
                                type="text"
                                class="form-control @error('rw') is-invalid @enderror"
                                id="rw"
                                name="rw"
                                placeholder="001"
                                value="{{ old('rw', $pelanggan->rw) }}">
                            @error('rw')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="kode_pos" class="form-label">
                                <i class="ri-mail-line"></i>Kode Pos
                            </label>
                            <input
                                type="text"
                                class="form-control @error('kode_pos') is-invalid @enderror"
                                id="kode_pos"
                                name="kode_pos"
                                placeholder="12345"
                                value="{{ old('kode_pos', $pelanggan->kode_pos) }}">
                            @error('kode_pos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Desa, Kecamatan, Kabupaten, Provinsi -->
                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <label for="desa" class="form-label">
                                <i class="ri-home-3-line"></i>Desa / Kelurahan
                            </label>
                            <input
                                type="text"
                                class="form-control @error('desa') is-invalid @enderror"
                                id="desa"
                                name="desa"
                                placeholder="Nama desa"
                                value="{{ old('desa', $pelanggan->desa) }}">
                            @error('desa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-4">
                            <label for="kecamatan" class="form-label">
                                <i class="ri-building-line"></i>Kecamatan
                            </label>
                            <input
                                type="text"
                                class="form-control @error('kecamatan') is-invalid @enderror"
                                id="kecamatan"
                                name="kecamatan"
                                placeholder="Nama kecamatan"
                                value="{{ old('kecamatan', $pelanggan->kecamatan) }}">
                            @error('kecamatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-4">
                            <label for="kabupaten" class="form-label">
                                <i class="ri-map-2-line"></i>Kabupaten / Kota
                            </label>
                            <input
                                type="text"
                                class="form-control @error('kabupaten') is-invalid @enderror"
                                id="kabupaten"
                                name="kabupaten"
                                placeholder="Nama kabupaten"
                                value="{{ old('kabupaten', $pelanggan->kabupaten) }}">
                            @error('kabupaten')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-4">
                            <label for="provinsi" class="form-label">
                                <i class="ri-global-line"></i>Provinsi
                            </label>
                            <input
                                type="text"
                                class="form-control @error('provinsi') is-invalid @enderror"
                                id="provinsi"
                                name="provinsi"
                                placeholder="Nama provinsi"
                                value="{{ old('provinsi', $pelanggan->provinsi) }}">
                            @error('provinsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Upload Foto KTP -->
                    <div class="mb-0">
                        <label for="foto_ktp" class="form-label">
                            <i class="ri-image-line"></i>Upload Foto KTP
                        </label>
                        <input
                            type="file"
                            class="form-control @error('foto_ktp') is-invalid @enderror"
                            id="foto_ktp"
                            name="foto_ktp"
                            accept="image/*">
                        <small class="form-text-muted">
                            <i class="ri-information-line me-1"></i>Format: JPG, PNG. Maksimal ukuran: 2MB
                        </small>
                        @error('foto_ktp')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                        @if($pelanggan->foto_ktp)
                            <img id="preview_ktp" src="{{ asset('storage/' . $pelanggan->foto_ktp) }}" alt="Foto KTP" class="preview-image">
                        @else
                            <img id="preview_ktp" src="#" alt="Preview Foto KTP" class="preview-image" style="display:none;">
                        @endif
                    </div>

                </div>
            </div>

            <!-- Paket Internet -->
            <div class="card form-card mb-4">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="ri-wifi-line"></i>
                        Paket Internet
                    </h5>
                </div>
                <div class="card-body p-4">

                    <!-- Nomor ID -->
                    <div class="mb-4">
                        <label for="nomer_id" class="form-label">
                            <i class="ri-barcode-line"></i>Nomor ID Pelanggan
                        </label>
                        <input
                            type="text"
                            class="form-control @error('nomer_id') is-invalid @enderror"
                            id="nomer_id"
                            name="nomer_id"
                            placeholder="Contoh: PLG001"
                            value="{{ old('nomer_id', $pelanggan->nomer_id) }}"
                            required>
                        <small class="form-text-muted">
                            <i class="ri-information-line me-1"></i>ID unik untuk pelanggan ini
                        </small>
                        @error('nomer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- ? PILIH PAKET DENGAN SELECT2 SEARCH -->
                    <div class="mb-4">
                        <label for="paket_id" class="form-label">
                            <i class="ri-price-tag-3-line"></i>Pilih Paket Internet
                        </label>
                        <select
                            class="form-select @error('paket_id') is-invalid @enderror"
                            id="paket_id"
                            name="paket_id"
                            required>
                            <option value="">-- Pilih Paket Internet --</option>
                            @foreach($paket as $p)
                                <option value="{{ $p->id }}" {{ $pelanggan->paket_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_paket }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text-muted">
                            <i class="ri-search-line me-1"></i>Ketik nama paket atau kecepatan untuk mencari
                        </small>
                        @error('paket_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Harga & Masa Aktif -->
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-money-dollar-circle-line"></i>Harga Paket
                            </label>
                            <div class="display-field" id="harga_display">-</div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-time-line"></i>Masa Aktif
                            </label>
                            <div class="display-field" id="masa_display">-</div>
                        </div>
                    </div>

                    <!-- Tanggal Aktif & Berakhir -->
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="tanggal_mulai" class="form-label">
                                <i class="ri-calendar-line"></i>Tanggal Mulai Aktif
                            </label>
                            <input
                                type="text"
                                class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                id="tanggal_mulai"
                                name="tanggal_mulai"
                                required>
                            @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="tanggal_berakhir" class="form-label">
                                <i class="ri-calendar-close-line"></i>Tanggal Berakhir
                            </label>
                            <input
                                type="text"
                                class="form-control @error('tanggal_berakhir') is-invalid @enderror"
                                id="tanggal_berakhir"
                                name="tanggal_berakhir"
                                required>
                            <small class="form-text-muted">
                                <i class="ri-information-line me-1"></i>Otomatis terisi sesuai masa aktif paket
                            </small>
                            @error('tanggal_berakhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

            <!-- Status Pelanggan -->
            <div class="card form-card mb-4">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="ri-shield-check-line"></i>
                        Status Pelanggan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-0">
                        <label for="status" class="form-label">
                            <i class="ri-checkbox-circle-line"></i>Status Persetujuan
                        </label>
                        <select
                            class="form-select @error('status') is-invalid @enderror"
                            id="status"
                            name="status"
                            required>
                            <option value="pending" {{ $pelanggan->status == 'pending' ? 'selected' : '' }}>
                                Progress - Menunggu Persetujuan
                            </option>
                            <option value="approve" {{ $pelanggan->status == 'approve' ? 'selected' : '' }}>
                                Approve - Disetujui
                            </option>
                            <option value="reject" {{ $pelanggan->status == 'reject' ? 'selected' : '' }}>
                                Reject - Ditolak
                            </option>
                        </select>
                        <div id="progress_note_wrapper" class="mt-3">
                            <label for="progress_note" class="form-label">
                                <i class="ri-sticky-note-line"></i>Catatan Progress
                            </label>
                            <textarea
                                class="form-control @error('progress_note') is-invalid @enderror"
                                id="progress_note"
                                name="progress_note"
                                rows="3"
                                placeholder="Contoh: Menunggu verifikasi dokumen KTP dan konfirmasi alamat pelanggan.">{{ old('progress_note', $pelanggan->progress_note ?? '') }}</textarea>
                            @error('progress_note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text-muted">
                            <i class="ri-information-line me-1"></i>Pilih status pelanggan sesuai keputusan admin
                        </small>
                        <div class="alert alert-info mt-2 mb-0 py-2 px-3">
                            <div class="small">
                                <strong>Catatan status:</strong><br>
                                <strong>Progress</strong>: Data pelanggan sedang diproses/verifikasi admin.<br>
                                <strong>Approve</strong>: Data disetujui dan pelanggan bisa lanjut layanan.<br>
                                <strong>Reject</strong>: Data ditolak, perlu perbaikan data pelanggan.
                            </div>
                        </div>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons (Mobile) -->
            <div class="d-md-none">
                <div class="d-flex gap-2 mb-4">
                    <a href="{{ route('pelanggan') }}" class="btn btn-label-secondary btn-cancel flex-fill">
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
