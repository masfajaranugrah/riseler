@extends('layouts/layoutMaster')

@section('title', 'Gaji Karyawan')

@php
use Illuminate\Support\Str;
@endphp

{{-- VENDOR STYLE --}}
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
<style>
:root {
  --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
  --card-hover-shadow: 0 4px 16px rgba(0,0,0,0.12);
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --primary-color: #18181b;
  --gray-border: #e4e4e7;
}
.card {
  border: none;
  border-radius: var(--border-radius);
  box-shadow: var(--card-shadow);
  background: white;
  transition: var(--transition);
}
.card:hover { box-shadow: var(--card-hover-shadow); }
.stats-card {
  border-radius: 12px;
  transition: transform 0.2s, box-shadow 0.2s;
  background: #18181b;
  color: #fafafa;
}
.stats-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(24,24,27,0.3);
}
.badge-status { font-weight: 600; padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; }
.action-buttons { gap: 12px; }
.card-header-custom {
  background: #ffffff !important;
  border-bottom: 1px solid var(--gray-border);
  padding: 1.5rem;
  border-radius: var(--border-radius) var(--border-radius) 0 0;
  color: #18181b;
}
.card-header-custom h4 { color: #18181b !important; }
.card-header-custom p { color: #71717a !important; }
.card-header-custom i { color: #18181b !important; }
.btn-primary, .btn.btn-primary, .btn-add {
  background: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
  padding: 10px 24px;
  border-radius: 8px;
  font-weight: 600;
}
.btn-primary i, .btn.btn-primary i { color: #ffffff !important; }
.btn-primary:hover, .btn-add:hover {
  background: #27272a !important;
  border-color: #27272a !important;
  transform: translateY(-2px) !important;
}
.btn-add i { margin-right: 8px; }
.btn-danger { background: #18181b !important; color: #fafafa !important; border: 1px solid #18181b !important; }
.btn-danger:hover { background: #27272a !important; }
.btn-secondary { background: transparent !important; border: 1px solid #e4e4e7 !important; color: #18181b !important; }
.btn-secondary:hover { background: #f4f4f5 !important; border-color: #18181b !important; }
.btn-outline-primary, .btn-outline-danger {
  background: transparent !important;
  border: 1px solid #18181b !important;
  color: #18181b !important;
}
.btn-outline-primary:hover, .btn-outline-danger:hover {
  background: #18181b !important;
  color: #fafafa !important;
}
.table-modern { border-radius: 8px; overflow: hidden; }
.table-modern thead th {
  background: #f8fafc;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  color: #18181b;
  padding: 1rem;
  border: none;
}
.table-modern thead th:first-child, .table-modern tbody td:first-child { text-align: center; width: 60px; }
.table-modern tbody tr { border-bottom: 1px solid #e4e4e7; }
.table-modern tbody tr:hover { background-color: #f4f4f5 !important; }
.table-modern tbody td { padding: 1rem; color: #18181b; vertical-align: middle; }
.loading-overlay {
  position: fixed;
  inset: 0;
  background: rgba(24, 24, 27, 0.5);
  backdrop-filter: blur(4px);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}
.spinner-border-custom { width: 3rem; height: 3rem; border-width: 0.3rem; }
.modal-content { border-radius: 16px; border: none; box-shadow: 0 8px 32px rgba(0,0,0,0.15); }
.modal-header {
  background: #18181b;
  border-radius: 16px 16px 0 0;
  padding: 1.5rem;
  border-bottom: none;
}
.modal-title { font-weight: 600; font-size: 1.125rem; color: #ffffff; }
.modal-body { padding: 2rem; max-height: 70vh; overflow-y: auto; }
.modal-footer { padding: 1.5rem; border-top: 1px solid #f0f0f0; background: #fafafa; }
.btn-close-white { filter: brightness(0) invert(1); }
/* Modal Blur Effect */
.modal-backdrop { 
  backdrop-filter: blur(8px) !important; 
  -webkit-backdrop-filter: blur(8px) !important; 
  background-color: rgba(24, 24, 27, 0.5) !important; 
}
.modal-backdrop.show { opacity: 1 !important; }
.modal { backdrop-filter: none !important; }
.modal-content { backdrop-filter: none !important; filter: none !important; }
.detail-section {
  background: #ffffff;
  border: 1px solid #e4e4e7;
  border-radius: 12px;
  padding: 1.25rem;
  margin-bottom: 1.25rem;
  transition: all 0.2s;
}
.detail-section:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-color: #18181b; }
.detail-section h6 {
  color: #18181b;
  font-weight: 700;
  margin-bottom: 1.25rem;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  display: flex;
  align-items: center;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #18181b;
}
.detail-section h6 i { margin-right: 0.5rem; font-size: 1.1rem; }
.detail-item { display: flex; padding: 0.875rem 0; border-bottom: 1px solid #f0f0f0; align-items: flex-start; }
.detail-item:last-child { border-bottom: none; padding-bottom: 0; }
.detail-label {
  color: #18181b;
  font-weight: 600;
  min-width: 180px;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
}
.detail-label i { margin-right: 0.5rem; color: #71717a; font-size: 1rem; }
.detail-value { color: #18181b; font-size: 0.875rem; flex: 1; word-break: break-word; }
.employee-header-info {
  text-align: center;
  padding: 1.5rem;
  background: #f4f4f5;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  border: 1px solid #e4e4e7;
}
.employee-name { font-size: 1.5rem; font-weight: 700; color: #18181b; margin-bottom: 0.5rem; }
.btn-icon-detail {
  width: 32px;
  height: 32px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  transition: all 0.3s;
}
.badge.bg-label-success { background: #18181b !important; color: #fafafa !important; }
.text-primary { color: #18181b !important; }
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
}
.pagination .page-item .page-link:hover { background-color: #f4f4f5; border-color: #18181b; }
.pagination .page-item.active .page-link { background-color: #18181b !important; border-color: #18181b !important; color: #fafafa !important; }

/* Hide duplicate pagination summary from Laravel Links */
.pagination-wrapper nav .text-muted {
    display: none !important;
}
.dataTables_info {
    display: none !important;
}
</style>
@endsection

{{-- VENDOR SCRIPT --}}
@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

{{-- PAGE SCRIPT --}}
@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function() {

    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }

    function hideLoading() {
        $('.loading-overlay').fadeOut(300);
    }

    // Inisialisasi DataTables
    const dtUserTable = $('.datatables-users').DataTable({
        paging: false,
        searching: false, // Disable DataTables search
        ordering: true,
        info: false,
        responsive: false,
        dom: 'rt',
        columnDefs: [
            { orderable: false, targets: [0, 1, -1] }
        ],
        language: {
            zeroRecords: "Tidak ada data yang sesuai"
        }
    });

    // Event Detail Gaji
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const tr = $(this).closest('tr');
        const nama = tr.data('nama') || '-';
        const gajiPokok = tr.data('gaji-pokok') || '0';
        const tunjJabatan = tr.data('tunj-jabatan') || '0';
        const tunjFungsional = tr.data('tunj-fungsional') || '0';
        const transport = tr.data('transport') || '0';
        const makan = tr.data('makan') || '0';
        const tunjDynamic = tr.data('tunj-dynamic') || '-';
        const tunjKehadiran = tr.data('tunj-kehadiran') || '0';
        const lembur = tr.data('lembur') || '0';
        const potSosial = tr.data('pot-sosial') || '0';
        const potDenda = tr.data('pot-denda') || '0';
        const potKoperasi = tr.data('pot-koperasi') || '0';
        const potPajak = tr.data('pot-pajak') || '0';
        const potLain = tr.data('pot-lain') || '0';
        const total = tr.data('total') || '0';
        const grandTotal = tr.data('grand-total') || '0';

        const initial = nama ? nama.charAt(0).toUpperCase() : '?';

        const html = `
            <div class="employee-header-info">
                <div class="employee-name">${nama}</div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-money-dollar-circle-line"></i>Komponen Gaji Pokok</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-bank-card-line"></i>Gaji Pokok
                    </span>
                    <span class="detail-value"><strong>Rp ${gajiPokok}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-briefcase-line"></i>Tunjangan Jabatan
                    </span>
                    <span class="detail-value">Rp ${tunjJabatan}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-award-line"></i>Tunjangan Fungsional
                    </span>
                    <span class="detail-value">Rp ${tunjFungsional}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-gift-line"></i>Tunjangan Tambahan</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-taxi-line"></i>Transport
                    </span>
                    <span class="detail-value">Rp ${transport}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-restaurant-line"></i>Makan
                    </span>
                    <span class="detail-value">Rp ${makan}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-star-line"></i>Tunjangan Dinamis
                    </span>
                    <span class="detail-value">${tunjDynamic}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-calendar-check-line"></i>Tunjangan Kehadiran
                    </span>
                    <span class="detail-value">Rp ${tunjKehadiran}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-time-line"></i>Lembur
                    </span>
                    <span class="detail-value">Rp ${lembur}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-subtract-line"></i>Potongan</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-hand-heart-line"></i>Potongan Sosial
                    </span>
                    <span class="detail-value text-danger">Rp ${potSosial}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-error-warning-line"></i>Potongan Denda
                    </span>
                    <span class="detail-value text-danger">Rp ${potDenda}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-store-line"></i>Potongan Koperasi
                    </span>
                    <span class="detail-value text-danger">Rp ${potKoperasi}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-file-list-line"></i>Potongan Pajak
                    </span>
                    <span class="detail-value text-danger">Rp ${potPajak}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-more-line"></i>Potongan Lainnya
                    </span>
                    <span class="detail-value text-danger">Rp ${potLain}</span>
                </div>
            </div>

            <div class="detail-section">
                <h6><i class="ri-calculator-line"></i>Total Gaji</h6>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-money-dollar-box-line"></i>Total Penghasilan
                    </span>
                    <span class="detail-value"><strong>Rp ${total}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">
                        <i class="ri-wallet-3-line"></i>Grand Total (Setelah Potongan)
                    </span>
                    <span class="detail-value text-success"><strong style="font-size: 1.1rem;">Rp ${grandTotal}</strong></span>
                </div>
            </div>
        `;

        $('#detailModal .modal-body').html(html);
        $('#detailModal').modal('show');
    });

    // Event DELETE dengan konfirmasi - VERSI SIMPLE
$(document).on('click', '.btn-delete', function(e) {
    e.preventDefault();
    e.stopPropagation();
    const form = $(this).closest('form');

    Swal.fire({
        title: 'Konfirmasi Penghapusan',
        text: 'Yakin ingin menghapus data gaji ini? Data tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-danger me-2',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            setTimeout(() => {
                hideLoading();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data gaji berhasil dihapus.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    form.submit();
                });
            }, 1000);
        }
    });
});




// Event Copy Share Link
$(document).on('click', '.btn-share', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const url = $(this).data('url');
    
    // Copy to clipboard
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Disalin!',
                text: 'Link slip gaji berhasil disalin ke clipboard.',
                timer: 1500,
                showConfirmButton: false
            });
        });
    } else {
        // Fallback for non-secure context
        let textArea = document.createElement("textarea");
        textArea.value = url;
        textArea.style.position = "fixed";
        textArea.style.left = "-9999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            Swal.fire({
                icon: 'success',
                title: 'Disalin!',
                text: 'Link slip gaji berhasil disalin ke clipboard.',
                timer: 1500,
                showConfirmButton: false
            });
        } catch (err) {
            console.error('Fallback copy failed', err);
        }
        document.body.removeChild(textArea);
    }
});

});
</script>
@endsection

{{-- CONTENT --}}
@section('content')
<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner-border spinner-border-custom text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="ri-money-dollar-circle-line me-2"></i>Data Gaji Karyawan
        </h4>
        <p class="mb-0 text-muted small">Kelola dan monitor gaji karyawan perusahaan</p>
    </div>
    <div class="mt-3 mt-md-0">
        <a href="{{ route('gaji.create') }}" class="btn btn-primary btn-add shadow-sm">
            <i class="ri-add-circle-line me-1"></i>
            Tambah Data Gaji
        </a>
    </div>
</div>

<!-- Gaji Karyawan List -->
<div class="card border-0 shadow-sm">
    <div class="card-header-custom">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold">
                    <i class="ri-list-check me-2"></i>Daftar Riwayat Gaji
                </h5>
            </div>
            <!-- Search Form -->
            <div class="mt-4">
                <form action="{{ route('gaji.index') }}" method="GET" class="d-flex gap-2">
                    <div class="input-group" style="max-width: 400px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="ri-search-line text-muted"></i>
                        </span>
                        <input 
                            type="text" 
                            name="search" 
                            class="form-control border-start-0 ps-0" 
                            placeholder="Cari nama karyawan atau nominal..." 
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-search-line me-1"></i> Cari
                    </button>
                    @if(request('search'))
                    <a href="{{ route('gaji.index') }}" class="btn btn-outline-secondary">
                        <i class="ri-close-line me-1"></i> Reset
                    </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="card-datatable table-responsive p-3">
            <table class="datatables-users table table-modern table-hover">
                <thead>
                    <tr>
                        <th><i class="ri-hashtag me-1"></i>No</th>
                        <th><i class="ri-eye-line me-1"></i>Detail</th>
                        <th><i class="ri-user-3-line me-1"></i>Nama</th>
                        <th><i class="ri-bank-card-line me-1"></i>Gaji Pokok</th>
                        <th><i class="ri-briefcase-line me-1"></i>Tunj Jabatan</th>
                        <th><i class="ri-gift-line me-1"></i>Tunj Fungsional</th>
                        <th><i class="ri-money-dollar-box-line me-1"></i>Grand Total</th>
                        <th class="text-center"><i class="ri-settings-3-line me-1"></i>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salaries as $salary)
                    <tr
                        data-nama="{{ $salary->employee->full_name }}"
                        data-gaji-pokok="{{ number_format($salary->gaji_pokok, 0, ',', '.') }}"
                        data-tunj-jabatan="{{ number_format($salary->tunj_jabatan, 0, ',', '.') }}"
                        data-tunj-fungsional="{{ number_format($salary->tunj_fungsional, 0, ',', '.') }}"
                        data-transport="{{ number_format($salary->transport, 0, ',', '.') }}"
                        data-makan="{{ number_format($salary->makan, 0, ',', '.') }}"
                        data-tunj-dynamic="@if($salary->tunj_dynamic)
                            @foreach(json_decode($salary->tunj_dynamic, true) as $key => $val)
                                {{ 'Tunjangan '.($key+1).': Rp '.number_format($val,0,',','.') }} ({{ json_decode($salary->tunj_keterangan, true)[$key] ?? '-' }})<br>
                            @endforeach
                        @else - @endif"
                        data-tunj-kehadiran="{{ number_format($salary->tunj_kehadiran, 0, ',', '.') }}"
                        data-lembur="{{ number_format($salary->lembur, 0, ',', '.') }}"
                        data-pot-sosial="{{ number_format($salary->pot_sosial, 0, ',', '.') }}"
                        data-pot-denda="{{ number_format($salary->pot_denda, 0, ',', '.') }}"
                        data-pot-koperasi="{{ number_format($salary->pot_koperasi, 0, ',', '.') }}"
                        data-pot-pajak="{{ number_format($salary->pot_pajak, 0, ',', '.') }}"
                        data-pot-lain="{{ number_format($salary->pot_lain, 0, ',', '.') }}"
                        data-total="{{ number_format($salary->total, 0, ',', '.') }}"
                        data-grand-total="{{ number_format($salary->grand_total, 0, ',', '.') }}"
                    >
                        <td class="text-muted fw-semibold">{{ $loop->iteration }}</td>
                        <td>
                            <button class="btn btn-sm btn-icon btn-outline-primary btn-detail" title="Lihat Detail">
                                <i class="ri-eye-line"></i>
                            </button>
                        </td>

                        <td>
                            <div class="d-flex align-items-center">
                                <span class="fw-semibold">{{ $salary->employee->full_name }}</span>
                            </div>
                        </td>

                        <td><span class="badge bg-label-success">Rp {{ number_format($salary->gaji_pokok, 0, ',', '.') }}</span></td>
                        <td>Rp {{ number_format($salary->tunj_jabatan, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($salary->tunj_fungsional, 0, ',', '.') }}</td>
                        <td><strong class="text-primary">Rp {{ number_format($salary->grand_total, 0, ',', '.') }}</strong></td>

                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-info btn-share" 
                                        data-url="{{ route('gaji.share.public', $salary->id) }}"
                                        title="Salin Link Publik">
                                    <i class="ri-share-forward-line"></i>
                                </button>

                                <a href="{{ route('gaji.print', $salary->id) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   target="_blank"
                                   title="Cetak PDF">
                                    <i class="ri-printer-line"></i>
                                </a>

                                <a href="{{ route('gaji.edit', $salary->id) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Edit">
                                    <i class="ri-edit-2-line"></i>
                                </a>

                                <form action="{{ route('gaji.delete', $salary->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete" title="Hapus">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if($salaries->total() > 0)
              <div class="pagination-wrapper mt-4 p-3 d-flex justify-content-between align-items-center">
                <div class="pagination-info text-muted small">
                  Menampilkan <strong>{{ $salaries->firstItem() ?? 0 }}</strong> - <strong>{{ $salaries->lastItem() ?? 0 }}</strong> dari <strong>{{ $salaries->total() }}</strong> result
                </div>
                <div>
                  @if($salaries->lastPage() > 1)
                      {{ $salaries->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
                  @else
                      <nav aria-label="Page navigation">
                          <ul class="pagination mb-0 justify-content-end">
                              <li class="page-item disabled">
                                  <span class="page-link"><i class="ri-arrow-left-s-line"></i></span>
                              </li>
                              <li class="page-item active">
                                  <span class="page-link">1</span>
                              </li>
                              <li class="page-item disabled">
                                  <span class="page-link"><i class="ri-arrow-right-s-line"></i></span>
                              </li>
                          </ul>
                      </nav>
                  @endif
                </div>
              </div>
            @endif
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header pb-4">
                <h5 class="modal-title">
                    <i class="ri-information-line me-2"></i>Detail Gaji Karyawan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Custom content will be inserted via JavaScript -->
            </div>
 
        </div>
    </div>
</div>
@endsection
