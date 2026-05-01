@extends('layouts/layoutMaster')

@section('title', 'Laporan Kwitansi')

@section('vendor-style')
<style>
/* ========================================= */
/* MODERN CLEAN STYLES - SHADCN UI */
/* ========================================= */
:root {
  --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
  --card-hover-shadow: 0 4px 16px rgba(0,0,0,0.12);
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --primary-color: #111827;
}

/* Card Design */
.card {
  border: none;
  border-radius: var(--border-radius);
  box-shadow: var(--card-shadow);
  transition: var(--transition);
  overflow: hidden;
}

.card:hover {
  box-shadow: var(--card-hover-shadow);
}

.card-header {
  background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
  color: #18181b;
  border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
  padding: 1.5rem;
  border-bottom: 1px solid #f0f0f0;
}

.card-header h5 {
  font-weight: 700;
  color: #18181b;
}

/* ========================================= */
/* SHADCN UI STYLE BUTTONS - ALL BLACK */
/* ========================================= */
.btn {
  border-radius: 6px !important;
  padding: 0.5rem 1rem !important;
  font-weight: 500 !important;
  font-size: 0.875rem !important;
  transition: all 0.15s ease !important;
  cursor: pointer !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 0.5rem !important;
}

/* Primary Button - Black */
.btn.btn-primary,
.btn-primary {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-primary:hover,
.btn-primary:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

/* Success Button - Black */
.btn.btn-success,
.btn-success {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-success:hover,
.btn-success:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

/* Outline Buttons */
.btn.btn-outline-primary,
.btn.btn-outline-secondary,
.btn-outline-primary,
.btn-outline-secondary {
  background: transparent !important;
  background-color: transparent !important;
  border: 1px solid #e4e4e7 !important;
  color: #18181b !important;
}

.btn.btn-outline-primary:hover,
.btn.btn-outline-secondary:hover,
.btn-outline-primary:hover,
.btn-outline-secondary:hover {
  background: #f4f4f5 !important;
  background-color: #f4f4f5 !important;
  border-color: #a1a1aa !important;
  color: #18181b !important;
}

/* ========================================= */
/* SHADCN UI STYLE BADGES */
/* ========================================= */
.badge {
  padding: 0.25rem 0.625rem;
  border-radius: 9999px;
  font-weight: 500;
  font-size: 0.75rem;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
}

.bg-label-primary,
.bg-label-success,
.bg-label-warning,
.bg-label-dark {
  background: #f4f4f5 !important;
  color: #18181b !important;
  border: 1px solid #e4e4e7 !important;
}

.bg-label-info {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

.badge.bg-success {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

.badge.bg-danger {
  background: #dc2626 !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

/* ========================================= */
/* TABLE STYLES */
/* ========================================= */
.table-modern {
  border-radius: 8px;
  overflow: hidden;
}

.table-modern thead th,
.table-light th {
  background: #f8fafc !important;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  color: #0f172a;
  border: none;
  padding: 1rem;
  white-space: nowrap;
}

.table-modern tbody tr {
  transition: var(--transition);
  border-bottom: 1px solid #e5e7eb;
}

.table-modern tbody tr:hover {
  background-color: #f1f5f9 !important;
}

.table-modern tbody td {
  padding: 1rem;
  vertical-align: middle;
}

/* ========================================= */
/* FILTER SECTION */
/* ========================================= */
.card-body.border-top {
  background: #fafafa;
  border-top: 1px solid #e4e4e7 !important;
}

/* Form Controls */
.form-select, .form-control {
  border-radius: 8px;
  border: 1px solid #e4e4e7;
  padding: 0.625rem 1rem;
  transition: var(--transition);
}

.form-select:focus, .form-control:focus {
  border-color: #18181b;
  box-shadow: 0 0 0 3px rgba(24, 24, 27, 0.12);
}

/* ========================================= */
/* PAGINATION STYLES - BLACK ACTIVE */
/* ========================================= */
.pagination {
  margin: 0;
  gap: 0.5rem;
  justify-content: flex-end;
}

.pagination .page-item .page-link {
  border-radius: 50% !important;
  width: 40px;
  height: 40px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #e4e4e7;
  color: #18181b;
  font-weight: 600;
  background-color: #fff;
  margin: 0 4px;
  transition: all 0.3s ease;
}

.pagination .page-item .page-link:hover {
  background-color: #f4f4f5;
  border-color: #18181b;
  color: #18181b;
}

.pagination .page-item.active .page-link {
  background-color: #18181b !important;
  border-color: #18181b !important;
  color: #fafafa !important;
  box-shadow: none;
}

.pagination .page-item.disabled .page-link {
  background-color: #f4f4f5;
  border-color: #e4e4e7;
  color: #a1a1aa;
  cursor: not-allowed;
}

/* Hide default Laravel pagination results text */
/* Main wrapper hiding */
.d-flex.flex-wrap.align-items-center.justify-content-between.gap-2.mt-3 > .text-muted.small {
  display: none !important;
}

/* Hide 'Showing X to Y results' specifically from list views */
nav[role="navigation"] > div:first-child,
nav[role="navigation"] > div > p.text-sm,
nav[role="navigation"] > div > p,
nav[role="navigation"] p.text-sm,
nav[role="navigation"] > div.hidden,
.pagination-wrapper nav > div:first-child,
.pagination-wrapper nav > div > p,
.pagination-wrapper > div > nav > div:first-child,
.pagination-wrapper > div > nav > div:last-child > span.relative,
p.text-sm.text-gray-700,
p.leading-5,
.flex.justify-between.flex-1 > div:first-child,
div.flex.flex-col.items-center > p,
nav > div.flex-1.hidden,
nav > div > p {
  display: none !important;
}

/* Pagination Wrapper */
.pagination-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-top: 1px solid #f0f0f0;
  background: #fafafa;
  border-radius: 0 0 var(--border-radius) var(--border-radius);
}

.pagination-info {
  color: #71717a;
  font-size: 0.875rem;
  font-weight: 500;
}

/* Text Colors */
.text-primary {
  color: #18181b !important;
}

.text-muted {
  color: #71717a !important;
}

/* Avatar */
.avatar-initial {
  border-radius: 12px;
  transition: var(--transition);
}

.avatar-initial.bg-label-primary {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
}

/* Kwitansi Link */
a.text-decoration-none {
  color: #18181b;
  font-weight: 500;
  transition: all 0.2s ease;
}

a.text-decoration-none:hover {
  color: #27272a;
}

a.text-decoration-none i {
  color: #18181b !important;
}

/* Modal backdrop with blur effect */
/* Modal backdrop with blur effect */
.modal-backdrop.show {
  opacity: 1;
  background-color: rgba(24, 24, 27, 0.4);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

/* Detail Section */
.detail-section {
  background: #ffffff;
  border: 1px solid #e4e4e7;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  transition: all 0.2s;
}

.detail-section:first-child {
  margin-top: 1rem;
}

.detail-section:last-child {
  margin-bottom: 1rem;
}

.detail-section:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  border-color: #18181b;
}

.detail-section h6 {
  color: #18181b;
  font-weight: 700;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  display: flex;
  align-items: center;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #18181b;
}

.detail-section h6 i {
  margin-right: 0.5rem;
  font-size: 1.1rem;
}

.detail-item {
  display: flex;
  padding: 0.875rem 0;
  border-bottom: 1px solid #f0f0f0;
  align-items: flex-start;
}

.detail-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.detail-label {
  color: #5a5f7d;
  font-weight: 600;
  min-width: 180px;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
}

.detail-label i {
  margin-right: 0.5rem;
  color: #a8afc7;
  font-size: 1rem;
}

.detail-value {
  color: #2c3e50;
  font-size: 0.875rem;
  flex: 1;
  word-break: break-word;
}

/* Customer Header Info */
.customer-header-info {
  text-align: center;
  padding: 1.5rem;
  background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
  border-radius: 12px;
  margin-bottom: 1.5rem;
  border: 1px solid #e8e8e8;
}

.customer-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  background: linear-gradient(135deg, #18181b 0%, #27272a 100%);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 2.5rem;
  margin-bottom: 1rem;
  box-shadow: 0 4px 16px rgba(0,0,0,0.1);
  border: 4px solid white;
}

.customer-name {
  font-size: 1.5rem;
  font-weight: 700;
  color: #18181b;
  margin-bottom: 0.5rem;
}

.customer-status {
  display: inline-block;
  padding: 0.5rem 1.5rem;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.875rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

/* Detail Modal Body Spacing */
.modal-body {
  padding: 1.5rem;
  padding-top: 2rem;
  padding-bottom: 3rem;
  max-height: 65vh;
  overflow-y: auto;
}

.btn-close-white {
  filter: brightness(0) invert(1);
}

/* Bukti Preview */
.bukti-preview {
  max-width: 100%;
  max-height: 400px;
  border-radius: 8px;
  border: 2px solid #e4e4e7;
  margin-top: 0.5rem;
  cursor: pointer;
  transition: transform 0.3s;
}

.bukti-preview:hover {
  transform: scale(1.02);
}
</style>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const exportBtn = document.getElementById('btnExportExcel');


    

    // Detail Modal Handler
    $(document).on('click', '.btn-detail', function() {
      const tr = $(this).closest('tr');
      const id = tr.data('id');
      const namaLengkap = tr.data('nama');
      const alamat = tr.data('alamat');
      const namaPaket = tr.data('paket');
      const hargaPaket = tr.data('harga');
      const kecepatan = tr.data('kecepatan');
      const status = tr.data('status');
      const bank = tr.data('bank');
      const kabupaten = tr.data('kabupaten');
      const kecamatan = tr.data('kecamatan');
      const tanggalMulai = tr.data('tanggal-mulai');
      const tanggalBerakhir = tr.data('tanggal-berakhir');
      const catatan = tr.data('catatan');
      const buktiBayar = tr.data('bukti');

      let statusBadge = '';
      if (status.toLowerCase() === 'lunas') {
          statusBadge = '<span class="badge bg-success"><i class="ri-checkbox-circle-line me-1"></i>Lunas</span>';
      } else if (status.toLowerCase() === 'belum bayar') {
          statusBadge = '<span class="badge bg-danger"><i class="ri-close-circle-line me-1"></i>Belum Bayar</span>';
      } else {
          statusBadge = `<span class="badge bg-secondary"><i class="ri-information-line me-1"></i>${status}</span>`;
      }

      // Build modal HTML
      const html = `
          <div class="customer-header-info">
              <div class="customer-avatar">
                  ${namaLengkap.charAt(0).toUpperCase()}
              </div>
              <div class="customer-name">${namaLengkap}</div>
              <div class="customer-status bg-label-primary">ID: ${id}</div>
          </div>

          <div class="detail-section">
              <h6><i class="ri-map-pin-line"></i>Informasi Lokasi</h6>
              <div class="detail-item">
                  <div class="detail-label">
                      <i class="ri-map-pin-2-line"></i>Alamat
                  </div>
                  <div class="detail-value">${alamat}</div>
              </div>
              <div class="detail-item">
                  <div class="detail-label">
                      <i class="ri-building-line"></i>Kecamatan
                  </div>
                  <div class="detail-value">${kecamatan}</div>
              </div>
              <div class="detail-item">
                  <div class="detail-label">
                      <i class="ri-map-2-line"></i>Kabupaten
                  </div>
                  <div class="detail-value">${kabupaten}</div>
              </div>
          </div>

          <div class="detail-section">
              <h6><i class="ri-wifi-line"></i>Informasi Paket Internet</h6>
              <div class="detail-item">
                  <div class="detail-label">
                      <i class="ri-rocket-line"></i>Nama Paket
                  </div>
                  <div class="detail-value"><strong>${namaPaket}</strong></div>
              </div>
              <div class="detail-item">
                  <div class="detail-label">
                      <i class="ri-money-dollar-circle-line"></i>Harga Paket
                  </div>
                  <div class="detail-value"><strong class="text-primary">${hargaPaket}</strong></div>
              </div>
              <div class="detail-item">
                  <div class="detail-label">
                      <i class="ri-speed-line"></i>Kecepatan
                  </div>
                  <div class="detail-value"><span class="badge bg-label-info">${kecepatan}</span></div>
              </div>
              <div class="detail-item">
                  <div class="detail-label">
                      <i class="ri-bank-line"></i>Metode Pembayaran
                  </div>
                  <div class="detail-value"><span class="badge bg-label-dark">${bank}</span></div>
              </div>
          </div>

          <div class="detail-section">
              <h6><i class="ri-calendar-check-line"></i>Periode & Status Pembayaran</h6>
              <div class="detail-item">
                  <div class="detail-label">
                      <i class="ri-calendar-line"></i>Tanggal Mulai
                  </div>
                  <div class="detail-value">${tanggalMulai}</div>
              </div>
              <div class="detail-item">
                  <div class="detail-label">
                      <i class="ri-calendar-event-line"></i>Tanggal Berakhir
                  </div>
                  <div class="detail-value">${tanggalBerakhir}</div>
              </div>
              <div class="detail-item">
                  <div class="detail-label">
                      <i class="ri-shield-check-line"></i>Status Pembayaran
                  </div>
                  <div class="detail-value">${statusBadge}</div>
              </div>
              <div class="detail-item">
                  <div class="detail-label">
                      <i class="ri-file-text-line"></i>Catatan
                  </div>
                  <div class="detail-value">${catatan}</div>
              </div>
          </div>

          <div class="detail-section">
              <h6><i class="ri-image-line"></i>Bukti Pembayaran / Kwitansi</h6>
              <div class="text-center">
                  ${buktiBayar ?
                      '<a href="' + buktiBayar + '" target="_blank"><img src="' + buktiBayar + '" class="bukti-preview" alt="Bukti Pembayaran"></a>' :
                      '<div class="alert alert-warning mb-0"><i class="ri-error-warning-line me-2"></i>Tidak ada bukti pembayaran</div>'}
              </div>
          </div>
      `;

      $('#detailModal .modal-body').html(html);
      $('#detailModal').modal('show');
    });

    // Export with Progress Modal
    exportBtn.addEventListener('click', function(e) {
      e.preventDefault();
      
      const modal = new bootstrap.Modal(document.getElementById('exportProgressModal'), {
          backdrop: 'static',
          keyboard: false
      });
      modal.show();
      
      const progressBar = $('#exportProgressBar');
      const progressText = $('#exportProgressText');
      const progressPercentage = $('#exportProgressPercentage');
      
      // Reset
      progressBar.css('width', '0%');
      progressPercentage.text('0%');
      progressText.text('Menyiapkan data...');
      
      // Simulate Progress
      let progress = 0;
      const interval = setInterval(() => {
          progress += Math.floor(Math.random() * 5) + 2; 
          
          if (progress > 95) progress = 95; 
          
          progressBar.css('width', progress + '%');
          progressPercentage.text(progress + '%');
          
          if (progress < 30) {
              progressText.text('Menyiapkan data kwitansi...');
          } else if (progress < 70) {
            progressText.text('Menggenerate file Excel...');
          } else {
             progressText.text('Finishing up...');
          }
      }, 100);
      
      const formData = new FormData(filterForm);
      const params = new URLSearchParams();
      for (const [key, value] of formData.entries()) {
        if (value) params.append(key, value);
      }
      let url = "{{ route('laporan.kwitansi.export') }}";
      const qs = params.toString();
      if (qs) url += '?' + qs;
      
      setTimeout(() => {
          window.location.href = url;
          
          setTimeout(() => {
              clearInterval(interval);
              progressBar.css('width', '100%');
              progressPercentage.text('100%');
              progressText.text('Selesai! File sedang didownload.');
              
              setTimeout(() => {
                  modal.hide();
              }, 1000);
          }, 2000);
      }, 1500);
    });
  });
</script>
@endsection

@section('content')

<div class="card mt-4">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
    <h5 class="mb-2 mb-md-0">Daftar Kwitansi</h5>
    <button id="btnExportExcel" type="button" class="btn btn-primary">
      <i class="ri-file-excel-2-line me-1" style="color: #ffffff !important;"></i> Export Excel
    </button>
  </div>

  <div class="card-body border-top">
    <form id="filterForm" class="mb-3" method="GET" action="{{ route('laporan.kwitansi.index') }}">
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label fw-semibold" for="status">Status Pembayaran</label>
          <select name="status" id="status" class="form-select">
            <option value="">-- Semua Status --</option>
            <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
            <option value="belum bayar" {{ request('status') === 'belum bayar' ? 'selected' : '' }}>Belum Bayar</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold" for="kabupaten">Kabupaten</label>
          <select name="kabupaten" id="kabupaten" class="form-select">
            <option value="">-- Semua Kabupaten --</option>
            @foreach($kabupatens as $kab)
              <option value="{{ $kab }}" {{ request('kabupaten') === $kab ? 'selected' : '' }}>{{ $kab }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold" for="kecamatan">Kecamatan</label>
          <select name="kecamatan" id="kecamatan" class="form-select">
            <option value="">-- Semua Kecamatan --</option>
            @foreach($kecamatans as $kec)
              <option value="{{ $kec }}" {{ request('kecamatan') === $kec ? 'selected' : '' }}>{{ $kec }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="d-flex flex-wrap gap-2 mt-3">
        <button type="submit" class="btn btn-primary"><i class="ri-filter-3-line me-1"></i>Terapkan Filter</button>
        <a href="{{ route('laporan.kwitansi.index') }}" class="btn btn-outline-secondary"><i class="ri-refresh-line me-1"></i>Reset</a>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-modern table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th class="text-center" style="width: 60px;"><i class="ri-hashtag me-1"></i>NO</th>
            <th class="text-center" style="width: 80px;"><i class="ri-eye-line me-1"></i>DETAIL</th>
            <th><i class="ri-user-3-line me-1"></i>NAMA LENGKAP</th>
            <th><i class="ri-map-pin-line me-1"></i>ALAMAT</th>
            <th><i class="ri-rocket-line me-1"></i>NAMA PAKET</th>
            <th><i class="ri-money-dollar-circle-line me-1"></i>HARGA</th>
            <th><i class="ri-speed-line me-1"></i>KECEPATAN</th>
            <th><i class="ri-shield-check-line me-1"></i>STATUS</th>
            <th><i class="ri-map-2-line me-1"></i>KABUPATEN</th>
            <th><i class="ri-community-line me-1"></i>KECAMATAN</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tagihans as $tagihan)
          <tr
            data-id="{{ $tagihan->id }}"
            data-nama="{{ $tagihan->pelanggan->nama_lengkap ?? '-' }}"
            data-alamat="{{ $tagihan->pelanggan->alamat_jalan ?? '-' }}"
            data-paket="{{ $tagihan->paket->nama_paket ?? '-' }}"
            data-harga="Rp {{ number_format($tagihan->harga ?? $tagihan->paket->harga ?? 0, 0, ',', '.') }}"
            data-kecepatan="{{ $tagihan->paket->kecepatan ?? '-' }}"
            data-status="{{ $tagihan->status_pembayaran }}"
            data-bank="{{ $tagihan->rekening->nama_bank ?? 'Lainnya' }}"
            data-kabupaten="{{ $tagihan->pelanggan->kabupaten ?? '-' }}"
            data-kecamatan="{{ $tagihan->pelanggan->kecamatan ?? '-' }}"
            data-tanggal-mulai="{{ $tagihan->tanggal_mulai ?? '-' }}"
            data-tanggal-berakhir="{{ $tagihan->tanggal_berakhir ?? '-' }}"
            data-catatan="{{ $tagihan->catatan ?? '-' }}"
            data-bukti="{{ !empty($tagihan->kwitansi) ? asset('storage/' . $tagihan->kwitansi) : '' }}"
          >
            <td class="text-center fw-semibold">{{ ($tagihans->firstItem() ?? 0) + $loop->index }}</td>
            <td>
              <button class="btn btn-sm btn-icon btn-outline-secondary btn-detail" title="Lihat Detail">
                <i class="ri-eye-line"></i>
              </button>
            </td>
            <td>
              <div class="d-flex align-items-center">
                <span class="fw-semibold">{{ $tagihan->pelanggan->nama_lengkap ?? '-' }}</span>
              </div>
            </td>
            <td>
              <div>
                {{ \Illuminate\Support\Str::limit($tagihan->pelanggan->alamat_jalan ?? '-', 35) }}
                <br>
                <small class="text-muted">
                  <i class="ri-map-pin-2-line"></i>
                  {{ $tagihan->pelanggan->kecamatan ?? '-' }}, {{ $tagihan->pelanggan->kabupaten ?? '-' }}
                </small>
              </div>
            </td>
            <td><span class="badge bg-label-info">{{ $tagihan->paket->nama_paket ?? '-' }}</span></td>
            <td><span class="fw-bold text-primary">Rp {{ number_format($tagihan->harga ?? $tagihan->paket->harga ?? 0, 0, ',', '.') }}</span></td>
            <td><span class="badge bg-label-dark"><i class="ri-speed-line me-1"></i>{{ $tagihan->paket->kecepatan ?? '-' }}</span></td>
            <td>
              @php
                $statusClass = match(strtolower($tagihan->status_pembayaran)) {
                    'lunas' => 'badge bg-success',
                    'belum bayar' => 'badge bg-danger',
                    default => 'badge bg-secondary',
                };
                $statusIcon = match(strtolower($tagihan->status_pembayaran)) {
                    'lunas' => 'ri-checkbox-circle-line',
                    'belum bayar' => 'ri-close-circle-line',
                    default => 'ri-information-line',
                };
              @endphp
              <span class="{{ $statusClass }}">
                <i class="{{ $statusIcon }} me-1"></i>{{ ucfirst($tagihan->status_pembayaran ?? '-') }}
              </span>
            </td>
            <td>{{ $tagihan->pelanggan->kabupaten ?? '-' }}</td>
            <td>{{ $tagihan->pelanggan->kecamatan ?? '-' }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="11" class="text-center text-muted py-4">Tidak ada data kwitansi.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="pagination-wrapper">
        <div class="pagination-info">
            Menampilkan <strong>{{ $tagihans->firstItem() ?? 0 }}</strong> - <strong>{{ $tagihans->lastItem() ?? 0 }}</strong>
            dari <strong>{{ $tagihans->total() }}</strong> kwitansi
        </div>
        <div>
            @if($tagihans->hasPages())
                {{ $tagihans->appends(request()->query())->onEachSide(1)->links('vendor.pagination.custom') }}
            @else
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item disabled"><span class="page-link"><i class="ri-arrow-left-s-line"></i></span></li>
                        <li class="page-item active"><span class="page-link">1</span></li>
                        <li class="page-item disabled"><span class="page-link"><i class="ri-arrow-right-s-line"></i></span></li>
                    </ul>
                </nav>
            @endif
        </div>
    </div>
  </div>
</div>
<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary py-4">
        <h5 class="modal-title text-white fw-bold" id="detailModalLabel">
          <i class="ri-information-line me-2"></i>Detail Kwitansi
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Content will be inserted via JavaScript -->
      </div>
    </div>
  </div>
</div>

<!-- Export Progress Modal -->
<div class="modal fade modal-blur" id="exportProgressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <!-- Icon placeholder -->
                    <div style="width: 80px; height: 80px; background: #f4f4f5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        <i class="ri-file-excel-2-fill" style="font-size: 40px; color: #18181b;"></i>
                    </div>
                </div>
                <h4 class="mb-2 fw-bold text-dark">Export Data Kwitansi</h4>
                <p class="text-muted mb-4" id="exportProgressText">Menyiapkan data...</p>
                
                <div class="progress mb-2" style="height: 10px; background-color: #f4f4f5;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         id="exportProgressBar" role="progressbar" style="width: 0%; background-color: #18181b;"></div>
                </div>
                <div class="fw-bold fs-4" style="color: #18181b;" id="exportProgressPercentage">0%</div>
            </div>
        </div>
    </div>
</div>
@endsection
