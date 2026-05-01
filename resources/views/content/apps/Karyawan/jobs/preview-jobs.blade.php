@extends('layouts/layoutMaster')

@section('title', 'Preview Ticket - Teknisi')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/flatpickr/flatpickr.js'])
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Preview foto teknisi
    const preview = document.getElementById('preview');
    if(preview && preview.src === '') preview.style.display = 'none';
});
</script>
@endsection

@section('content')
<div class="app-preview-ticket">
    {{-- Header Section --}}
    <div class="page-header-preview">
        <div class="header-content-preview">
            <div class="header-text-preview">
                <h4 class="page-title-preview">Detail Ticket</h4>
                <p class="page-subtitle-preview">Ticket #{{ $ticket->id }} . {{ $ticket->pelanggan->nama_lengkap }}</p>
            </div>
            <div class="status-badge-large status-{{ $ticket->status }}">
                {{ ucfirst($ticket->status) }}
            </div>
        </div>
        <a href="{{ route('jobs.approved') }}" class="btn-back-preview">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <div class="preview-layout">
        {{-- Left Column - Customer & Ticket Info --}}
        <div class="preview-main">
            {{-- Customer Information --}}
            <div class="card-preview">
                <div class="card-header-preview">
                    <div class="card-header-content">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <h5 class="card-title-preview">Informasi Customer</h5>
                    </div>
                </div>
                <div class="card-body-preview">
                    <div class="info-grid-preview">
                        <div class="info-item-preview">
                            <span class="info-label-preview">Nama Customer</span>
                            <span class="info-value-preview">{{ $ticket->pelanggan->nama_lengkap }}</span>
                        </div>
                        <div class="info-item-preview">
                            <span class="info-label-preview">No. Telepon</span>
                            <span class="info-value-preview">{{ $ticket->phone }}</span>
                        </div>
                    </div>

                    @if($ticket->location_link)
                        <div class="location-link-preview">
                            <span class="info-label-preview">Lokasi</span>
                            <a href="{{ $ticket->location_link }}" target="_blank" class="link-button-preview">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Buka di Google Maps
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Ticket Details --}}
            <div class="card-preview">
                <div class="card-header-preview">
                    <div class="card-header-content">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h5 class="card-title-preview">Detail Kendala</h5>
                    </div>
                </div>
                <div class="card-body-preview">
                    <div class="detail-row-preview">
                        <div class="detail-item-preview">
                            <span class="detail-label-preview">Kategori</span>
                            <div class="category-badge-preview">{{ ucfirst($ticket->category) }}</div>
                        </div>
                        <div class="detail-item-preview">
                            <span class="detail-label-preview">Prioritas</span>
                            <div class="priority-badge-preview priority-{{ $ticket->priority }}">
                                @if($ticket->priority == 'urgent')  @elseif($ticket->priority == 'medium')  @else  @endif
                                {{ ucfirst($ticket->priority) }}
                            </div>
                        </div>
                    </div>

                    <div class="description-section-preview">
                        <span class="info-label-preview">Deskripsi Kendala</span>
                        <div class="description-box-preview">{{ $ticket->issue_description }}</div>
                    </div>

                    @if($ticket->additional_note)
                        <div class="note-section-preview">
                            <span class="info-label-preview">Catatan Tambahan</span>
                            <div class="note-box-preview">{{ $ticket->additional_note }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Technician Work --}}
            <div class="card-preview">
                <div class="card-header-preview">
                    <div class="card-header-content">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h5 class="card-title-preview">Hasil Pekerjaan</h5>
                    </div>
                </div>
                <div class="card-body-preview">
                    @if($ticket->technician_note)
                        <div class="technician-note-preview">
                            <span class="info-label-preview">Catatan Teknisi</span>
                            <div class="work-note-box-preview">{{ $ticket->technician_note }}</div>
                        </div>
                    @else
                        <div class="empty-note-preview">
                            Belum ada catatan dari teknisi
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column - Photo & Meta Info --}}
        <div class="preview-sidebar">
            {{-- Photo Evidence --}}
            @if($ticket->technician_attachment)
                <div class="card-preview">
                    <div class="card-header-preview">
                        <div class="card-header-content">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <h5 class="card-title-preview">Bukti Foto</h5>
                        </div>
                    </div>
                    <div class="card-body-preview">
                        <div class="photo-container-preview">
                            <img id="preview" src="{{ asset('storage/' . $ticket->technician_attachment) }}" class="photo-preview" alt="Bukti Foto">
                            <a href="{{ asset('storage/' . $ticket->technician_attachment) }}" target="_blank" class="photo-overlay-preview">
                                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                </svg>
                                <span>Lihat Detail</span>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="card-preview">
                    <div class="card-header-preview">
                        <div class="card-header-content">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <h5 class="card-title-preview">Bukti Foto</h5>
                        </div>
                    </div>
                    <div class="card-body-preview">
                        <div class="no-photo-preview">
                            <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p>Tidak ada bukti foto</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Meta Information --}}
            <div class="card-preview">
                <div class="card-header-preview">
                    <div class="card-header-content">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h5 class="card-title-preview">Informasi Ticket</h5>
                    </div>
                </div>
                <div class="card-body-preview">
                    <div class="meta-list-preview">
                        <div class="meta-row-preview">
                            <span class="meta-label-preview">Teknisi</span>
                            <span class="meta-value-preview">{{ $ticket->user->name ?? '-' }}</span>
                        </div>
                        <div class="meta-row-preview">
                            <span class="meta-label-preview">Dibuat Oleh</span>
                            <span class="meta-value-preview">{{ $ticket->creator->name ?? '-' }}</span>
                        </div>
                        <div class="meta-row-preview">
                            <span class="meta-label-preview">Tanggal Dibuat</span>
                            <span class="meta-value-preview">{{ $ticket->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        @if($ticket->updated_at != $ticket->created_at)
                            <div class="meta-row-preview">
                                <span class="meta-label-preview">Terakhir Update</span>
                                <span class="meta-value-preview">{{ $ticket->updated_at->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Action Button --}}
            <a href="{{ route('jobs.approved') }}" class="btn-full-preview">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

<style>
/* Modern Black & White Theme - Preview Ticket */
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

.app-preview-ticket {
    max-width: 1400px;
    margin: 0 auto;
    padding: 32px 20px;
}

/* Page Header */
.page-header-preview {
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 2px solid var(--color-gray-200);
}

.header-content-preview {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
    margin-bottom: 16px;
}

.header-text-preview {
    flex: 1;
}

.page-title-preview {
    font-size: 32px;
    font-weight: 700;
    color: var(--color-black);
    margin: 0 0 6px 0;
    letter-spacing: -0.5px;
}

.page-subtitle-preview {
    font-size: 15px;
    color: var(--color-gray-500);
    margin: 0;
}

.status-badge-large {
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge-large.status-finished {
    background: var(--color-black);
    color: var(--color-white);
}

.status-badge-large.status-progress {
    background: var(--color-gray-700);
    color: var(--color-white);
}

.status-badge-large.status-pending {
    background: var(--color-gray-200);
    color: var(--color-gray-700);
}

.status-badge-large.status-assigned {
    background: var(--color-gray-300);
    color: var(--color-gray-900);
}

.status-badge-large.status-approved {
    background: var(--color-black);
    color: var(--color-white);
}

.btn-back-preview {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: transparent;
    color: var(--color-gray-700);
    border: 2px solid var(--color-gray-300);
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-back-preview:hover {
    border-color: var(--color-black);
    background: var(--color-gray-50);
    color: var(--color-black);
}

/* Layout */
.preview-layout {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 24px;
}

@media (max-width: 1024px) {
    .preview-layout {
        grid-template-columns: 1fr;
    }
}

.preview-main {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.preview-sidebar {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* Card */
.card-preview {
    background: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: 12px;
    overflow: hidden;
}

.card-header-preview {
    padding: 20px 24px;
    background: var(--color-gray-50);
    border-bottom: 1px solid var(--color-gray-200);
}

.card-header-content {
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header-content svg {
    color: var(--color-gray-600);
}

.card-title-preview {
    font-size: 16px;
    font-weight: 600;
    color: var(--color-black);
    margin: 0;
}

.card-body-preview {
    padding: 24px;
}

/* Info Grid */
.info-grid-preview {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 20px;
}

@media (max-width: 640px) {
    .info-grid-preview {
        grid-template-columns: 1fr;
    }
}

.info-item-preview {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.info-label-preview {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--color-gray-500);
    font-weight: 600;
}

.info-value-preview {
    font-size: 15px;
    color: var(--color-gray-900);
    font-weight: 500;
}

/* Location Link */
.location-link-preview {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.link-button-preview {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: var(--color-gray-100);
    border: 1px solid var(--color-gray-300);
    border-radius: 8px;
    color: var(--color-gray-900);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.link-button-preview:hover {
    background: var(--color-gray-200);
    border-color: var(--color-gray-400);
}

/* Detail Row */
.detail-row-preview {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

@media (max-width: 640px) {
    .detail-row-preview {
        grid-template-columns: 1fr;
    }
}

.detail-item-preview {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.detail-label-preview {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--color-gray-500);
    font-weight: 600;
}

.category-badge-preview {
    display: inline-block;
    padding: 8px 14px;
    background: var(--color-gray-100);
    border: 1px solid var(--color-gray-300);
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    color: var(--color-gray-900);
    width: fit-content;
}

.priority-badge-preview {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    width: fit-content;
}

.priority-badge-preview.priority-urgent {
    background: var(--color-gray-900);
    color: var(--color-white);
}

.priority-badge-preview.priority-medium {
    background: var(--color-gray-300);
    color: var(--color-gray-900);
}

.priority-badge-preview.priority-low {
    background: var(--color-gray-100);
    color: var(--color-gray-700);
}

/* Description & Notes */
.description-section-preview,
.note-section-preview,
.technician-note-preview {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 20px;
}

.description-section-preview:last-child,
.note-section-preview:last-child,
.technician-note-preview:last-child {
    margin-bottom: 0;
}

.description-box-preview {
    padding: 16px;
    background: var(--color-gray-50);
    border: 1px solid var(--color-gray-200);
    border-radius: 8px;
    color: var(--color-gray-800);
    font-size: 14px;
    line-height: 1.6;
}

.note-box-preview {
    padding: 14px 16px;
    background: var(--color-gray-100);
    border-left: 3px solid var(--color-gray-400);
    border-radius: 4px;
    color: var(--color-gray-700);
    font-size: 14px;
    font-style: italic;
    line-height: 1.5;
}

.work-note-box-preview {
    padding: 16px;
    background: var(--color-gray-50);
    border: 1px solid var(--color-gray-300);
    border-radius: 8px;
    color: var(--color-gray-800);
    font-size: 14px;
    line-height: 1.6;
}

.empty-note-preview {
    padding: 32px;
    text-align: center;
    color: var(--color-gray-400);
    font-size: 14px;
    font-style: italic;
}

/* Photo Container */
.photo-container-preview {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    background: var(--color-gray-100);
}

.photo-preview {
    width: 100%;
    height: auto;
    display: block;
}

.photo-overlay-preview {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    opacity: 0;
    transition: opacity 0.3s ease;
    text-decoration: none;
    color: var(--color-white);
    font-weight: 600;
    font-size: 14px;
}

.photo-container-preview:hover .photo-overlay-preview {
    opacity: 1;
}

.no-photo-preview {
    padding: 60px 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    color: var(--color-gray-400);
}

.no-photo-preview svg {
    color: var(--color-gray-300);
}

.no-photo-preview p {
    margin: 0;
    font-size: 14px;
}

/* Meta List */
.meta-list-preview {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.meta-row-preview {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--color-gray-100);
}

.meta-row-preview:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.meta-label-preview {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--color-gray-500);
    font-weight: 600;
}

.meta-value-preview {
    font-size: 14px;
    color: var(--color-gray-900);
    font-weight: 500;
    text-align: right;
}

/* Full Button */
.btn-full-preview {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 24px;
    background: var(--color-black);
    color: var(--color-white);
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-full-preview:hover {
    background: var(--color-gray-800);
    transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 768px) {
    .header-content-preview {
        flex-direction: column;
    }
    
    .status-badge-large {
        align-self: flex-start;
    }
}
</style>
@endsection
