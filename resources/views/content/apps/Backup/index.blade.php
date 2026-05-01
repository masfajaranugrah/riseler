@extends('layouts/layoutMaster')

@section('title', 'Backup Database')

@php
use Illuminate\Support\Facades\File;
@endphp

@section('vendor-style')
@vite([
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
.card:hover {
  box-shadow: var(--card-hover-shadow);
}
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
.btn-primary:hover, .btn-add:hover {
  background: #27272a !important;
  border-color: #27272a !important;
  transform: translateY(-2px) !important;
}
.btn-add i { margin-right: 8px; color: #ffffff !important; }
.btn-danger { background: #18181b !important; color: #fafafa !important; border: 1px solid #18181b !important; }
.btn-danger:hover { background: #27272a !important; }
.btn-secondary { background: transparent !important; border: 1px solid #e4e4e7 !important; color: #18181b !important; }
.btn-secondary:hover { background: #f4f4f5 !important; border-color: #18181b !important; }
.btn-outline-success, .btn-outline-danger {
  background: transparent !important;
  border: 1px solid #18181b !important;
  color: #18181b !important;
}
.btn-outline-success:hover, .btn-outline-danger:hover {
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
.table-modern tbody tr { border-bottom: 1px solid #e4e4e7; }
.table-modern tbody tr:hover { background-color: #f4f4f5 !important; }
.table-modern tbody td { padding: 1rem; color: #18181b; }
.loading-overlay {
  position: fixed;
  inset: 0;
  background: rgba(24, 24, 27, 0.7);
  backdrop-filter: blur(8px);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}
.loading-content {
  text-align: center;
  color: #fff;
}
.loading-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 1.5rem;
  position: relative;
}
.loading-icon .spinner-ring {
  position: absolute;
  width: 100%;
  height: 100%;
  border: 4px solid rgba(255,255,255,0.1);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}
.loading-icon .spinner-ring:nth-child(2) {
  width: 60px;
  height: 60px;
  top: 10px;
  left: 10px;
  border-top-color: #059669;
  animation-duration: 0.8s;
  animation-direction: reverse;
}
.loading-icon .spinner-ring:nth-child(3) {
  width: 40px;
  height: 40px;
  top: 20px;
  left: 20px;
  border-top-color: #fbbf24;
  animation-duration: 0.6s;
}
.loading-icon i {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 1.5rem;
  color: #fff;
  animation: pulse 1.5s ease-in-out infinite;
}
@keyframes spin {
  to { transform: rotate(360deg); }
}
@keyframes pulse {
  0%, 100% { opacity: 0.5; transform: translate(-50%, -50%) scale(1); }
  50% { opacity: 1; transform: translate(-50%, -50%) scale(1.1); }
}
.loading-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}
.loading-message {
  font-size: 0.9rem;
  opacity: 0.8;
  margin-bottom: 1rem;
}
.loading-progress {
  width: 280px;
  height: 8px;
  background: rgba(255,255,255,0.2);
  border-radius: 4px;
  margin: 0 auto 1rem;
  overflow: hidden;
}
.loading-progress-bar {
  height: 100%;
  background: linear-gradient(90deg, #059669, #10b981, #34d399);
  border-radius: 4px;
  transition: width 0.3s ease;
  width: 0%;
}
.loading-progress-bar.indeterminate {
  width: 100%;
  background-size: 200% 100%;
  animation: progress-flow 1.5s linear infinite;
}
@keyframes progress-flow {
  0% { background-position: 100% 0; }
  100% { background-position: -100% 0; }
}
.loading-percentage {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: #10b981;
}
.loading-detail {
  background: rgba(255,255,255,0.1);
  border-radius: 8px;
  padding: 1rem;
  margin: 1rem auto;
  max-width: 320px;
  text-align: left;
}
.loading-detail-row {
  display: flex;
  justify-content: space-between;
  padding: 0.25rem 0;
  font-size: 0.8rem;
  border-bottom: 1px solid rgba(255,255,255,0.1);
}
.loading-detail-row:last-child {
  border-bottom: none;
}
.loading-detail-label {
  opacity: 0.7;
}
.loading-detail-value {
  font-weight: 600;
  color: #10b981;
}
.loading-current-item {
  background: rgba(16, 185, 129, 0.2);
  border: 1px solid rgba(16, 185, 129, 0.3);
  border-radius: 6px;
  padding: 0.5rem 1rem;
  margin: 0.75rem auto;
  max-width: 320px;
  font-size: 0.75rem;
  font-family: monospace;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  animation: pulse-glow 1.5s ease-in-out infinite;
}
@keyframes pulse-glow {
  0%, 100% { box-shadow: 0 0 5px rgba(16, 185, 129, 0.3); }
  50% { box-shadow: 0 0 15px rgba(16, 185, 129, 0.6); }
}
.loading-steps {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 1.5rem;
}
.loading-step {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: rgba(255,255,255,0.3);
  transition: all 0.3s ease;
}
.loading-step.active {
  background: #fbbf24;
  box-shadow: 0 0 10px rgba(251, 191, 36, 0.5);
}
.loading-step.done {
  background: #10b981;
}
.loading-step-labels {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  margin-top: 0.75rem;
  font-size: 0.7rem;
  opacity: 0.7;
}
.loading-dots {
  display: inline-block;
}
.loading-dots span {
  animation: dots 1.4s infinite;
  opacity: 0;
}
.loading-dots span:nth-child(2) { animation-delay: 0.2s; }
.loading-dots span:nth-child(3) { animation-delay: 0.4s; }
@keyframes dots {
  0%, 60%, 100% { opacity: 0; }
  30% { opacity: 1; }
}
.badge.bg-label-info { background: #18181b !important; color: #fafafa !important; }
.empty-state {
  padding: 4rem 2rem;
  text-align: center;
  background: #fafafa;
  border-radius: 12px;
  border: 2px dashed #e4e4e7;
}
.empty-state i { font-size: 4rem; color: #a1a1aa; margin-bottom: 1rem; }
.empty-state p { color: #71717a; }
.file-icon {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background: #18181b;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #fafafa;
  margin-right: 12px;
}
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
<script>
document.addEventListener("DOMContentLoaded", function() {
    let statusCheckInterval = null;
    
    // Helper function untuk loading overlay
    function showLoading(type = 'db', message = 'Mohon tunggu', showProgress = false) {
        // Reset state
        $('#loading-progress-bar').removeClass('indeterminate').css('width', '0%');
        $('#loading-percentage').hide().text('0%');
        $('#loading-steps').hide();
        $('#loading-step-labels').hide();
        $('#loading-detail').hide();
        $('#loading-current-item').hide();
        $('#detail-tables, #detail-rows, #detail-files, #detail-size').hide();
        $('.loading-step').removeClass('active done');
        
        if (type === 'full') {
            $('#loading-type-icon').removeClass('ri-database-2-line').addClass('ri-folder-zip-line');
            $('#loading-title').text('Backup Lengkap');
            // Always show progress elements for full backup
            $('#loading-percentage').show();
            $('#loading-steps').show();
            $('#loading-step-labels').show();
            $('#loading-detail').show();
            $('#loading-current-item').show();
        } else {
            $('#loading-type-icon').removeClass('ri-folder-zip-line').addClass('ri-database-2-line');
            $('#loading-title').text('Backup Database');
            // Untuk backup DB, gunakan indeterminate progress
            $('#loading-progress-bar').addClass('indeterminate');
            // Show percentage for DB too so user sees updates
            $('#loading-percentage').show();
        }
        $('#loading-message').text(message);
        $('.loading-overlay').css('display', 'flex');
    }
    
    function updateProgress(progress, message, step, details = {}) {
        // Make sure percentage is always visible once we have data
        $('#loading-percentage').show().text(progress + '%');
        $('#loading-progress-bar').removeClass('indeterminate').css('width', progress + '%');
        if (message) {
            $('#loading-message').text(message);
        }
        
        // Update current item with appropriate icon
        if (details.current_table) {
            $('#current-item-icon').removeClass().addClass('ri-table-line me-1');
            $('#current-item-name').text(details.current_table);
            $('#loading-current-item').show();
        } else if (details.current_file) {
            // Determine icon based on file extension
            let icon = 'ri-file-line';
            const file = details.current_file.toLowerCase();
            if (file.match(/\.(jpg|jpeg|png|gif|webp|svg)$/)) icon = 'ri-image-line';
            else if (file.match(/\.(pdf)$/)) icon = 'ri-file-pdf-line';
            else if (file.match(/\.(doc|docx)$/)) icon = 'ri-file-word-line';
            else if (file.match(/\.(xls|xlsx)$/)) icon = 'ri-file-excel-line';
            
            $('#current-item-icon').removeClass().addClass(icon + ' me-1');
            $('#current-item-name').text(details.current_file);
            $('#loading-current-item').show();
        }
        
        // Update detail info based on step
        if (step === 'database' && details.total_tables) {
            $('#detail-tables').show();
            $('#detail-tables-value').text(details.processed_tables || 0);
            $('#detail-tables-total').text(details.total_tables);
            
            if (details.total_rows !== undefined) {
                $('#detail-rows').show();
                $('#detail-rows-value').text(details.total_rows.toLocaleString());
            }
        }
        
        if (step === 'storage' && details.total_files) {
            $('#detail-tables').hide();
            $('#detail-rows').hide();
            $('#detail-files').show();
            $('#detail-files-value').text(details.processed_files || 0);
            $('#detail-files-total').text(details.total_files);
            
            if (details.total_size) {
                $('#detail-size').show();
                $('#detail-size-value').text(details.total_size);
            }
        }
        
        // Update step indicators
        const steps = ['database', 'zip', 'storage', 'finalize', 'done'];
        const currentStepIndex = steps.indexOf(step);
        
        $('.loading-step').each(function(index) {
            const stepName = $(this).data('step');
            const stepIndex = steps.indexOf(stepName);
            
            if (stepIndex < currentStepIndex) {
                $(this).removeClass('active').addClass('done');
            } else if (stepIndex === currentStepIndex) {
                $(this).removeClass('done').addClass('active');
            } else {
                $(this).removeClass('active done');
            }
        });
    }
    
    function hideLoading() {
        if (statusCheckInterval) {
            clearInterval(statusCheckInterval);
            statusCheckInterval = null;
        }
        $('.loading-overlay').fadeOut(300);
    }

    // Backup Database (Quick) — sekarang async via queue
    $('#btn-backup-db').on('click', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Backup Database',
            text: 'Mulai backup database sekarang?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Backup!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#18181b',
            cancelButtonColor: '#8898aa',
            customClass: {
                confirmButton: 'btn btn-primary me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');
                showLoading('db', 'Backup database dijadwalkan...', false);
                
                $.ajax({
                    url: '{{ route("backup.create") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        type: 'db'
                    },
                    success: function(response) {
                        // Respons cepat (queued) — mulai polling status
                        if (response.queued) {
                            startProgressPolling(response.backup_id, function(finalResponse) {
                                hideLoading();
                                btn.prop('disabled', false).html('<i class="ri-database-2-line"></i> Backup Database');
                                Swal.fire({
                                    icon: finalResponse.status === 'completed' ? 'success' : 'error',
                                    title: finalResponse.status === 'completed' ? 'Berhasil!' : 'Gagal!',
                                    text: finalResponse.message || 'Backup database selesai.',
                                    timer: 2500,
                                    showConfirmButton: false
                                }).then(() => { location.reload(); });
                            });
                        } else {
                            // Fallback: respons langsung (tidak seharusnya terjadi)
                            hideLoading();
                            btn.prop('disabled', false).html('<i class="ri-database-2-line"></i> Backup Database');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Backup database berhasil dibuat.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => { location.reload(); });
                        }
                    },
                    error: function(xhr) {
                        hideLoading();
                        btn.prop('disabled', false).html('<i class="ri-database-2-line"></i> Backup Database');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat backup.',
                        });
                    }
                });
            }
        });
    });

    // Backup Lengkap (Database + Storage) - Queue
    $('#btn-backup-full').on('click', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Backup Lengkap',
            html: '<p>Backup ini akan mencakup:</p>' +
                  '<ul class="text-start"><li>Database SQL</li><li>Semua file di storage (gambar, PDF, dll)</li></ul>' +
                  '<p class="text-muted small">Proses berjalan di background, Anda bisa meninggalkan halaman ini.</p>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Backup Lengkap!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#059669',
            cancelButtonColor: '#8898aa',
            customClass: {
                confirmButton: 'btn me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false,
            didOpen: () => {
                $('.swal2-confirm').css({
                    'background': '#059669',
                    'border-color': '#059669',
                    'color': '#fff'
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');
                showLoading('full', 'Backup lengkap dijadwalkan...', true);
                
                $.ajax({
                    url: '{{ route("backup.create") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        type: 'full'
                    },
                    success: function(response) {
                        if (response.queued) {
                            // Mulai polling dengan backup_id dari response
                            startProgressPolling(response.backup_id, function(finalResponse) {
                                btn.prop('disabled', false).html('<i class="ri-folder-zip-line"></i> Backup Lengkap');
                                if (finalResponse.status === 'completed') {
                                    updateProgress(100, 'Backup selesai!', 'done');
                                    setTimeout(() => {
                                        hideLoading();
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: finalResponse.message || 'Backup lengkap selesai.',
                                            timer: 2500,
                                            showConfirmButton: false
                                        }).then(() => { location.reload(); });
                                    }, 500);
                                } else {
                                    hideLoading();
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Backup Gagal!',
                                        text: finalResponse.message || 'Terjadi kesalahan saat backup.',
                                    });
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        hideLoading();
                        btn.prop('disabled', false).html('<i class="ri-folder-zip-line"></i> Backup Lengkap');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat backup.',
                        });
                    }
                });
            }
        });
    });
    
    // Polling progress — backupId dikirim ke server agar status file ditemukan
    function startProgressPolling(backupId, onComplete) {
        if (statusCheckInterval) {
            clearInterval(statusCheckInterval);
        }
        statusCheckInterval = setInterval(function() {
            $.ajax({
                url: '{{ route("backup.status") }}',
                type: 'GET',
                data: { id: backupId },   // <-- kunci fix: kirim backup_id
                success: function(response) {
                    if (response.status && response.status !== 'idle') {
                        updateProgress(
                            response.progress || 0,
                            response.message || 'Memproses...', 
                            response.step || 'database',
                            response.details || {}
                        );
                    }
                    
                    // Stop polling jika sudah selesai atau gagal
                    if (response.status === 'completed' || response.status === 'failed') {
                        clearInterval(statusCheckInterval);
                        statusCheckInterval = null;
                        if (typeof onComplete === 'function') {
                            onComplete(response);
                        }
                    }
                },
                error: function() {
                    // Abaikan error jaringan sementara, tetap polling
                }
            });
        }, 1000); // Poll setiap 1 detik
    }

    // Event DELETE dengan konfirmasi modern - HANYA 2 BUTTON
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const form = $(this).closest('form');
        const filename = form.data('filename');

        Swal.fire({
            title: 'Konfirmasi Penghapusan',
            text: 'Yakin ingin menghapus backup ini? Data tidak dapat dikembalikan!',
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
                showLoading('db', 'Menghapus file backup');
                
                setTimeout(() => {
                    hideLoading();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Backup berhasil dihapus.',
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
    <div class="loading-content">
        <div class="loading-icon">
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <i class="ri-database-2-line" id="loading-type-icon"></i>
        </div>
        <div class="loading-percentage" id="loading-percentage" style="display: none;">0%</div>
        <div class="loading-title" id="loading-title">Memproses Backup</div>
        <div class="loading-message">
            <span id="loading-message">Mohon tunggu</span>
            <span class="loading-dots"><span>.</span><span>.</span><span>.</span></span>
        </div>
        
        <!-- Current item being processed -->
        <div class="loading-current-item" id="loading-current-item" style="display: none;">
            <i class="ri-table-line me-1" id="current-item-icon"></i><span id="current-item-name">-</span>
        </div>
        
        <div class="loading-progress">
            <div class="loading-progress-bar" id="loading-progress-bar"></div>
        </div>
        
        <!-- Detail info -->
        <div class="loading-detail" id="loading-detail" style="display: none;">
            <div class="loading-detail-row" id="detail-tables" style="display: none;">
                <span class="loading-detail-label">Tabel</span>
                <span class="loading-detail-value"><span id="detail-tables-value">0</span> / <span id="detail-tables-total">0</span></span>
            </div>
            <div class="loading-detail-row" id="detail-rows" style="display: none;">
                <span class="loading-detail-label">Total Baris</span>
                <span class="loading-detail-value" id="detail-rows-value">0</span>
            </div>
            <div class="loading-detail-row" id="detail-files" style="display: none;">
                <span class="loading-detail-label">File</span>
                <span class="loading-detail-value"><span id="detail-files-value">0</span> / <span id="detail-files-total">0</span></span>
            </div>
            <div class="loading-detail-row" id="detail-size" style="display: none;">
                <span class="loading-detail-label">Ukuran</span>
                <span class="loading-detail-value" id="detail-size-value">0 B</span>
            </div>
        </div>
        
        <!-- Step indicators untuk backup lengkap -->
        <div class="loading-steps" id="loading-steps" style="display: none;">
            <div class="loading-step" data-step="database" title="Database"></div>
            <div class="loading-step" data-step="zip" title="ZIP"></div>
            <div class="loading-step" data-step="storage" title="Storage"></div>
            <div class="loading-step" data-step="finalize" title="Selesai"></div>
        </div>
        <div class="loading-step-labels" id="loading-step-labels" style="display: none;">
            <span>Database</span>
            <span>ZIP</span>
            <span>Storage</span>
            <span>Selesai</span>
        </div>
    </div>
</div>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <!-- Backup Database Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header-custom">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 fw-bold">
                                <i class="ri-database-2-line me-2"></i>Backup Database
                            </h4>
                            <p class="mb-0 opacity-75 small">Kelola backup database dan file storage sistem</p>
                        </div>
                        <div class="d-flex gap-2 mt-3 mt-md-0">
                            <button type="button" id="btn-backup-db" class="btn btn-primary btn-add">
                                <i class="ri-database-2-line"></i>
                                Backup Database
                            </button>
                            <button type="button" id="btn-backup-full" class="btn btn-add" style="background: #059669 !important; border-color: #059669 !important;">
                                <i class="ri-folder-zip-line"></i>
                                Backup Lengkap
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <!-- Info Cards -->
                    <div class="row mx-3 mt-3 g-3">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3" style="background: #f8fafc; border-color: #e4e4e7 !important;">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="rounded-circle p-2 me-2" style="background: #18181b;">
                                        <i class="ri-database-2-line text-white"></i>
                                    </div>
                                    <strong>Backup Database</strong>
                                </div>
                                <small class="text-muted">
                                    Backup cepat hanya untuk database SQL. Cocok untuk backup harian atau sebelum update sistem.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3" style="background: #f0fdf4; border-color: #bbf7d0 !important;">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="rounded-circle p-2 me-2" style="background: #059669;">
                                        <i class="ri-folder-zip-line text-white"></i>
                                    </div>
                                    <strong>Backup Lengkap</strong>
                                </div>
                                <small class="text-muted">
                                    Backup database + semua file di storage (gambar, PDF, dokumen). Proses berjalan di background.
                                </small>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success mx-3 mt-3 mb-0">
                            <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger mx-3 mt-3 mb-0">
                            <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @if(count($files) > 0)
                        <div class="table-responsive p-3">
                            <table class="table table-modern table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="ri-hashtag me-1"></i>No</th>
                                        <th><i class="ri-file-line me-1"></i>Nama File</th>
                                        <th><i class="ri-price-tag-3-line me-1"></i>Tipe</th>
                                        <th><i class="ri-file-info-line me-1"></i>Ukuran</th>
                                        <th class="text-center"><i class="ri-settings-3-line me-1"></i>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($files as $index => $file)
                                        @php
                                            $size = round($file->getSize() / 1024 / 1024, 2) . ' MB';
                                            $filename = $file->getFilename();
                                            $isFullBackup = str_contains($filename, 'backup-full') || str_contains($filename, 'full_backup') || str_contains($filename, '_full_');
                                        @endphp
                                        <tr>
                                            <td class="fw-bold">{{ $index + 1 }}</td>
                                            
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="file-icon" style="{{ $isFullBackup ? 'background: #059669;' : '' }}">
                                                        <i class="{{ $isFullBackup ? 'ri-folder-zip-line' : 'ri-file-zip-line' }}" style="font-size: 1.25rem;"></i>
                                                    </div>
                                                    <div>
                                                        <span class="fw-semibold d-block">{{ $filename }}</span>
                                                        <small class="text-muted">
                                                            <i class="ri-time-line me-1"></i>
                                                            {{ date('d M Y H:i', $file->getMTime()) }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                @if($isFullBackup)
                                                    <span class="badge" style="background: #059669; padding: 6px 12px;">
                                                        <i class="ri-folder-zip-line me-1"></i>Lengkap
                                                    </span>
                                                @else
                                                    <span class="badge" style="background: #18181b; padding: 6px 12px;">
                                                        <i class="ri-database-2-line me-1"></i>Database
                                                    </span>
                                                @endif
                                            </td>
                                            
                                            <td>
                                                <span class="badge bg-label-info" style="padding: 8px 16px; font-size: 0.8rem;">
                                                    <i class="ri-hard-drive-line me-1"></i>{{ $size }}
                                                </span>
                                            </td>
                                            
                                            <td>
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <a href="{{ route('backup.download', $file->getFilename()) }}" 
                                                       class="btn btn-sm btn-outline-success"
                                                       title="Download">
                                                        <i class="ri-download-2-line"></i>
                                                    </a>

                                                    <form action="{{ route('backup.delete', $file->getFilename()) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          data-filename="{{ $file->getFilename() }}">
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
                    @else
                        <div class="p-4">
                            <div class="empty-state">
                                <i class="ri-database-2-line d-block"></i>
                                <p class="mb-0">Belum ada backup</p>
                                <small class="text-muted">Klik tombol "Backup Database" atau "Backup Lengkap" untuk membuat backup</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection