@extends('layouts/layoutMaster')

@section('title', 'Tambah Gaji')

{{-- VENDOR STYLE --}}
@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.scss',
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
  .section-header i { margin-right: 0.75rem; font-size: 1.25rem; }
  .form-text-muted { color: #a1a1aa; font-size: 0.8125rem; margin-top: 0.25rem; display: block; }
  .btn-add-tunjangan {
    border-radius: 8px;
    padding: 0.5rem 1.5rem;
    font-weight: 600;
    border: 2px dashed #18181b;
    color: #18181b;
    transition: all 0.3s;
  }
  .btn-add-tunjangan:hover { background: #18181b; color: #fafafa; border-style: solid; }
  .tunjangan-item {
    background: #f4f4f5;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 0.75rem;
    border: 1px solid #e4e4e7;
  }
  .btn-danger { background: #18181b !important; color: #fafafa !important; border: 1px solid #18181b !important; }
  .btn-danger:hover { background: #27272a !important; }
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
  .select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1.5px solid #e4e4e7 !important;
    border-radius: 8px !important;
    padding: 0.5rem 1rem !important;
  }
  .select2-container--default .select2-search--dropdown .select2-search__field:focus {
    border-color: #18181b !important;
    outline: none !important;
  }
</style>
@endsection

{{-- VENDOR SCRIPT --}}
@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js',
])
@endsection

{{-- PAGE SCRIPT --}}
@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // === Select2 Karyawan ===
    $('#employee_id').select2({
        placeholder: "-- Pilih Karyawan --",
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "Tidak ada karyawan ditemukan";
            },
            searching: function() {
                return "Mencari...";
            }
        }
    });

    var selectedEmployee = '{{ old('employee_id', $selectedEmployeeId ?? '') }}';
    if (selectedEmployee) {
        $('#employee_id').val(selectedEmployee).trigger('change');
    }

    // === FORMAT RUPIAH ===
    function formatRupiah(angka) {
        let number_string = angka.replace(/[^,\d]/g, "").toString(),
            split = number_string.split(","),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/g);
        if (ribuan) {
            rupiah += (sisa ? "." : "") + ribuan.join(".");
        }
        return "Rp. " + rupiah;
    }

    function toNumber(rupiah) {
        return rupiah.replace(/[^0-9]/g, "");
    }

    // === FORMAT EXISTING INPUT RUPIAH ===
    const rupiahInputs = document.querySelectorAll('.rupiah');
    rupiahInputs.forEach(input => {
        const target = input.parentElement.querySelector('input[type="hidden"]');
        input.addEventListener('input', function () {
            this.value = formatRupiah(this.value);
            if (target) target.value = toNumber(this.value);
        });
    });

    // === DYNAMIC TUNJANGAN DENGAN KETERANGAN ===
    const tunjContainer = document.getElementById('tunjangan-container');

    document.getElementById('add-tunjangan-btn').addEventListener('click', function () {
        const index = tunjContainer.querySelectorAll('.tunjangan-item').length + 1;

        const div = document.createElement('div');
        div.classList.add('tunjangan-item');

        div.innerHTML = `
            <div class="row">
                <div class="col-md-5 mb-2">
                    <label class="form-label small"><i class="ri-money-dollar-circle-line"></i>Nominal</label>
                    <input type="text" class="form-control rupiah" placeholder="Rp. 0">
                    <input type="hidden" name="tunj_dynamic[]" class="tunj_dynamic_hidden">
                </div>
                <div class="col-md-5 mb-2">
                    <label class="form-label small"><i class="ri-file-text-line"></i>Keterangan</label>
                    <input type="text" class="form-control" name="tunj_keterangan[]" placeholder="Contoh: Tunjangan Transportasi">
                </div>
                <div class="col-md-2 mb-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger w-100 remove-tunj" title="Hapus">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </div>
        `;

        tunjContainer.appendChild(div);

        const inputRupiah = div.querySelector('.rupiah');
        const inputHidden = div.querySelector('.tunj_dynamic_hidden');

        // Sync rupiah ke hidden
        inputRupiah.addEventListener('input', function () {
            this.value = formatRupiah(this.value);
            inputHidden.value = toNumber(this.value);
        });

        // Remove tunjangan
        div.querySelector('.remove-tunj').addEventListener('click', function () {
            div.remove();
        });
    });

    // === Pastikan semua hidden input terisi sebelum submit ===
    document.querySelector('form').addEventListener('submit', function(){
        tunjContainer.querySelectorAll('.tunjangan-item').forEach(item=>{
            const rupiah = item.querySelector('.rupiah');
            const hidden = item.querySelector('.tunj_dynamic_hidden');
            hidden.value = toNumber(rupiah.value);
        });
    });

});
</script>
@endsection

{{-- CONTENT --}}
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="app-gaji-add">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div>
                    <h4>
                        <i class="ri-money-dollar-circle-line me-2"></i>Tambah Data Gaji
                    </h4>
                    <p class="text-muted mb-0">Isi data gaji karyawan dengan lengkap dan benar</p>
                </div>
                <div class="d-flex gap-2 mt-3 mt-md-0">
                    <a href="{{ route('gaji.index') }}" class="btn btn-label-secondary btn-cancel">
                        <i class="ri-close-line me-1"></i>Batal
                    </a>
                    <button type="submit" form="form-gaji" class="btn btn-primary btn-save">
                        <i class="ri-save-line me-1"></i>Simpan Gaji
                    </button>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form id="form-gaji" action="{{ route('gaji.store') }}" method="POST">
            @csrf

            <!-- Informasi Karyawan -->
            <div class="card form-card mb-4">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="ri-user-line"></i>
                        Informasi Karyawan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-0">
                        <label for="employee_id" class="form-label">
                            <i class="ri-user-search-line"></i>Pilih Karyawan
                        </label>
                        <select name="employee_id" id="employee_id" class="form-select" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ old('employee_id') == $employee->id ? 'selected' : (isset($gaji) && $gaji->employee_id == $employee->id ? 'selected' : '') }}>
                                    {{ $employee->full_name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text-muted">
                            <i class="ri-information-line me-1"></i>Pilih karyawan yang akan diberikan gaji
                        </small>
                        @error('employee_id')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Gaji dan Tunjangan -->
            <div class="card form-card mb-4">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="ri-wallet-3-line"></i>
                        Gaji dan Tunjangan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Gaji Pokok -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-money-dollar-box-line"></i>Gaji Pokok
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ old('gaji_pokok') }}" placeholder="Rp. 0">
                            <input type="hidden" name="gaji_pokok" value="{{ old('gaji_pokok') }}">
                        </div>

                        <!-- Tunj Jabatan -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-briefcase-line"></i>Tunjangan Jabatan
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ old('tunj_jabatan') }}" placeholder="Rp. 0">
                            <input type="hidden" name="tunj_jabatan" value="{{ old('tunj_jabatan') }}">
                        </div>

                        <!-- Tunj Fungsional -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-star-line"></i>Tunjangan Fungsional
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ old('tunj_fungsional') }}" placeholder="Rp. 0">
                            <input type="hidden" name="tunj_fungsional" value="{{ old('tunj_fungsional') }}">
                        </div>

                        <!-- Transport -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-bus-line"></i>Tunjangan Transport
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ old('transport') }}" placeholder="Rp. 0">
                            <input type="hidden" name="transport" value="{{ old('transport') }}">
                        </div>

                        <!-- Makan -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-restaurant-line"></i>Tunjangan Makan
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ old('makan') }}" placeholder="Rp. 0">
                            <input type="hidden" name="makan" value="{{ old('makan') }}">
                        </div>

                        <!-- Tunj Kehadiran -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-calendar-check-line"></i>Tunjangan Kehadiran
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ old('tunj_kehadiran') }}" placeholder="Rp. 0">
                            <input type="hidden" name="tunj_kehadiran" value="{{ old('tunj_kehadiran') }}">
                        </div>

                        <!-- Lembur -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-time-line"></i>Lembur
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ old('lembur') }}" placeholder="Rp. 0">
                            <input type="hidden" name="lembur" value="{{ old('lembur') }}">
                        </div>
                    </div>

                    <!-- Section Tunjangan Tambahan -->
                    <div class="section-header mt-2">
                        <i class="ri-add-circle-line"></i>
                        Tunjangan Tambahan
                    </div>

                    <div id="tunjangan-container"></div>

                    <button type="button" class="btn btn-outline-primary btn-add-tunjangan" id="add-tunjangan-btn">
                        <i class="ri-add-line me-1"></i>Tambah Tunjangan
                    </button>
                </div>
            </div>

            <!-- Potongan -->
            <div class="card form-card mb-4">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <i class="ri-subtract-line"></i>
                        Potongan Gaji
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Potongan Sosial -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-heart-line"></i>Potongan Sosial
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ old('potongan_sosial') }}" placeholder="Rp. 0">
                            <input type="hidden" name="pot_sosial" value="{{ old('potongan_sosial') }}">
                        </div>

                        <!-- Potongan Denda -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-error-warning-line"></i>Potongan Denda
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ old('potongan_denda') }}" placeholder="Rp. 0">
                            <input type="hidden" name="pot_denda" value="{{ old('potongan_denda') }}">
                        </div>

                        <!-- Potongan Koperasi -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-store-line"></i>Potongan Koperasi
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ old('potongan_koperasi') }}" placeholder="Rp. 0">
                            <input type="hidden" name="pot_koperasi" value="{{ old('potongan_koperasi') }}">
                        </div>

                        <!-- Potongan Pajak -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-file-list-line"></i>Potongan Pajak
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ old('potongan_pajak') }}" placeholder="Rp. 0">
                            <input type="hidden" name="pot_pajak" value="{{ old('potongan_pajak') }}">
                        </div>

                        <!-- Potongan Lainnya -->
                        <div class="col-md-6 mb-0">
                            <label class="form-label">
                                <i class="ri-more-line"></i>Potongan Lainnya
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ old('potongan_lain') }}" placeholder="Rp. 0">
                            <input type="hidden" name="pot_lain" value="{{ old('potongan_lain') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons (Mobile) -->
            <div class="d-md-none">
                <div class="d-flex gap-2 mb-4">
                    <a href="{{ route('gaji.index') }}" class="btn btn-label-secondary btn-cancel flex-fill">
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
