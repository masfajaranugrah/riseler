@php
use Illuminate\Support\Str;

@endphp

@extends('layouts/layoutMaster')

@section('title', 'Kelola Notifikasi')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
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

/* ========== HEADER SECTION ========== */
.card-header-custom {
  background: #ffffff !important;
  border-bottom: 1px solid var(--gray-border);
  padding: 1.5rem;
  border-radius: var(--border-radius) var(--border-radius) 0 0;
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
.btn.btn-primary {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
  box-shadow: none !important;
}

.btn-primary:hover,
.btn.btn-primary:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
  transform: translateY(-2px) !important;
}

.btn-primary i,
.btn.btn-primary i {
  color: #ffffff !important;
}

.btn-success,
.btn.btn-success {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn-success:hover,
.btn.btn-success:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
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

/* ========== BADGES ========== */
.badge {
  border-radius: 9999px !important;
  font-weight: 500 !important;
  padding: 0.35rem 0.75rem !important;
}

.badge.bg-success {
  background: #18181b !important;
  color: #fafafa !important;
}

.badge.bg-secondary {
  background: #f4f4f5 !important;
  color: #71717a !important;
  border: 1px solid #e4e4e7;
}

.badge.bg-label-primary,
.badge.bg-label-info,
.badge.bg-label-success,
.badge.bg-label-warning {
  background: #18181b !important;
  color: #fafafa !important;
}

/* ========== NOTIFICATION IMAGE ========== */
.notification-image {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #e4e4e7;
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
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function() {
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }

    function hideLoading() {
        $('.loading-overlay').fadeOut(300);
    }

    // DataTable
    $('.datatables-notifications').DataTable({
        paging: true,
        pageLength: 10,
        searching: true,
        ordering: true,
        responsive: true,
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ notifikasi",
            paginate: {
                previous: '<i class="ri-arrow-left-s-line"></i>',
                next: '<i class="ri-arrow-right-s-line"></i>'
            }
        }
    });

    // Kirim Notifikasi
    $(document).on('click', '.btn-send', function(e) {
        e.preventDefault();
        const notifId = $(this).data('id');
        const title = $(this).data('title');

        Swal.fire({
            title: 'Kirim Notifikasi?',
            html: `<p>Kirim "<strong>${title}</strong>" ke semua pelanggan?</p>`,
            icon: 'question',
            showCancelButton: true,
            showDenyButton: false,
            confirmButtonText: '<i class="ri-send-plane-fill me-2"></i>Ya, Kirim!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();

                fetch(`/dashboard/admin/iklan/${notifId}/send`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    hideLoading();

                    if (data.queued) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Dikirim ke antrian',
                            html: `<small>Notifikasi sedang dikirim di background. Anda bisa lanjut bekerja.</small>`,
                            showConfirmButton: false,
                            timer: 3500,
                            timerProgressBar: true,
                        });
                        return;
                    }

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: `<p><strong>${data.sent || 0}</strong> notifikasi berhasil dikirim</p>`,
                            showCancelButton: false,
                            showDenyButton: false,
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'btn btn-success' },
                            buttonsStyling: false
                        });
                    }
                })
                .catch(err => {
                    hideLoading();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan!',
                        showCancelButton: false,
                        showDenyButton: false,
                        confirmButtonText: 'OK',
                        customClass: { confirmButton: 'btn btn-danger' },
                        buttonsStyling: false
                    });
                });
            }
        });
    });

    // Delete Notifikasi
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Hapus Notifikasi?',
            text: 'Data akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            showDenyButton: false,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endsection

@section('content')
<div class="loading-overlay">
    <div class="spinner-border text-light" style="width: 3rem; height: 3rem;"></div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header-custom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="ri-notification-3-line me-2"></i>Kelola Notifikasi
                </h4>
                <p class="mb-0 text-muted small">Buat dan kirim notifikasi ke pelanggan</p>
            </div>
            <a href="{{ route('iklan.create')}}" class="btn btn-primary">
                <i class="ri-add-line me-2 text-white"></i>Buat Notifikasi
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive p-3">
            <table class="datatables-notifications table table-modern table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Judul</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Terkirim</th>
                        <th>Dibuat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($iklans as $notif)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($notif->image)
                            <img src="{{ asset('storage/' . $notif->image) }}" class="notification-image" alt="Image">
                            @else
                            <div class="notification-image bg-light d-flex align-items-center justify-content-center">
                                <i class="ri-image-line text-muted"></i>
                            </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $notif->title }}</strong>
                            <br>
                            <small class="text-muted">{{ Str::limit($notif->message, 50) }}</small>
                        </td>
                        <td>
                            <span class="badge bg-label-{{ $notif->type_color }}">
                                <i class="{{ $notif->type_icon }} me-1"></i>
                                {{ ucfirst($notif->type) }}
                            </span>
                        </td>
                        <td>
                            @if($notif->status === 'draft')
                                <span class="badge bg-secondary">Draft</span>
                            @else
                                <span class="badge bg-success">Aktif</span>
                            @endif
                        </td>
                        <td>
                            @if($notif->status === 'sent')
                                <span class="text-success">{{ $notif->total_sent }} orang</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $notif->created_at->format('d M Y') }}</small>
                            <br>
                            <small class="text-muted">{{ $notif->creator->name ?? 'Admin' }}</small>
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                @if($notif->status === 'draft')
                                <button class="btn btn-sm btn-success btn-send"
                                        data-id="{{ $notif->id }}"
                                        data-title="{{ $notif->title }}">
                                    <i class="ri-send-plane-fill text-white"></i>
                                </button>
                                <a href="{{ route('iklan.edit', $notif->id) }}"
                                   class="btn btn-sm btn-primary">
                                    <i class="ri-edit-line text-white"></i>
                                </a>
                                @endif

                                <form action="{{ route('iklan.destroy', $notif->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger btn-delete">
                                        <i class="ri-delete-bin-line text-white"></i>
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
</div>
@endsection
