@extends('layouts/layoutMaster')

@section('title', 'Daftar Ticket Teknisi')

@section('content')
<div class="app-job-list">
    {{-- Header Section --}}
    <div class="page-header-job">
        <div class="header-content-job">
            <h4 class="page-title-job">Daftar Ticket</h4>
            <p class="page-subtitle-job">Kelola dan pantau semua tugas Anda</p>
        </div>
        <div class="header-stats">
            @php
                $totalTickets = 0;
                foreach(['urgent', 'medium', 'low'] as $level) {
                    if(isset($tickets[$level])) {
                        $totalTickets += $tickets[$level]->count();
                    }
                }
            @endphp
            <div class="stat-item">
                <span class="stat-label">Total Jobs</span>
                <span class="stat-value">{{ $totalTickets }}</span>
            </div>
        </div>
    </div>

    @php
        $hasTickets = false;
        foreach(['urgent', 'medium', 'low'] as $level) {
            if(isset($tickets[$level]) && $tickets[$level]->count()) {
                $hasTickets = true;
                break;
            }
        }
    @endphp

    @if($hasTickets)
        @foreach(['urgent', 'medium', 'low'] as $level)
            @if(isset($tickets[$level]) && $tickets[$level]->count())
                {{-- Section Header --}}
                <div class="section-header-job">
                    <div class="section-title-container">
                        <h5 class="section-title-job">{{ ucfirst($level) }}</h5>
                        <span class="section-count-job">{{ $tickets[$level]->count() }} Ticket</span>
                    </div>
                    <div class="priority-indicator priority-{{ $level }}"></div>
                </div>

                {{-- Ticket Grid --}}
                <div class="ticket-grid">
                    @foreach($tickets[$level] as $ticket)
                        <div class="ticket-card-modern">
                            {{-- Card Header --}}
                            <div class="ticket-card-header">
                                <div class="customer-info">
                                    <div class="customer-avatar">
                                        {{ strtoupper(substr($ticket->customer_name, 0, 2)) }}
                                    </div>
                                    <div class="customer-details">
                                        <h6 class="customer-name-job">{{ $ticket->customer_name }}</h6>
                                        <span class="ticket-date">{{ $ticket->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                                <span class="status-pill status-{{ $ticket->status }}">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </div>

                            {{-- Card Body --}}
                            <div class="ticket-card-body">
                                <div class="ticket-description">
                                    <p class="description-text">
                                        {{ strlen($ticket->issue_description) > 80 ? substr($ticket->issue_description, 0, 80) . '...' : $ticket->issue_description }}
                                    </p>
                                </div>

                                @if($ticket->additional_note)
                                    <div class="ticket-note">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                        <span>{{ strlen($ticket->additional_note) > 60 ? substr($ticket->additional_note, 0, 60) . '...' : $ticket->additional_note }}</span>
                                    </div>
                                @endif

                                <div class="ticket-meta">
                                    <div class="meta-item">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>{{ $ticket->user->name ?? 'Unassigned' }}</span>
                                    </div>
                                    
                                    @if($ticket->location_link)
                                        <a href="{{ $ticket->location_link }}" target="_blank" class="meta-item meta-link">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span>Lokasi</span>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Card Footer --}}
                            <div class="ticket-card-footer">
                                @if($ticket->attachment)
                                    <a href="{{ asset('storage/' . $ticket->attachment) }}" target="_blank" class="btn-ticket-secondary">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        Foto
                                    </a>
                                @endif
                                <a href="{{ route('jobs.show', $ticket->id) }}" class="btn-ticket-primary">
                                    Lihat Detail
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach
    @else
        <div class="empty-state-job">
            <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <h5 class="empty-title">Tidak Ada Ticket</h5>
            <p class="empty-text">Belum ada job yang tersedia saat ini</p>
        </div>
    @endif
</div>

<style>
/* Modern Black & White Theme - Job List */
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

.app-job-list {
    max-width: 1400px;
    margin: 0 auto;
    padding: 32px 20px;
}

/* Page Header */
.page-header-job {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 40px;
    padding-bottom: 24px;
    border-bottom: 2px solid var(--color-gray-200);
}

.header-content-job {
    flex: 1;
}

.page-title-job {
    font-size: 32px;
    font-weight: 700;
    color: var(--color-black);
    margin: 0 0 6px 0;
    letter-spacing: -0.5px;
}

.page-subtitle-job {
    font-size: 15px;
    color: var(--color-gray-500);
    margin: 0;
}

.header-stats {
    display: flex;
    gap: 24px;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
}

.stat-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--color-gray-500);
    font-weight: 600;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--color-black);
    line-height: 1;
}

/* Section Header */
.section-header-job {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 40px 0 24px 0;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--color-gray-200);
}

.section-title-container {
    display: flex;
    align-items: center;
    gap: 12px;
}

.section-title-job {
    font-size: 20px;
    font-weight: 600;
    color: var(--color-black);
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.section-count-job {
    background: var(--color-gray-100);
    color: var(--color-gray-600);
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
}

.priority-indicator {
    width: 40px;
    height: 4px;
    border-radius: 2px;
}

.priority-indicator.priority-urgent {
    background: var(--color-black);
}

.priority-indicator.priority-medium {
    background: var(--color-gray-600);
}

.priority-indicator.priority-low {
    background: var(--color-gray-300);
}

/* Ticket Grid */
.ticket-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

@media (max-width: 768px) {
    .ticket-grid {
        grid-template-columns: 1fr;
    }
}

/* Ticket Card */
.ticket-card-modern {
    background: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.ticket-card-modern:hover {
    border-color: var(--color-gray-400);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

/* Card Header */
.ticket-card-header {
    padding: 20px;
    background: var(--color-gray-50);
    border-bottom: 1px solid var(--color-gray-200);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
}

.customer-info {
    display: flex;
    gap: 12px;
    flex: 1;
    min-width: 0;
}

.customer-avatar {
    width: 40px;
    height: 40px;
    background: var(--color-black);
    color: var(--color-white);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 700;
    flex-shrink: 0;
}

.customer-details {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}

.customer-name-job {
    font-size: 15px;
    font-weight: 600;
    color: var(--color-black);
    margin: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.ticket-date {
    font-size: 12px;
    color: var(--color-gray-500);
}

/* Status Pill */
.status-pill {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.status-pill.status-finished {
    background: var(--color-black);
    color: var(--color-white);
}

.status-pill.status-progress {
    background: var(--color-gray-700);
    color: var(--color-white);
}

.status-pill.status-pending {
    background: var(--color-gray-200);
    color: var(--color-gray-700);
}

.status-pill.status-assigned {
    background: var(--color-gray-300);
    color: var(--color-gray-900);
}

/* Card Body */
.ticket-card-body {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.ticket-description {
    margin-bottom: 4px;
}

.description-text {
    font-size: 14px;
    color: var(--color-gray-800);
    line-height: 1.6;
    margin: 0;
}

.ticket-note {
    display: flex;
    gap: 8px;
    padding: 10px 12px;
    background: var(--color-gray-100);
    border-left: 3px solid var(--color-gray-400);
    border-radius: 4px;
    font-size: 13px;
    color: var(--color-gray-600);
    align-items: flex-start;
}

.ticket-note svg {
    flex-shrink: 0;
    margin-top: 2px;
}

.ticket-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-top: auto;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--color-gray-600);
}

.meta-item svg {
    color: var(--color-gray-400);
}

.meta-link {
    text-decoration: none;
    transition: all 0.2s ease;
}

.meta-link:hover {
    color: var(--color-black);
}

.meta-link:hover svg {
    color: var(--color-black);
}

/* Card Footer */
.ticket-card-footer {
    padding: 16px 20px;
    background: var(--color-gray-50);
    border-top: 1px solid var(--color-gray-200);
    display: flex;
    gap: 8px;
}

.btn-ticket-primary {
    flex: 1;
    padding: 10px 16px;
    background: var(--color-black);
    color: var(--color-white);
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.btn-ticket-primary:hover {
    background: var(--color-gray-800);
    transform: translateY(-1px);
}

.btn-ticket-primary svg {
    width: 14px;
    height: 14px;
}

.btn-ticket-secondary {
    padding: 10px 16px;
    background: var(--color-white);
    color: var(--color-gray-700);
    border: 2px solid var(--color-gray-300);
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.btn-ticket-secondary:hover {
    border-color: var(--color-black);
    background: var(--color-gray-50);
}

.btn-ticket-secondary svg {
    width: 14px;
    height: 14px;
}

/* Empty State */
.empty-state-job {
    text-align: center;
    padding: 80px 20px;
    background: var(--color-gray-50);
    border: 2px dashed var(--color-gray-300);
    border-radius: 12px;
}

.empty-state-job svg {
    color: var(--color-gray-300);
    margin-bottom: 20px;
}

.empty-title {
    font-size: 20px;
    font-weight: 600;
    color: var(--color-gray-700);
    margin: 0 0 8px 0;
}

.empty-text {
    font-size: 14px;
    color: var(--color-gray-500);
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .page-header-job {
        flex-direction: column;
        gap: 20px;
    }
    
    .header-stats {
        width: 100%;
        justify-content: flex-start;
    }
    
    .section-header-job {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
}
</style>
@endsection
