@extends('layouts/layoutMaster')

@section('title', 'User List - Pages')

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
  --gray-bg: #fafafa;
  --gray-border: #e4e4e7;
}

/* ========== CARD ========== */
.card {
  border: none;
  border-radius: var(--border-radius);
  box-shadow: var(--card-shadow);
  background: white;
  transition: var(--transition);
  overflow: hidden;
}

.card:hover {
  box-shadow: var(--card-hover-shadow);
}

/* ========== STATS CARD ========== */
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

/* ========== HEADER SECTION ========== */
.card-header-custom {
  background: #ffffff !important;
  border-bottom: 1px solid var(--gray-border);
  padding: 1.5rem;
  border-radius: var(--border-radius) var(--border-radius) 0 0;
  color: #18181b;
}

.card-header-custom h4 {
  color: #18181b !important;
  font-size: 1.5rem;
}

.card-header-custom p {
  color: #71717a !important;
}

.card-header-custom i {
  color: #18181b !important;
}

/* ========== TABLE STYLES ========== */
.table-modern {
  margin-bottom: 0;
  border-radius: 8px;
  overflow: hidden;
  border-collapse: separate;
  border-spacing: 0;
}

.table-modern thead th {
  background: #f8fafc;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  color: #18181b;
  padding: 1rem;
  border: none;
  white-space: nowrap;
}

.table-modern tbody tr {
  transition: var(--transition);
  border-bottom: 1px solid #e4e4e7;
}

.table-modern tbody tr:hover {
  background-color: #f4f4f5 !important;
}

.table-modern tbody td {
  padding: 1rem;
  vertical-align: middle;
  border-bottom: 1px solid #e4e4e7;
  color: #18181b;
}

/* ========== BUTTONS - ALL BLACK ========== */
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

.btn-primary,
.btn.btn-primary,
.btn-add {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
  box-shadow: none !important;
  padding: 10px 24px;
  border-radius: 8px;
  font-weight: 600;
}

.btn-primary:hover,
.btn.btn-primary:hover,
.btn-add:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
  transform: translateY(-2px) !important;
}

.btn-add i {
  margin-right: 8px;
  color: #ffffff !important;
}

.btn-danger,
.btn.btn-danger {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn-danger:hover,
.btn.btn-danger:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

.btn-secondary,
.btn.btn-secondary {
  background: transparent !important;
  background-color: transparent !important;
  border: 1px solid #e4e4e7 !important;
  color: #18181b !important;
}

.btn-secondary:hover,
.btn.btn-secondary:hover {
  background: #f4f4f5 !important;
  background-color: #f4f4f5 !important;
  border-color: #18181b !important;
  color: #18181b !important;
}

.btn-outline-primary,
.btn-outline-danger {
  background: transparent !important;
  border: 1px solid #18181b !important;
  color: #18181b !important;
}

.btn-outline-primary:hover,
.btn-outline-danger:hover {
  background: #18181b !important;
  color: #fafafa !important;
}

/* ========== BADGES ========== */
.badge {
  border-radius: 9999px !important;
  font-weight: 500 !important;
  padding: 0.35rem 0.75rem !important;
}

.badge.bg-label-primary {
  background: #18181b !important;
  color: #fafafa !important;
}

.badge-status {
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 0.75rem;
}

.action-buttons {
  gap: 12px;
}

.icon-wrapper {
  width: 48px;
  height: 48px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  font-size: 24px;
  background: #18181b;
  color: #fafafa;
}

/* ========== LOADING OVERLAY ========== */
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

.spinner-border-custom {
  width: 3rem;
  height: 3rem;
  border-width: 0.3rem;
}

/* ========== DROPDOWN ========== */
.dropdown-menu {
  border: 1px solid #e4e4e7;
  border-radius: 8px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.12);
  padding: 0.5rem;
}

.dropdown-item {
  border-radius: 6px;
  padding: 0.5rem 1rem;
  transition: all 0.2s;
  font-size: 0.875rem;
  color: #18181b;
}

.dropdown-item:hover {
  background: #f4f4f5;
  transform: translateX(4px);
}

.dropdown-item i {
  width: 20px;
}

/* ========== MODAL STYLES ========== */
.modal-content {
  border-radius: 16px;
  border: none;
  box-shadow: 0 8px 32px rgba(0,0,0,0.15);
}

.modal-header {
  background: #18181b;
  border-radius: 16px 16px 0 0;
  padding: 1.5rem;
  border-bottom: none;
}

.modal-title {
  font-weight: 600;
  font-size: 1.125rem;
  color: #ffffff;
}

.modal-body {
  padding: 2rem;
}

.modal-body p {
  margin-bottom: 1rem;
  line-height: 1.6;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f0f0f0;
}

.modal-body p:last-child {
  border-bottom: none;
}

.modal-body strong {
  color: #18181b;
  font-weight: 600;
  display: inline-block;
  min-width: 100px;
}

.modal-footer {
  padding: 1.5rem;
  border-top: 1px solid #f0f0f0;
  background: #fafafa;
}

.btn-close-white {
  filter: brightness(0) invert(1);
}

/* ========== USER AVATAR ========== */
.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #18181b;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #fafafa;
  font-weight: 600;
  font-size: 1rem;
  margin-right: 12px;
}

/* ========== PAGINATION STYLES ========== */
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
}

/* ========== ANIMATIONS ========== */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.card {
  animation: fadeIn 0.3s ease-out;
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
@vite(['resources/assets/js/extended-ui-perfect-scrollbar.js'])
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Helper function untuk loading overlay
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }
    
    function hideLoading() {
        $('.loading-overlay').fadeOut(300);
    }

    // Inisialisasi DataTable dengan styling modern
    // Inisialisasi DataTable dengan styling modern (Tanpa Pagination internal karena pakai backend pagination)
    const dtUserTable = $('.datatables-users').DataTable({
        paging: false, // Matikan paging DataTables
        searching: false, // Matikan searching DataTables
        ordering: true,
        info: false, // Matikan info DataTables
        responsive: true,
        dom: 'rt', // Hanya tampilkan table
        columnDefs: [
            { orderable: false, targets: [0, -1] }
        ],
        language: {
            zeroRecords: "Tidak ada data yang sesuai"
        }
    });

    // Event Detail User
    $(document).on('click', '.btn-detail', function(e) {
        e.stopPropagation();
        const tr = $(this).closest('tr');
        const row = dtUserTable.row(tr).data();
        if (!row) return;

        const html = `
            <div class="row g-3">
                <div class="col-12 text-center mb-3">
                    <div class="user-avatar mx-auto" style="width: 80px; height: 80px; font-size: 2rem;">
                        ${row[1].charAt(0).toUpperCase()}
                    </div>
                </div>
                <div class="col-12">
                    <p><strong>Nama:</strong> ${row[1]}</p>
                </div>
                <div class="col-12">
                    <p><strong>Email:</strong> ${row[2]}</p>
                </div>
                <div class="col-12">
                    <p><strong>Role:</strong> <span class="badge bg-label-primary">${row[3]}</span></p>
                </div>
            </div>
        `;
        $('#detailModal .modal-body').html(html);
        $('#detailModal').modal('show');
    });

     // Event DELETE dengan konfirmasi modern - HANYA 2 BUTTON
$(document).on('click', '.btn-delete', function(e) {
    e.preventDefault();
    e.stopPropagation();
    const form = $(this).closest('form');

    Swal.fire({
        title: 'Konfirmasi Penghapusan',
        text: 'Yakin ingin menghapus data karyawan ini? Data tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        showDenyButton: false,  // Pastikan deny button tidak muncul
        confirmButtonText: '<i class="ri-delete-bin-line me-2"></i>Ya, Hapus!',
        cancelButtonText: '<i class="ri-close-line me-2"></i>Batal',
        confirmButtonColor: '#f5365c',
        cancelButtonColor: '#8898aa',
        reverseButtons: true,  // Cancel di kiri, Confirm di kanan
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-danger me-2',
            cancelButton: 'btn btn-secondary'
        },
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed) {
            const btn = $(form).find('.btn-delete');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menghapus...');
            showLoading();
            
            setTimeout(() => {
                hideLoading();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Dihapus!',
                    text: 'Data karyawan berhasil dihapus dari sistem.',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    allowOutsideClick: false
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

<!-- Users List Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header-custom">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="ri-group-line me-2"></i>Data Users
                </h4>
                <p class="mb-0 opacity-75 small">Kelola dan monitor data pengguna sistem</p>
            </div>
            <div class="d-flex action-buttons mt-3 mt-md-0">
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-add">
                    <i class="ri-user-add-line"></i>
                    Tambah User Baru
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="card-datatable table-responsive p-3">
            <table class="datatables-users table table-modern table-hover">
                <thead>
                    <tr>
 
                        <th><i class="ri-user-3-line me-1"></i>Nama</th>
                        <th><i class="ri-mail-line me-1"></i>Email</th>
                        <th><i class="ri-shield-user-line me-1"></i>Role</th>
                        <th class="text-center"><i class="ri-settings-3-line me-1"></i>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                    
                        
                        <td>
                            <div class="d-flex align-items-center">
                                                          <div>
                                    <span class="fw-semibold">{{ $user->name }}</span>
                                </div>
                            </div>
                        </td>
                        
                        <td>
                            <i class="ri-mail-line me-1 text-muted"></i>
                            {{ $user->email }}
                        </td>
                        
                        <td>
                            <span class="badge bg-label-primary">
                                <i class="ri-shield-user-line me-1"></i>{{ ucfirst($user->role) }}
                            </span>
                        </td>
                        
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Edit User">
                                    <i class="ri-edit-2-line"></i>
                                </a>
                                
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete" title="Hapus User">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Custom Pagination --}}
            @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator && $users->total() > 0)
            <div class="pagination-wrapper mt-4 p-3 d-flex justify-content-between align-items-center">
                <div class="pagination-info text-muted small">
                    Menampilkan <strong>{{ $users->firstItem() ?? 0 }}</strong> - <strong>{{ $users->lastItem() ?? 0 }}</strong> 
                    dari <strong>{{ $users->total() }}</strong> users
                </div>
                <div>
                    @if($users->hasPages())
                        {{ $users->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-information-line me-2"></i>Detail User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <!-- Content will be inserted via JavaScript -->
            </div>
            
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
