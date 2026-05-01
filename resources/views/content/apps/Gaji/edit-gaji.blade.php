@extends('layouts/layoutMaster')

@section('title', 'Edit Gaji Karyawan')

{{-- VENDOR STYLE --}}
@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss'])
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
@vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

{{-- PAGE SCRIPT --}}
@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {

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
    
    let selectedEmployee = '{{ old('employee_id', $gaji->employee_id) }}';
    if(selectedEmployee) $('#employee_id').val(selectedEmployee).trigger('change');

    function formatRupiah(angka){ angka=parseInt(angka)||0; return 'Rp. '+angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g,"."); }
    function toNumber(rupiah){ return parseInt(rupiah.toString().replace(/[^0-9]/g,''))||0; }

    const rupiahInputs = document.querySelectorAll('.rupiah');
    rupiahInputs.forEach(input=>{
        const hidden = input.parentElement.querySelector('input[type="hidden"]');
        if(hidden) input.value = formatRupiah(hidden.value);
        input.addEventListener('input', function(){
            const val=toNumber(this.value);
            if(hidden) hidden.value=val;
            this.value = formatRupiah(val);
        });
    });

    // === TUNJANGAN DINAMIS ===
    const tunjContainer = document.getElementById('tunjangan-container');
    let oldTunj = @json(old('tunj_dynamic', $gaji->tunj_dynamic ?? [])) || [];
    let oldKet  = @json(old('tunj_keterangan', $gaji->tunj_keterangan ?? [])) || [];

    // Load tunjangan lama
    if(Array.isArray(oldTunj) && oldTunj.length){
        oldTunj.forEach((val, idx) => {
            addTunjangan(val, oldKet[idx] ?? '');
        });
    }

    // Tombol tambah tunjangan baru
    document.getElementById('add-tunjangan-btn').addEventListener('click', function(){
        addTunjangan('','');
    });

    function addTunjangan(value='', ket=''){
        const index = tunjContainer.querySelectorAll('.tunjangan-item').length+1;
        const div=document.createElement('div');
        div.classList.add('tunjangan-item');
        div.innerHTML=`
            <div class="row">
                <div class="col-md-5 mb-2">
                    <label class="form-label small"><i class="ri-money-dollar-circle-line"></i>Nominal</label>
                    <input type="text" class="form-control rupiah" placeholder="Rp. 0" value="">
                    <input type="hidden" name="tunj_dynamic[]" class="tunj_dynamic_hidden" value="${value}">
                </div>
                <div class="col-md-5 mb-2">
                    <label class="form-label small"><i class="ri-file-text-line"></i>Keterangan</label>
                    <input type="text" class="form-control" name="tunj_keterangan[]" placeholder="Contoh: Tunjangan Transportasi" value="${ket}">
                </div>
                <div class="col-md-2 mb-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger w-100 remove-tunj" title="Hapus">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </div>`;
        tunjContainer.appendChild(div);

        const inputRupiah = div.querySelector('.rupiah');
        const inputHidden = div.querySelector('.tunj_dynamic_hidden');

        // Set value awal jika ada data lama
        if(value) inputRupiah.value = formatRupiah(value);

        // Sync input ke hidden
        inputRupiah.addEventListener('input', function(){
            const val = toNumber(this.value);
            inputHidden.value = val;
            this.value = formatRupiah(val);
        });

        div.querySelector('.remove-tunj').addEventListener('click', function(){
            div.remove();
        });
    }

    // Pastikan semua hidden diupdate sebelum submit
    document.querySelector('form').addEventListener('submit', function(){
        tunjContainer.querySelectorAll('.tunjangan-item').forEach(item=>{
            const rupiah = item.querySelector('.rupiah');
            const hidden = item.querySelector('.tunj_dynamic_hidden');
            hidden.value = toNumber(rupiah.value);
        });

        // Update gaji & potongan
        rupiahInputs.forEach(input=>{
            const hidden = input.parentElement.querySelector('input[type="hidden"]');
            if(hidden) hidden.value = toNumber(input.value);
        });
    });

});
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="app-gaji-edit">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div>
                    <h4>
                        <i class="ri-edit-box-line me-2"></i>Edit Data Gaji
                    </h4>
                    <p class="text-muted mb-0">Perbarui data gaji karyawan dengan lengkap dan benar</p>
                </div>
                <div class="d-flex gap-2 mt-3 mt-md-0">
                    <a href="{{ route('gaji.index') }}" class="btn btn-label-secondary btn-cancel">
                        <i class="ri-close-line me-1"></i>Batal
                    </a>
                    <button type="submit" form="form-gaji" class="btn btn-primary btn-save">
                        <i class="ri-save-line me-1"></i>Update Gaji
                    </button>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form id="form-gaji" action="{{ route('gaji.update', $gaji->id) }}" method="POST">
            @csrf
            @method('PUT')

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
                                <option value="{{ $employee->id }}" {{ $gaji->employee_id==$employee->id?'selected':'' }}>
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
                            <input type="text" class="form-control rupiah" value="{{ $gaji->gaji_pokok }}" placeholder="Rp. 0">
                            <input type="hidden" name="gaji_pokok" value="{{ $gaji->gaji_pokok }}">
                        </div>

                        <!-- Tunj Jabatan -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-briefcase-line"></i>Tunjangan Jabatan
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ $gaji->tunj_jabatan }}" placeholder="Rp. 0">
                            <input type="hidden" name="tunj_jabatan" value="{{ $gaji->tunj_jabatan }}">
                        </div>

                        <!-- Tunj Fungsional -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-star-line"></i>Tunjangan Fungsional
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ $gaji->tunj_fungsional }}" placeholder="Rp. 0">
                            <input type="hidden" name="tunj_fungsional" value="{{ $gaji->tunj_fungsional }}">
                        </div>

                        <!-- Transport -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-bus-line"></i>Tunjangan Transport
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ $gaji->transport }}" placeholder="Rp. 0">
                            <input type="hidden" name="transport" value="{{ $gaji->transport }}">
                        </div>

                        <!-- Makan -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-restaurant-line"></i>Tunjangan Makan
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ $gaji->makan }}" placeholder="Rp. 0">
                            <input type="hidden" name="makan" value="{{ $gaji->makan }}">
                        </div>

                        <!-- Tunj Kehadiran -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-calendar-check-line"></i>Tunjangan Kehadiran
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ $gaji->tunj_kehadiran }}" placeholder="Rp. 0">
                            <input type="hidden" name="tunj_kehadiran" value="{{ $gaji->tunj_kehadiran }}">
                        </div>

                        <!-- Lembur -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-time-line"></i>Lembur
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ $gaji->lembur }}" placeholder="Rp. 0">
                            <input type="hidden" name="lembur" value="{{ $gaji->lembur }}">
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
                            <input type="text" class="form-control rupiah" value="{{ $gaji->pot_sosial }}" placeholder="Rp. 0">
                            <input type="hidden" name="pot_sosial" value="{{ $gaji->pot_sosial }}">
                        </div>

                        <!-- Potongan Denda -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-error-warning-line"></i>Potongan Denda
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ $gaji->pot_denda }}" placeholder="Rp. 0">
                            <input type="hidden" name="pot_denda" value="{{ $gaji->pot_denda }}">
                        </div>

                        <!-- Potongan Koperasi -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-store-line"></i>Potongan Koperasi
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ $gaji->pot_koperasi }}" placeholder="Rp. 0">
                            <input type="hidden" name="pot_koperasi" value="{{ $gaji->pot_koperasi }}">
                        </div>

                        <!-- Potongan Pajak -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                <i class="ri-file-list-line"></i>Potongan Pajak
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ $gaji->pot_pajak }}" placeholder="Rp. 0">
                            <input type="hidden" name="pot_pajak" value="{{ $gaji->pot_pajak }}">
                        </div>

                        <!-- Potongan Lainnya -->
                        <div class="col-md-6 mb-0">
                            <label class="form-label">
                                <i class="ri-more-line"></i>Potongan Lainnya
                            </label>
                            <input type="text" class="form-control rupiah" value="{{ $gaji->pot_lain }}" placeholder="Rp. 0">
                            <input type="hidden" name="pot_lain" value="{{ $gaji->pot_lain }}">
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
                        <i class="ri-save-line me-1"></i>Update
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection
