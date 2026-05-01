@extends('layouts/layoutMaster')

@section('title', 'Data Rekening')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
<style>
/* ========================================= */
/* SHADCN UI STYLE - BLACK & WHITE */
/* ========================================= */
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
  transition: all 0.3s;
  box-shadow: 0 4px 12px rgba(24,24,27,0.2);
}
.btn-primary:hover, .btn-add:hover {
  background: #27272a !important;
  border-color: #27272a !important;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(24,24,27,0.3);
}
.btn-add i { margin-right: 8px; }

.table-modern { border-radius: 8px; overflow: hidden; }
.table-modern thead th {
  background: #f8fafc;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  color: #18181b;
  border: none;
  padding: 1rem;
}
.table-modern tbody tr {
  transition: all 0.2s;
  border-bottom: 1px solid #e4e4e7;
}
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

.badge.bg-label-primary { background: #18181b !important; color: #fafafa !important; font-weight: 600; }
.badge.bg-label-info { background: #18181b !important; color: #fafafa !important; font-weight: 600; }

.icon-wrapper.bg-label-primary { background: #18181b !important; }
.icon-wrapper.bg-label-primary i { color: #fafafa !important; }

.btn-outline-primary, .btn-outline-danger {
  background: transparent !important;
  border: 1px solid #18181b !important;
  color: #18181b !important;
}
.btn-outline-primary:hover, .btn-outline-danger:hover {
  background: #18181b !important;
  color: #fafafa !important;
}

code {
  background: #18181b !important;
  color: #fafafa !important;
  padding: 4px 12px;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 600;
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
.pagination .page-item .page-link:hover { background-color: #f4f4f5; border-color: #18181b; }
.pagination .page-item.active .page-link { background-color: #18181b !important; border-color: #18181b !important; color: #fafafa !important; }
.pagination .page-item.disabled .page-link { background-color: #f4f4f5; border-color: #e4e4e7; color: #a1a1aa; cursor: not-allowed; }

/* Hide default Laravel pagination results text */
.pagination-wrapper .pagination + div,
.pagination-wrapper nav + div,
.pagination-wrapper div:has(> nav) > p,
.pagination-wrapper > div > nav ~ *:not(.pagination),
.pagination-wrapper > div:last-child p,
nav[role="navigation"] > div:first-child,
nav[role="navigation"] > div > p {
  display: none !important;
}
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Helper function untuk loading overlay
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }
    
    function hideLoading() {
        $('.loading-overlay').fadeOut(300);
    }

    // Inisialisasi DataTable
    // DataTable for ordering only - pagination is handled by Laravel
    const dtRekeningTable = $('.datatables-rekenings').DataTable({
        paging: false,
        searching: false,
        ordering: true,
        info: false,
        responsive: false,
        columnDefs: [
            { orderable: false, targets: [-1] }
        ]
    });

    // Event DELETE dengan konfirmasi modern - HANYA 2 BUTTON
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Konfirmasi Penghapusan',
            text: 'Yakin ingin menghapus data rekening ini? Data tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            showDenyButton: false,
            showCloseButton: false,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#f5365c',
            cancelButtonColor: '#8898aa',
            reverseButtons: false,
            allowOutsideClick: false,
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = $(form).find('.btn-delete');
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menghapus...');
                showLoading();
                
                setTimeout(() => {
                    hideLoading();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data rekening berhasil dihapus.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        form.submit();
                    });
                }, 1000);
            }
        });
    });
});
</script>
@endsection

@section('content')
<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner-border spinner-border-custom text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- Rekening List Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header-custom">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="ri-bank-card-2-line me-2"></i>Data Rekening
                </h4>
                <p class="mb-0 opacity-75 small">Kelola dan monitor data rekening bank</p>
            </div>
            <div class="d-flex mt-3 mt-md-0">
                <a href="{{ route('rekenings.add') }}" class="btn btn-primary btn-add">
                    <i class="ri-add-line text-white" style="color: #fff !important;"></i>
                    Tambah Rekening
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="card-datatable table-responsive p-3">
            <table class="datatables-rekenings table table-modern table-hover">
                <thead>
                    <tr>
                        <th><i class="ri-hashtag me-1"></i>No</th>
                        <th><i class="ri-bank-line me-1"></i>Nama Bank</th>
                        <th><i class="ri-bank-card-line me-1"></i>Nomor Rekening</th>
                        <th><i class="ri-user-3-line me-1"></i>Nama Pemilik</th>
                        <th class="text-center"><i class="ri-settings-3-line me-1"></i>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekenings as $rekening)
                    <tr>
                        <td class="fw-bold">{{ $loop->iteration + ($rekenings->currentPage() - 1) * $rekenings->perPage() }}</td>
                        
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper bg-label-primary me-2" style="width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="ri-bank-line" style="font-size: 1.25rem;"></i>
                                </div>
                                <span class="fw-semibold">{{ $rekening->nama_bank }}</span>
                            </div>
                        </td>
                        
                        <td>
                            <code style="background: #f8f9fa; padding: 4px 12px; border-radius: 6px; font-size: 0.875rem; font-weight: 600;">
                                {{ $rekening->nomor_rekening }}
                            </code>
                        </td>
                        
                        <td>
                            <span class="badge bg-label-info" style="padding: 8px 16px; font-size: 0.8rem;">
                                <i class="ri-user-line me-1"></i>{{ $rekening->nama_pemilik }}
                            </span>
                        </td>
                        
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('rekenings.edit', $rekening->id) }}" 
                                   class="btn btn-sm btn-outline-primary"
                                   title="Edit">
                                    <i class="ri-edit-2-line"></i>
                                </a>

                                <form action="{{ route('rekenings.destroy', $rekening->id) }}" method="POST" class="d-inline">
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
        </div>
    </div>

    <div class="pagination-wrapper">
        <div class="pagination-info">
            Menampilkan <strong>{{ $rekenings->firstItem() ?? 0 }}</strong> - <strong>{{ $rekenings->lastItem() ?? 0 }}</strong>
            dari <strong>{{ $rekenings->total() }}</strong> rekening
        </div>
        <div>
            @if($rekenings->hasPages())
                {{ $rekenings->onEachSide(1)->links('pagination::bootstrap-5') }}
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
@endsection
