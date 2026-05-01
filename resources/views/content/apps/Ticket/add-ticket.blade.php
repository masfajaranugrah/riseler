@extends('layouts/layoutMaster')

@section('title', 'Tambah Ticket Baru')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.scss',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
<style>
/* ========================================= */
/* SHADCN UI – ADD TICKET (OPTIMIZED)        */
/* ========================================= */
:root {
  --primary: #18181b;
  --gray-bg: #fafafa;
  --gray-border: #e4e4e7;
  --gray-muted: #71717a;
  --radius: 10px;
  --radius-sm: 8px;
  --shadow-card: 0 2px 8px rgba(0,0,0,0.06);
}

body { background: #f5f5f9; }

/* Card */
.card {
  border: 1px solid var(--gray-border) !important;
  border-radius: var(--radius) !important;
  box-shadow: var(--shadow-card) !important;
  background: #fff;
  transition: box-shadow 0.2s ease;
}
.card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08) !important; }

.card-header-custom {
  background: #fff !important;
  border-bottom: 1px solid var(--gray-border);
  padding: 1.1rem 1.5rem;
  border-radius: var(--radius) var(--radius) 0 0;
}
.card-header-custom h5 {
  font-weight: 700;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--primary);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0;
}

/* Page Title */
.page-title h4 { font-size: 1.4rem; font-weight: 700; color: var(--primary); }
.page-title p  { color: var(--gray-muted); font-size: 0.85rem; }

/* Buttons */
.btn {
  border-radius: var(--radius-sm) !important;
  font-weight: 500 !important;
  font-size: 0.85rem !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 0.4rem !important;
  transition: all 0.2s ease !important;
}
.btn-primary {
  background: var(--primary) !important;
  color: #fafafa !important;
  border: 1px solid var(--primary) !important;
  box-shadow: 0 4px 12px rgba(24,24,27,0.25) !important;
}
.btn-primary:hover { background: #27272a !important; transform: translateY(-1px); }
.btn-outline-secondary {
  background: transparent !important;
  border: 1px solid var(--gray-border) !important;
  color: var(--primary) !important;
}
.btn-outline-secondary:hover {
  background: var(--primary) !important;
  color: #fff !important;
  border-color: var(--primary) !important;
}

/* Form Controls */
.form-label { font-size: 0.82rem; font-weight: 600; color: var(--primary); margin-bottom: 0.35rem; }
.form-control, .form-select {
  border: 1px solid var(--gray-border) !important;
  border-radius: var(--radius-sm) !important;
  font-size: 0.85rem !important;
  padding: 0.55rem 0.85rem !important;
  background: var(--gray-bg) !important;
  color: var(--primary) !important;
  transition: all 0.15s ease !important;
}
.form-control:focus, .form-select:focus {
  border-color: var(--primary) !important;
  background: #fff !important;
  box-shadow: 0 0 0 3px rgba(24,24,27,0.08) !important;
}
.form-control[readonly] {
  background: #f4f4f5 !important;
  color: var(--gray-muted) !important;
  cursor: default;
}
textarea.form-control { resize: vertical; }

/* Select2 */
.select2-container--default .select2-selection--single {
  border: 1px solid var(--gray-border) !important;
  border-radius: var(--radius-sm) !important;
  height: 38px !important;
  background: var(--gray-bg) !important;
  display: flex;
  align-items: center;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
  color: var(--primary) !important;
  font-size: 0.85rem !important;
  line-height: 38px !important;
  padding-left: 0.85rem !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px !important; }
.select2-container--default.select2-container--focus .select2-selection--single {
  border-color: var(--primary) !important;
  box-shadow: 0 0 0 3px rgba(24,24,27,0.08) !important;
}
.select2-dropdown {
  border: 1px solid var(--gray-border) !important;
  border-radius: var(--radius-sm) !important;
  box-shadow: 0 4px 16px rgba(0,0,0,0.1) !important;
  font-size: 0.85rem !important;
}
.select2-results__option--highlighted { background: var(--primary) !important; }
.select2-search__field { border: 1px solid var(--gray-border) !important; border-radius: 6px !important; font-size: 0.85rem !important; }

/* Toggle Type Tabs */
.type-toggle {
  display: inline-flex;
  border: 1px solid var(--gray-border);
  border-radius: var(--radius);
  overflow: hidden;
  background: #fff;
}
.type-toggle-btn {
  padding: 0.5rem 1.1rem;
  font-size: 0.82rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  transition: all 0.2s ease;
  background: #fff;
  color: var(--gray-muted);
}
.type-toggle-btn.active {
  background: var(--primary);
  color: #fff;
}
.type-toggle-btn:not(.active):hover {
  background: #f4f4f5;
  color: var(--primary);
}

/* Autofill highlight */
.autofill-highlight { border-left: 3px solid var(--primary) !important; }

/* Info badge */
.info-badge {
  background: #f4f4f5;
  border: 1px solid var(--gray-border);
  border-radius: var(--radius-sm);
  padding: 0.85rem 1rem;
  font-size: 0.82rem;
  color: #52525b;
}
.info-badge i { color: var(--primary); }

hr { border-color: var(--gray-border); }
</style>
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function () {

    // ─── Select2 ───
    $('#pelangganSelect').select2({
        placeholder: 'Cari nama atau ID pelanggan...',
        allowClear: true,
        width: '100%'
    });

    // ─── Toggle Tipe Tiket ───
    function switchTicketType(type) {
        $('#ticket_type').val(type);

        // Toggle active class
        $('.type-toggle-btn').removeClass('active');
        if (type === 'internal') {
            $('#btn-type-internal').addClass('active');
        } else {
            $('#btn-type-customer').addClass('active');
        }

        if (type === 'internal') {
            // Hide customer, Show internal
            $('#section-pelanggan').hide();
            $('#section-internal').show();
            // Disable customer fields
            $('#pelangganSelect').prop('required', false);
            $('#pelanggan_id').prop('required', false);
            $('#issue_description').prop('required', false).prop('disabled', true);
            $('#category').prop('disabled', true);
            $('#location_link').prop('disabled', true);
            $('#additional_note').prop('disabled', true);
            // Enable internal fields
            $('#title').prop('required', true).prop('disabled', false);
            $('#category-internal').prop('required', true).prop('disabled', false);
            $('#issue_description_internal').prop('required', true).prop('disabled', false);
            $('#location_link_internal').prop('disabled', false);
            $('#additional_note_internal').prop('disabled', false);
        } else {
            // Hide internal, Show customer
            $('#section-internal').hide();
            $('#section-pelanggan').show();
            // Enable customer fields
            $('#pelangganSelect').prop('required', true);
            $('#pelanggan_id').prop('required', true);
            $('#issue_description').prop('required', true).prop('disabled', false);
            $('#category').prop('disabled', false);
            $('#location_link').prop('disabled', false);
            $('#additional_note').prop('disabled', false);
            // Disable internal fields
            $('#title').prop('required', false).prop('disabled', true);
            $('#category-internal').prop('required', false).prop('disabled', true);
            $('#issue_description_internal').prop('required', false).prop('disabled', true);
            $('#location_link_internal').prop('disabled', true);
            $('#additional_note_internal').prop('disabled', true);
        }
    }

    $('#btn-type-customer').on('click', function() { switchTicketType('customer'); });
    $('#btn-type-internal').on('click', function() { switchTicketType('internal'); });

    // ─── Auto-fill saat pelanggan dipilih ───
    $('#pelangganSelect').on('change', function () {
        const selected = $(this).find('option:selected');
        const val = $(this).val();

        if (!val) { resetCustomerForm(); return; }

        $('#pelanggan_id').val(val);
        $('#phone').val(selected.attr('data-nowhatsapp') || '').addClass('autofill-highlight');
        $('#nama_pelanggan').val(selected.attr('data-nama') || '').addClass('autofill-highlight');
        $('#paket_pelanggan').val(selected.attr('data-paket') || '').addClass('autofill-highlight');

        // Alamat
        const parts = [];
        const jalan = selected.attr('data-jalan') || '';
        const rt = selected.attr('data-rt') || '';
        const rw = selected.attr('data-rw') || '';
        const desa = selected.attr('data-desa') || '';
        const kec = selected.attr('data-kecamatan') || '';

        if (jalan) parts.push(jalan);
        if (rt && rw) parts.push('RT ' + rt + '/RW ' + rw);
        else if (rt) parts.push('RT ' + rt);
        if (desa) parts.push(desa);
        if (kec) parts.push(kec);

        $('#alamat_pelanggan').val(parts.length > 0 ? parts.join(', ') : '').addClass('autofill-highlight');
    });

    function resetCustomerForm() {
        $('#pelanggan_id').val('');
        $('#phone, #nama_pelanggan, #alamat_pelanggan, #paket_pelanggan')
            .val('').removeClass('autofill-highlight');
    }

    // ─── Validasi submit ───
    $('form').on('submit', function(e) {
        const type = $('#ticket_type').val();

        if (type === 'customer') {
            const pelId = $('#pelanggan_id').val();
            if (!pelId) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Pelanggan',
                    text: 'Harap pilih pelanggan terlebih dahulu.',
                    confirmButtonColor: '#18181b'
                });
                return false;
            }
        } else {
            const title = $('#title').val();
            if (!title || !title.trim()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Judul Penugasan',
                    text: 'Harap isi judul penugasan internal.',
                    confirmButtonColor: '#18181b'
                });
                return false;
            }
        }
    });
});
</script>
@endsection

@section('content')
<form action="{{ route('tickets.stores') }}" method="POST" enctype="multipart/form-data">
@csrf
<input type="hidden" id="ticket_type" name="ticket_type" value="customer">

{{-- ===== PAGE HEADER ===== --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div class="page-title">
        <h4 class="mb-1"><i class="ri-ticket-2-line me-2"></i>Tambah Ticket Baru</h4>
        <p class="mb-0">Isi data tiket dengan lengkap agar teknisi mudah menindaklanjuti.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap align-items-center">
        {{-- Toggle --}}
        <div class="type-toggle">
            <button type="button" id="btn-type-customer" class="type-toggle-btn active">
                <i class="ri-user-3-line"></i> Pelanggan
            </button>
            <button type="button" id="btn-type-internal" class="type-toggle-btn">
                <i class="ri-tools-line"></i> Internal
            </button>
        </div>
    </div>
</div>

{{-- Validation Errors --}}
@if($errors->any())
<div class="alert alert-danger mb-4" style="border-radius:10px; border:1px solid #fecaca; background:#fef2f2;">
    <strong><i class="ri-error-warning-line me-2"></i>Terdapat kesalahan:</strong>
    <ul class="mb-0 mt-2 ps-3">
        @foreach($errors->all() as $error)
            <li style="font-size:0.85rem;">{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- ===== GRID LAYOUT ===== --}}
<div class="row g-4">

    {{-- ===== KIRI ===== --}}
    <div class="col-12 col-lg-8">

        {{-- ══════════════════════════════════ --}}
        {{-- SECTION: TIKET PELANGGAN          --}}
        {{-- ══════════════════════════════════ --}}
        <div id="section-pelanggan">

            {{-- Card: Informasi Pelanggan --}}
            <div class="card mb-4">
                <div class="card-header-custom">
                    <h5><i class="ri-user-3-line"></i> Informasi Pelanggan</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-label" for="pelangganSelect">
                            Pilih Pelanggan <span class="text-danger">*</span>
                        </label>
                        <select id="pelangganSelect" class="form-select" required>
                            <option value=""></option>
                            @foreach($pelanggan as $p)
                                <option
                                    value="{{ $p->id }}"
                                    data-nama="{{ $p->nama_lengkap }}"
                                    data-nowhatsapp="{{ $p->no_whatsapp ?? $p->no_telp }}"
                                    data-jalan="{{ $p->alamat_jalan }}"
                                    data-rt="{{ $p->rt }}"
                                    data-rw="{{ $p->rw }}"
                                    data-desa="{{ $p->desa }}"
                                    data-kecamatan="{{ $p->kecamatan }}"
                                    data-paket="{{ optional($p->paket)->nama_paket }}"
                                >
                                    {{ $p->nomer_id }} &mdash; {{ $p->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="pelanggan_id" id="pelanggan_id">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><i class="ri-user-line me-1"></i> Nama</label>
                            <input type="text" id="nama_pelanggan" class="form-control" placeholder="Otomatis terisi" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="ri-whatsapp-line me-1"></i> WhatsApp</label>
                            <input type="text" id="phone" name="phone" class="form-control" placeholder="Otomatis terisi">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label"><i class="ri-map-pin-line me-1"></i> Alamat</label>
                            <input type="text" id="alamat_pelanggan" class="form-control" placeholder="Otomatis terisi" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="ri-wifi-line me-1"></i> Paket</label>
                            <input type="text" id="paket_pelanggan" class="form-control" placeholder="Otomatis terisi" readonly>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Detail Kendala --}}
            <div class="card">
                <div class="card-header-custom">
                    <h5><i class="ri-bug-line"></i> Detail Kendala</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="category" class="form-label">Kategori Kendala</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">-- Pilih Kategori --</option>
                                <option value="internet_down">Internet Down</option>
                                <option value="modem_error">Modem Error</option>
                                <option value="kabel_putus">Kabel Putus</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="location_link" class="form-label">Link Lokasi (Google Maps) <small class="text-muted fw-normal">opsional</small></label>
                            <input type="url" class="form-control" id="location_link" name="location_link"
                                placeholder="https://maps.app.goo.gl/...">
                        </div>
                        <div class="col-12">
                            <label for="issue_description" class="form-label">
                                Deskripsi Kendala <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="issue_description" name="issue_description"
                                rows="4" placeholder="Jelaskan detail masalah yang dialami pelanggan..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label for="additional_note" class="form-label">
                                Catatan Tambahan <small class="text-muted fw-normal">opsional</small>
                            </label>
                            <textarea class="form-control" id="additional_note" name="additional_note"
                                rows="2" placeholder="Informasi tambahan jika ada..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- end #section-pelanggan --}}

        {{-- ══════════════════════════════════ --}}
        {{-- SECTION: TUGAS INTERNAL           --}}
        {{-- ══════════════════════════════════ --}}
        <div id="section-internal" style="display:none;">

            <div class="card mb-4">
                <div class="card-header-custom">
                    <h5><i class="ri-briefcase-line"></i> Detail Penugasan Internal</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="title" class="form-label">
                                Judul Penugasan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="title" name="title" disabled
                                placeholder="Contoh: Pemasangan Tiang RT 05 Desa Banaran">
                        </div>
                        <div class="col-md-6">
                            <label for="category-internal" class="form-label">
                                Jenis Pekerjaan <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="category-internal" name="category" disabled>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="pemasangan_tiang">Pemasangan Tiang</option>
                                <option value="tarik_kabel">Tarik Kabel</option>
                                <option value="maintenance">Maintenance Jaringan</option>
                                <option value="perbaikan_infrastruktur">Perbaikan Infrastruktur</option>
                                <option value="survey_lokasi">Survey Lokasi Baru</option>
                                <option value="instalasi_baru">Instalasi Baru</option>
                                <option value="pencabutan">Pencabutan / Bongkar</option>
                                <option value="lainnya_internal">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="location_link_internal" class="form-label">
                                Link Lokasi <small class="text-muted fw-normal">opsional</small>
                            </label>
                            <input type="url" class="form-control" id="location_link_internal" name="location_link"
                                placeholder="https://maps.app.goo.gl/..." disabled>
                        </div>
                        <div class="col-12">
                            <label for="issue_description_internal" class="form-label">
                                Deskripsi Pekerjaan <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="issue_description_internal" name="issue_description"
                                rows="4" placeholder="Jelaskan detail pekerjaan yang perlu dilakukan..." disabled></textarea>
                        </div>
                        <div class="col-12">
                            <label for="additional_note_internal" class="form-label">
                                Catatan Tambahan <small class="text-muted fw-normal">opsional</small>
                            </label>
                            <textarea class="form-control" id="additional_note_internal" name="additional_note"
                                rows="2" placeholder="Info tambahan, material yang diperlukan, dsb..." disabled></textarea>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- end #section-internal --}}

    </div>{{-- end col-lg-8 --}}

    {{-- ===== KANAN: SIDEBAR ===== --}}
    <div class="col-12 col-lg-4">

        {{-- Card: Prioritas --}}
        <div class="card mb-4">
            <div class="card-header-custom">
                <h5><i class="ri-alarm-warning-line"></i> Prioritas</h5>
            </div>
            <div class="card-body p-4">
                <label class="form-label">Tingkat Urgensi <span class="text-danger">*</span></label>
                <select class="form-select" id="priority" name="priority" required>
                    <option value="medium" selected>Medium (Default)</option>
                    <option value="urgent">Urgent</option>
                    <option value="low">Low</option>
                </select>
            </div>
        </div>

        {{-- Card: Penugasan Teknisi --}}
        <div class="card mb-4">
            <div class="card-header-custom">
                <h5><i class="ri-tools-line"></i> Penugasan Teknisi</h5>
            </div>
            <div class="card-body p-4">
                <label for="user_id" class="form-label">
                    Pilih Teknisi <small class="text-muted fw-normal">opsional</small>
                </label>
                <select class="form-select" id="user_id" name="user_id">
                    <option value="">-- Assign nanti --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <small class="text-muted mt-2 d-block" style="font-size:0.78rem;">
                    Jika tidak dipilih, ticket berstatus <strong>Pending</strong>.
                    Bisa diassign nanti dari halaman edit.
                </small>
            </div>
        </div>

        {{-- Info Box --}}
        <div class="info-badge mb-4">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="ri-information-line fs-5"></i>
                <strong style="font-size:0.82rem;">Informasi</strong>
            </div>
            <p class="mb-0" style="font-size:0.8rem; line-height:1.5;">
                Ticket akan otomatis berstatus <strong>Pending</strong> dan menunggu assignment teknisi oleh admin.
                Jika langsung memilih teknisi, status otomatis menjadi <strong>Assigned</strong>.
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary" style="padding:0.8rem !important; font-size:0.9rem !important;">
                <i class="ri-save-line"></i> Simpan Ticket
            </button>
            <a href="{{ route('tickets.indexs') }}" class="btn btn-outline-secondary" style="padding:0.7rem !important;">
                <i class="ri-close-line"></i> Batal
            </a>
        </div>

    </div>{{-- end col-lg-4 --}}

</div>{{-- end row --}}
</form>

@endsection
