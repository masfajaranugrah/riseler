@extends('layouts/layoutMaster')

@section('title', 'Update Progress Ticket - Teknisi')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/flatpickr/flatpickr.js'])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const attachmentInput = document.getElementById('attachment');
    const preview = document.getElementById('preview');
    const uploadArea = document.getElementById('upload-area');

    // Image preview with compression
    attachmentInput?.addEventListener('change', function(event) {
        const file = this.files[0];
        if (!file) {
            preview.style.display = 'none';
            uploadArea.classList.remove('has-image');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.src = e.target.result;

            img.onload = function() {
                const canvas = document.createElement('canvas');
                const maxWidth = 1024, maxHeight = 1024;
                let width = img.width, height = img.height;

                if (width > height) {
                    if (width > maxWidth) {
                        height *= maxWidth / width;
                        width = maxWidth;
                    }
                } else {
                    if (height > maxHeight) {
                        width *= maxHeight / height;
                        height = maxHeight;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob(function(blob) {
                    const newFile = new File([blob], file.name, { type: 'image/jpeg', lastModified: Date.now() });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(newFile);
                    attachmentInput.files = dataTransfer.files;

                    preview.src = URL.createObjectURL(newFile);
                    preview.style.display = 'block';
                    uploadArea.classList.add('has-image');
                }, 'image/jpeg', 0.7);
            }
        }
        reader.readAsDataURL(file);
    });
});
</script>
@endsection

@section('content')
<div class="app-update-ticket">
    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="alert-modern alert-error mb-4">
            <div class="alert-icon">?</div>
            <div class="alert-content">
                <h6 class="alert-title">Terjadi Kesalahan</h6>
                <ul class="alert-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('jobs.update', $ticket->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Header Section --}}
        <div class="page-header">
            <div class="header-content">
                <div class="header-text">
                    <h4 class="page-title">Update Progress Ticket</h4>
                    <p class="page-subtitle">Ticket #{{ $ticket->id }} · {{ $ticket->pelanggan->nama_lengkap }}</p>
                </div>
                <div class="header-actions">
                    <a href="{{ route('jobs.index') }}" class="btn-modern-outline">
                        Kembali
                    </a>
                    <button type="submit" class="btn-modern-primary">
                        Simpan Progress
                    </button>
                </div>
            </div>
        </div>

        <div class="form-layout-update">
            {{-- Left Column - Ticket Details --}}
            <div class="form-main">
                <div class="card-modern-form">
                    <div class="card-header-form">
                        <h5 class="card-title-form">Detail Ticket</h5>
                        <span class="status-badge-modern status-{{ $ticket->status }}">
                            {{ ucfirst($ticket->status) }}
                        </span>
                    </div>
                    <div class="card-body-form">
                        {{-- Customer Info --}}
                        <div class="info-grid">
                            <div class="info-item-modern">
                                <span class="info-label-modern">Customer</span>
                                <span class="info-value-modern">{{ $ticket->pelanggan->nama_lengkap }}</span>
                            </div>
                            <div class="info-item-modern">
                                <span class="info-label-modern">No. Telepon</span>
                                <span class="info-value-modern">{{ $ticket->phone }}</span>
                            </div>
                        </div>

                        <div class="divider-modern"></div>

                        {{-- Ticket Details --}}
                        <div class="form-group">
                            <label class="form-label-modern">Kategori</label>
                            <div class="read-only-value">{{ ucfirst($ticket->category) }}</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label-modern">Prioritas</label>
                            <div class="priority-badge priority-{{ $ticket->priority }}">
                                @if($ticket->priority == 'urgent') @elseif($ticket->priority == 'medium')  @else  @endif
                                {{ ucfirst($ticket->priority) }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label-modern">Deskripsi Kendala</label>
                            <div class="description-box">{{ $ticket->issue_description }}</div>
                        </div>

                        @if($ticket->location_link)
                            <div class="form-group">
                                <label class="form-label-modern">Lokasi</label>
                                <a href="{{ $ticket->location_link }}" target="_blank" class="link-modern-button">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Buka Lokasi di Maps
                                </a>
                            </div>
                        @endif

                        @if($ticket->additional_note)
                            <div class="form-group">
                                <label class="form-label-modern">Catatan Tambahan</label>
                                <div class="note-box">{{ $ticket->additional_note }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column - Update Form --}}
            <div class="form-sidebar">
                {{-- Status Update --}}
                <div class="card-modern-form">
                    <div class="card-header-form">
                        <h5 class="card-title-form">Update Status</h5>
                    </div>
                    <div class="card-body-form">
                        <div class="form-group">
                            <label class="form-label-modern">
                                Status Ticket
                                <span class="label-required">*</span>
                            </label>
                            <select name="status" class="form-control-modern" required>
                                <option value="pending" {{ $ticket->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="progress" {{ $ticket->status == 'progress' ? 'selected' : '' }}>On Progress</option>
                                <option value="finished" {{ $ticket->status == 'finished' ? 'selected' : '' }}>Finished</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label-modern">Catatan Teknisi</label>
                            <textarea name="technician_note" class="form-control-modern" rows="4" placeholder="Tuliskan hasil pekerjaan, kendala, atau rekomendasi...">{{ old('technician_note', $ticket->technician_note) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Photo Upload --}}
                <div class="card-modern-form">
                    <div class="card-header-form">
                        <h5 class="card-title-form">Bukti Foto</h5>
                        <span class="card-badge-small">Optional</span>
                    </div>
                    <div class="card-body-form">
                        <div class="upload-area" id="upload-area">
                            <input type="file" class="upload-input" name="technician_attachment" id="attachment" accept="image/*">
                            <label for="attachment" class="upload-label">
                                @if($ticket->technician_attachment)
                                    <img id="preview" src="{{ asset('storage/' . $ticket->technician_attachment) }}" class="upload-preview">
                                @else
                                    <div class="upload-placeholder">
                                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="upload-text">Klik untuk upload foto</span>
                                        <span class="upload-hint">JPG, PNG . Max 2MB</span>
                                    </div>
                                    <img id="preview" src="#" class="upload-preview" style="display:none;">
                                @endif
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="action-buttons-sticky">
                    <button type="submit" class="btn-modern-primary w-100">
                        Simpan Progress
                    </button>
                    <a href="{{ route('jobs.index') }}" class="btn-modern-outline w-100">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
/* Modern Black & White Theme - Update Ticket */
:root {
    --color-black: #000000;
    --color-white: #ffffff;
    --color-gray-50: #fafafa;
    --color-gray-100: #f5f5f5;
    --color-gray-200: #e5e5e5;
    --color-gray-300: #d4d4d4;
    --color-gray-400: #a3a3a3;
    --color-gray-500: #737373;
    --color-gray-600: #525252;
    --color-gray-700: #404040;
    --color-gray-800: #262626;
    --color-gray-900: #171717;
}

.app-update-ticket {
    max-width: 1400px;
    margin: 0 auto;
    padding: 32px 20px;
}

/* Alert Modern */
.alert-modern {
    display: flex;
    gap: 16px;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid;
}

.alert-error {
    background: var(--color-gray-50);
    border-color: var(--color-gray-300);
}

.alert-icon {
    font-size: 24px;
    line-height: 1;
}

.alert-content {
    flex: 1;
}

.alert-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--color-black);
    margin: 0 0 8px 0;
}

.alert-list {
    margin: 0;
    padding-left: 20px;
    color: var(--color-gray-700);
    font-size: 14px;
}

/* Page Header */
.page-header {
    margin-bottom: 32px;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
    padding-bottom: 24px;
    border-bottom: 2px solid var(--color-gray-200);
}

.header-text {
    flex: 1;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--color-black);
    margin: 0 0 6px 0;
    letter-spacing: -0.5px;
}

.page-subtitle {
    font-size: 14px;
    color: var(--color-gray-500);
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 12px;
}

/* Form Layout */
.form-layout-update {
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: 24px;
}

@media (max-width: 1024px) {
    .form-layout-update {
        grid-template-columns: 1fr;
    }
}

/* Card Modern */
.card-modern-form {
    background: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 24px;
}

.card-header-form {
    padding: 20px 24px;
    background: var(--color-gray-50);
    border-bottom: 1px solid var(--color-gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-title-form {
    font-size: 16px;
    font-weight: 600;
    color: var(--color-black);
    margin: 0;
}

.card-badge-small {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--color-gray-500);
    background: var(--color-gray-200);
    padding: 3px 8px;
    border-radius: 4px;
    font-weight: 600;
}

.card-body-form {
    padding: 24px;
}

/* Status Badge */
.status-badge-modern {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
}

.status-pending {
    background: var(--color-gray-200);
    color: var(--color-gray-700);
}

.status-progress {
    background: var(--color-gray-700);
    color: var(--color-white);
}

.status-finished {
    background: var(--color-black);
    color: var(--color-white);
}

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

@media (max-width: 640px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
}

.info-item-modern {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.info-label-modern {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--color-gray-500);
    font-weight: 600;
}

.info-value-modern {
    font-size: 14px;
    color: var(--color-gray-900);
    font-weight: 500;
}

/* Form Elements */
.form-group {
    margin-bottom: 20px;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-label-modern {
    display: block;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--color-gray-600);
    margin-bottom: 8px;
    font-weight: 600;
}

.label-required {
    color: var(--color-gray-900);
}

.form-control-modern {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--color-gray-200);
    border-radius: 8px;
    font-size: 14px;
    color: var(--color-gray-900);
    background: var(--color-white);
    transition: all 0.2s ease;
}

.form-control-modern:focus {
    outline: none;
    border-color: var(--color-black);
    background: var(--color-gray-50);
}

textarea.form-control-modern {
    resize: vertical;
    font-family: inherit;
    line-height: 1.6;
}

.read-only-value {
    padding: 12px 16px;
    background: var(--color-gray-100);
    border-radius: 8px;
    color: var(--color-gray-700);
    font-size: 14px;
}

.description-box {
    padding: 16px;
    background: var(--color-gray-50);
    border: 1px solid var(--color-gray-200);
    border-radius: 8px;
    color: var(--color-gray-800);
    font-size: 14px;
    line-height: 1.6;
}

.note-box {
    padding: 12px 16px;
    background: var(--color-gray-100);
    border-left: 3px solid var(--color-gray-400);
    border-radius: 4px;
    color: var(--color-gray-700);
    font-size: 13px;
    font-style: italic;
}

/* Priority Badge */
.priority-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
}

.priority-urgent {
    background: var(--color-gray-900);
    color: var(--color-white);
}

.priority-medium {
    background: var(--color-gray-300);
    color: var(--color-gray-900);
}

.priority-low {
    background: var(--color-gray-100);
    color: var(--color-gray-700);
}

/* Upload Area */
.upload-area {
    position: relative;
    border: 2px dashed var(--color-gray-300);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s ease;
}

.upload-area:hover {
    border-color: var(--color-gray-400);
    background: var(--color-gray-50);
}

.upload-area.has-image {
    border-style: solid;
    border-color: var(--color-gray-300);
}

.upload-input {
    position: absolute;
    width: 0;
    height: 0;
    opacity: 0;
}

.upload-label {
    display: block;
    cursor: pointer;
    margin: 0;
}

.upload-placeholder {
    padding: 48px 24px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}

.upload-placeholder svg {
    color: var(--color-gray-400);
}

.upload-text {
    font-size: 14px;
    font-weight: 600;
    color: var(--color-gray-700);
}

.upload-hint {
    font-size: 12px;
    color: var(--color-gray-500);
}

.upload-preview {
    width: 100%;
    height: auto;
    display: block;
}

/* Link Button */
.link-modern-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: var(--color-gray-100);
    border: 1px solid var(--color-gray-300);
    border-radius: 8px;
    color: var(--color-gray-900);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.link-modern-button:hover {
    background: var(--color-gray-200);
    border-color: var(--color-gray-400);
}

/* Buttons */
.btn-modern-primary {
    padding: 12px 24px;
    background: var(--color-black);
    color: var(--color-white);
    border: 2px solid var(--color-black);
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-modern-primary:hover {
    background: var(--color-gray-800);
    border-color: var(--color-gray-800);
    transform: translateY(-1px);
}

.btn-modern-outline {
    padding: 12px 24px;
    background: transparent;
    color: var(--color-gray-900);
    border: 2px solid var(--color-gray-300);
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-modern-outline:hover {
    border-color: var(--color-black);
    background: var(--color-gray-50);
}

.w-100 {
    width: 100%;
}

.action-buttons-sticky {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.divider-modern {
    height: 1px;
    background: var(--color-gray-200);
    margin: 24px 0;
}

/* Responsive */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
    }
    
    .header-actions {
        width: 100%;
    }
    
    .header-actions a,
    .header-actions button {
        flex: 1;
    }
}
</style>
@endsection
