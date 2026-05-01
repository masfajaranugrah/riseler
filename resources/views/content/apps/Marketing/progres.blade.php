
@extends('layouts/layoutMaster')

@section('title', $pageTitle ?? 'Progres Pelanggan')

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
  /* ========== SHADCN-LIKE THEME (Black & White) ========== */
  :root {
    --primary-color: #0f172a;
    --secondary-color: #f8fafc;
    --border-color: #e2e8f0;
    --text-primary: #0f172a;
    --text-secondary: #64748b;
    --text-muted: #94a3b8;
    --radius: 0.5rem;
    --radius-lg: 0.75rem;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
  }

  body {
    background-color: #f8fafc;
    color: var(--text-primary);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  /* Card */
  .card-main {
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    background: #fff;
    overflow: hidden;
  }

  /* Page Header */
  .page-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
    background: #fff;
  }

  .page-header h4 {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.125rem;
    letter-spacing: -0.025em;
  }

  .page-header p {
    font-size: 0.8125rem;
    color: var(--text-secondary);
    margin-bottom: 0;
  }

  /* Toolbar */
  .toolbar {
    padding: 0.75rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
    background: var(--secondary-color);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
  }

  .stage-switcher {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    padding: 0.875rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
    background: #fff;
  }

  .stage-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 0.9rem;
    border: 1px solid var(--border-color);
    border-radius: 999px;
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 0.8125rem;
    font-weight: 600;
    background: #fff;
    transition: all 0.15s ease;
  }

  .stage-pill:hover {
    color: var(--text-primary);
    background: var(--secondary-color);
  }

  .stage-pill.is-active {
    border-color: var(--primary-color);
    background: var(--primary-color);
    color: #fff;
    box-shadow: 0 10px 25px -18px rgba(15, 23, 42, 0.8);
  }

  .stage-pill small {
    font-size: 0.6875rem;
    opacity: 0.8;
  }

  /* Search */
  .search-box {
    position: relative;
    flex: 1;
    min-width: 200px;
    max-width: 320px;
  }

  .search-box .search-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 0.875rem;
    pointer-events: none;
  }

  .search-box input {
    width: 100%;
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    padding: 0.5rem 0.75rem 0.5rem 2.25rem;
    font-size: 0.8125rem;
    color: var(--text-primary);
    background: #fff;
    transition: border-color 0.15s, box-shadow 0.15s;
    outline: none;
    height: 36px;
  }

  .search-box input::placeholder {
    color: var(--text-muted);
  }

  .search-box input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08);
  }

  /* Buttons */
  .btn-shadcn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
    padding: 0 0.875rem;
    min-height: 36px;
    font-size: 0.8125rem;
    font-weight: 500;
    border-radius: var(--radius);
    border: 1px solid var(--border-color);
    background: #fff;
    color: var(--text-primary);
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
    white-space: nowrap;
  }

  .btn-shadcn:hover {
    background: var(--secondary-color);
    color: var(--text-primary);
  }

  .btn-shadcn-primary {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: #fff;
  }

  .btn-shadcn-primary:hover {
    background: #1e293b;
    border-color: #1e293b;
    color: #fff;
  }

  .btn-shadcn-icon {
    width: 36px;
    padding: 0;
  }

  .btn-shadcn-sm {
    height: 30px;
    padding: 0 0.625rem;
    font-size: 0.75rem;
  }

  .btn-shadcn-sm.btn-shadcn-icon {
    width: 30px;
    padding: 0;
  }

  .btn-shadcn-danger {
    border-color: #fecaca;
    color: #dc2626;
    background: #fff;
  }

  .btn-shadcn-danger:hover {
    background: #fef2f2;
    border-color: #fca5a5;
    color: #b91c1c;
  }

  /* Stat Cards */
  .stat-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
    margin-bottom: 1rem;
  }

  .stat-card {
    background: #fff;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    transition: box-shadow 0.2s ease, transform 0.2s ease;
  }

  .stat-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -4px rgba(0,0,0,0.05);
    transform: translateY(-2px);
  }

  .stat-card-icon {
    width: 44px;
    height: 44px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
  }

  .stat-card-icon.icon-total {
    background: #f1f5f9;
    color: #0f172a;
  }

  .stat-card-icon.icon-approve {
    background: #dcfce7;
    color: #15803d;
  }

  .stat-card-icon.icon-pending {
    background: #fef3c7;
    color: #92400e;
  }

  .stat-card-body .stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
    margin-bottom: 0.25rem;
  }

  .stat-card-body .stat-label {
    font-size: 0.75rem;
    color: var(--text-secondary);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.04em;
  }

  @media (max-width: 767px) {
    .stat-cards {
      grid-template-columns: repeat(3, 1fr);
      gap: 0.5rem;
    }
    .stat-card {
      padding: 0.75rem;
      flex-direction: column;
      align-items: flex-start;
      gap: 0.5rem;
    }
    .stat-card-icon {
      width: 36px;
      height: 36px;
      font-size: 1rem;
    }
    .stat-card-body .stat-value {
      font-size: 1.125rem;
    }
  }

  /* Table */
  .table-clean {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
  }

  .table-clean thead th {
    background: var(--secondary-color);
    color: var(--text-secondary);
    font-weight: 500;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.625rem 1rem;
    border-bottom: 1px solid var(--border-color);
    white-space: nowrap;
  }

  .table-clean tbody td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--border-color);
    font-size: 0.8125rem;
    color: var(--text-primary);
    vertical-align: middle;
  }

  .table-clean tbody tr {
    transition: background-color 0.1s;
  }

  .table-clean tbody tr:hover {
    background-color: var(--secondary-color);
  }

  .table-clean tbody tr.row-urgent {
    background-color: #fff5f5;
  }

  .table-clean tbody tr.row-urgent:hover {
    background-color: #ffe9e9;
  }

  .table-clean tbody tr:last-child td {
    border-bottom: none;
  }

  .table-clean tbody tr.row-hidden {
    display: none;
  }

  /* Cell styling */
  .cell-name {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.8125rem;
    margin-bottom: 0.125rem;
  }

  .cell-sub {
    font-size: 0.6875rem;
    color: var(--text-muted);
    font-family: 'SF Mono', 'Fira Code', monospace;
  }

  .cell-wa {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    color: var(--text-secondary);
    font-size: 0.8125rem;
    text-decoration: none;
    transition: color 0.15s;
  }

  .cell-wa:hover {
    color: #16a34a;
  }

  .cell-wa i {
    color: #16a34a;
  }

  .cell-address {
    max-width: 180px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 0.8125rem;
    color: var(--text-primary);
  }

  .cell-address-sub {
    font-size: 0.6875rem;
    color: var(--text-muted);
  }

  .cell-date {
    font-size: 0.8125rem;
    color: var(--text-secondary);
    white-space: nowrap;
  }

  /* Badges */
  .badge-status {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.625rem;
    border-radius: 9999px;
    font-size: 0.6875rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    line-height: 1;
  }

  .badge-approve {
    background: #dcfce7;
    color: #15803d;
  }

  .badge-pending {
    background: #fef3c7;
    color: #92400e;
  }

  .badge-reject {
    background: #fee2e2;
    color: #b91c1c;
  }

  .badge-default {
    background: #f1f5f9;
    color: #475569;
  }

  .badge-id {
    display: inline-flex;
    align-items: center;
    padding: 0.125rem 0.5rem;
    border-radius: var(--radius);
    font-size: 0.6875rem;
    font-weight: 500;
    background: #f1f5f9;
    color: #475569;
    font-family: 'SF Mono', 'Fira Code', monospace;
  }

  .desktop-quick-cell {
    min-width: 0;
  }

  .desktop-quick-card {
    display: flex;
    flex-direction: column;
    gap: 0.625rem;
    padding: 0;
    border: none;
    background: transparent;
  }

  .desktop-quick-card.is-urgent,
  .m-card.is-urgent {
    border: 1px solid #fecaca;
    border-radius: 1rem;
    background: linear-gradient(180deg, #fff7f7 0%, #fff 100%);
    padding: 0.85rem;
  }

  .urgency-chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.3rem 0.65rem;
    border-radius: 999px;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    white-space: nowrap;
  }

  .urgency-chip.urgent {
    background: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fecaca;
  }

  .desktop-quick-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
  }

  .desktop-quick-title {
    min-width: 0;
  }

  .desktop-quick-title span {
    display: block;
    margin-bottom: 0.125rem;
    font-size: 0.65rem;
    color: var(--text-muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .desktop-quick-title strong {
    display: block;
    font-size: 0.8125rem;
    color: var(--primary-color);
    font-weight: 600;
  }

  /* Pagination */
  .pagination-wrapper {
    padding: 0.875rem 1.25rem;
    border-top: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.625rem;
    background: #fff;
  }

  .pagination-info {
    font-size: 0.75rem;
    color: var(--text-secondary);
  }

  .pagination-info strong {
    color: var(--text-primary);
    font-weight: 600;
  }

  .pagination {
    margin-bottom: 0;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.375rem;
    flex-wrap: wrap;
  }

  .pagination .page-item {
    margin: 0 !important;
  }

  .pagination .page-item .page-link {
    width: 36px;
    height: 36px;
    min-width: 36px;
    min-height: 36px;
    border-radius: 999px !important;
    border: 1px solid #d1d5db;
    color: #1f2937;
    background: #fff;
    font-size: 0.95rem;
    font-weight: 600;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
  }

  .pagination .page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: #fff;
    box-shadow: 0 6px 14px -8px rgba(15, 23, 42, 0.75);
  }

  .pagination .page-item.disabled .page-link {
    color: #cbd5e1;
    background: #f8fafc;
    border-color: #e5e7eb;
    opacity: 1;
  }

  .pagination .page-item .page-link:hover {
    background: #f8fafc;
    color: #0f172a;
  }

  .pagination .page-item.active .page-link:hover {
    background-color: #1e293b;
    color: #fff;
  }

  /* Mobile Cards */
  .mobile-cards {
    padding: 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.625rem;
  }

  .m-card {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.875rem;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    transition: all 0.2s ease;
  }

  .m-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
    border-color: #cbd5e1;
  }

  .m-card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
  }

  .m-card-top .badge-id {
    background: #f1f5f9;
    color: #475569;
    font-size: 0.75rem;
    padding: 0.25rem 0.625rem;
    border-radius: 9999px;
    font-weight: 600;
    letter-spacing: 0.02em;
  }

  .m-card-name {
    font-weight: 700;
    font-size: 1rem;
    color: #0f172a;
    line-height: 1.2;
    margin-bottom: 0.35rem;
  }

  .m-card-creator {
    display: none;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.8125rem;
    color: #64748b;
    background: #f8fafc;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    margin-bottom: 1.25rem;
  }
  .m-card-creator i {
    color: #94a3b8;
  }

  .m-progress-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 0.75rem;
  }

  .m-progress-head {
    font-size: 0.75rem;
    color: #64748b;
    margin-bottom: 0.5rem;
    text-align: center;
  }

  .m-progress-head strong {
    color: #0f172a;
    font-weight: 700;
    display: block;
    text-align: center;
    font-size: 0.875rem;
    margin-top: 0.25rem;
  }

  .stepper-mini {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 0.5rem;
  }
  .step-dot {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65rem;
    font-weight: 700;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px #e2e8f0;
    z-index: 2;
  }
  .step-dot.current {
    background: #0f172a;
    color: #fff;
    box-shadow: 0 0 0 2px #0f172a;
  }
  .step-dot.pending-current {
    background: #f59e0b;
    color: #fff;
    box-shadow: 0 0 0 2px #f59e0b;
  }
  .step-dot.done {
    background: #10b981;
    color: #fff;
    box-shadow: 0 0 0 1px #10b981;
  }
  .step-line {
    width: 20px;
    height: 2px;
    background: #e2e8f0;
    z-index: 1;
    margin: 0 -0.25rem;
  }
  .step-line.done {
    background: #10b981;
  }

  .m-card-detail-group {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 0.75rem;
  }

  .m-card-detail {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    font-size: 0.8125rem;
    color: #475569;
    line-height: 1.4;
  }

  .m-card-detail i {
    font-size: 0.875rem;
    color: #94a3b8;
    margin-top: 0.125rem;
  }

  .m-card-detail a {
    color: #10b981;
    font-weight: 500;
    text-decoration: none;
  }
  .m-card-detail a:hover { text-decoration: underline; }

  .quick-mobile-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0;
  }
  .quick-mobile-title span {
    font-size: 0.8125rem;
    font-weight: 600;
    color: #0f172a;
  }
  .quick-mobile-title small {
    font-size: 0.75rem;
    color: #94a3b8;
  }

  .m-card-actions {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
    margin-top: 0.75rem;
  }

  .mobile-quick-panel {
    border-top: 1px solid #f1f5f9;
    padding-top: 0.625rem;
  }

  .mobile-quick-panel summary {
    list-style: none;
    cursor: pointer;
  }

  .mobile-quick-panel summary::-webkit-details-marker {
    display: none;
  }

  .mobile-quick-panel .quick-mobile-title::after {
    content: '+';
    font-size: 1rem;
    font-weight: 700;
    color: #94a3b8;
  }

  .mobile-quick-panel[open] .quick-mobile-title::after {
    content: '-';
  }

  .mobile-quick-body {
    padding-top: 0.625rem;
  }

  .m-card-actions .btn-shadcn {
    width: 100%;
    justify-content: center;
    height: 36px;
    font-size: 0.8125rem;
    border-radius: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #475569;
    box-shadow: none;
    font-weight: 500;
  }
  .m-card-actions .btn-shadcn:hover {
    background: #f1f5f9;
    color: #0f172a;
  }
  .m-card-actions .btn-shadcn-danger {
    color: #ef4444;
  }
  .m-card-actions .btn-shadcn-danger:hover {
    background: #fef2f2;
    border-color: #fca5a5;
    color: #dc2626;
  }

  .quick-progress-form {
    display: grid;
    gap: 0.5rem;
  }

  .quick-progress-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    gap: 0.5rem;
    align-items: start;
  }

  .quick-progress-inline {
    display: flex;
    gap: 0.375rem;
    align-items: stretch;
  }

  select.progress-select,
  .quick-progress-form select.progress-select {
    width: 100%;
    height: 48px !important;
    min-height: 48px !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 0.375rem;
    background: #f8fafc !important;
    color: var(--text-primary) !important;
    font-size: 1rem !important;
    font-weight: 500;
    line-height: 1.2 !important;
    padding: 0.5rem 0.875rem !important;
    outline: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  select.progress-select:hover,
  .quick-progress-form select.progress-select:hover {
    background: #f1f5f9;
  }

  select.progress-select:focus,
  .quick-progress-form select.progress-select:focus,
  .quick-note-input:focus {
    border-color: var(--primary-color) !important;
    box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08);
    background: #fff;
  }

  .quick-save-btn {
    min-width: unset;
    padding: 0 0.875rem;
    border-radius: 0.375rem;
    font-weight: 500;
    font-size: 0.8125rem;
  }

  .quick-note-input {
    width: 100%;
    min-height: 38px;
    max-height: 110px;
    border: 1px solid var(--border-color);
    border-radius: 0.375rem;
    background: #fff;
    color: var(--text-primary);
    font-size: 0.8125rem;
    padding: 0.5rem 0.75rem;
    outline: none;
    resize: vertical;
    transition: all 0.2s ease;
  }

  .quick-note-input::placeholder {
    color: var(--text-muted);
  }


  .quick-note-preview {
    margin-top: 0.625rem;
    padding: 0.625rem 0.75rem;
    border: 1px dashed var(--border-color);
    border-radius: var(--radius);
    background: var(--secondary-color);
  }

  .quick-note-preview-label {
    display: block;
    margin-bottom: 0.25rem;
    font-size: 0.6875rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--text-muted);
  }

  .quick-note-preview p {
    margin: 0;
    font-size: 0.8125rem;
    line-height: 1.5;
    color: var(--text-secondary);
    white-space: pre-wrap;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .note-hidden {
    display: none !important;
  }

  .quick-mobile-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
  }

  .quick-mobile-title span {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--text-primary);
  }

  .desktop-actions {
    display: flex;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 0.375rem;
  }

  /* Detail Modal */
  .detail-label {
    font-size: 0.6875rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.375rem;
  }

  .detail-section {
    padding: 0.875rem;
    background: var(--secondary-color);
    border-radius: var(--radius);
    border: 1px solid var(--border-color);
  }

  .detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.375rem 0;
  }

  .detail-row:not(:last-child) {
    border-bottom: 1px solid var(--border-color);
  }

  .detail-row-label {
    font-size: 0.8125rem;
    color: var(--text-secondary);
  }

  .detail-row-value {
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--text-primary);
  }

  /* Empty State */
  .empty-state {
    padding: 3rem 1rem;
    text-align: center;
  }

  .empty-state i {
    font-size: 2.5rem;
    color: var(--border-color);
    margin-bottom: 0.75rem;
    display: block;
  }

  .empty-state p {
    color: var(--text-secondary);
    font-size: 0.8125rem;
    margin: 0;
  }

  /* Loading */
  .loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.85);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(4px);
  }

  /* No results from client-side search */
  .no-results-row {
    display: none;
  }

  .no-results-row td {
    text-align: center;
    padding: 2rem 1rem !important;
    color: var(--text-secondary);
  }

  /* Progress Stepper */
  .stepper-ui { display: flex; align-items: center; justify-content: space-between; margin: 1.5rem 0 2rem 0; position: relative; }
  .stepper-ui::before { content: ''; position: absolute; top: 12px; left: 10%; right: 10%; height: 2px; background: var(--border-color); z-index: 0; }
  .stepper-step { text-align: center; position: relative; z-index: 1; flex: 1; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; }
  .stepper-circle { width: 26px; height: 26px; border-radius: 50%; background: #fff; border: 2px solid var(--border-color); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: var(--text-muted); transition: all 0.2s; }
  .stepper-label { font-size: 0.6875rem; font-weight: 500; color: var(--text-muted); position: absolute; top: 32px; white-space: nowrap; }
  .stepper-step.active .stepper-circle { border-color: #16a34a; background: #16a34a; color: #fff; box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.1); }
  .stepper-step.active .stepper-label { color: #16a34a; font-weight: 600; }
  .stepper-step.pending .stepper-circle { border-color: #f59e0b; background: #f59e0b; color: #fff; box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.16); }
  .stepper-step.pending .stepper-label { color: #b45309; font-weight: 600; }
  .stepper-step.completed .stepper-circle { border-color: #16a34a; background: #fff; color: #16a34a; }
  .stepper-step.completed .stepper-label { color: #16a34a; }

  /* Responsive */
  @media (max-width: 767px) {
    .page-header {
      padding: 1rem;
    }
    .toolbar {
      padding: 0.75rem 1rem;
    }
    .stage-switcher {
      padding: 0.75rem 1rem;
    }
    .search-box {
      max-width: 100%;
      order: 10;
      min-width: 100%;
    }
    .stats-bar {
      padding: 0.75rem 1rem;
      gap: 1rem;
    }
    .pagination-wrapper {
      padding: 0.75rem 1rem;
      justify-content: center;
    }

    .quick-progress-grid {
      grid-template-columns: 1fr;
    }

    .quick-progress-inline {
      grid-template-columns: 1fr;
    }

    .quick-save-btn {
      width: 100%;
    }

    select.progress-select,
    .quick-progress-form select.progress-select {
      height: 50px !important;
      min-height: 50px !important;
      font-size: 1.03rem !important;
      padding-inline: 1rem !important;
    }
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
    // ====== Server-side search (lintas semua halaman data) ======
    const serverSearchInput = document.querySelector('input[name="search"]');
    if (serverSearchInput) {
        serverSearchInput.addEventListener('search', function() {
            this.form.submit();
        });
    }

    // ====== Detail Modal ======
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        const target = $(this).closest('tr').length ? $(this).closest('tr') : $(this).closest('.m-card');

        const data = {
            nomerId: target.data('nomer-id'),
            nama: target.data('nama'),
            whatsapp: target.data('whatsapp'),
            alamat: target.data('alamat'),
            rt: target.data('rt'),
            rw: target.data('rw'),
            kecamatan: target.data('kecamatan'),
            kabupaten: target.data('kabupaten'),
            tanggal: target.data('tanggal-mulai'),
            foto: target.data('foto-ktp'),
            status: target.data('status'),
            marketing: target.data('marketing-name'),
            email: target.data('marketing-email'),
            created: target.data('created-at'),
            is_pending: Number(target.data('is-pending')) === 1,
            progress_note: target.attr('data-progress-note'),
            progres: target.attr('data-progres'),
            deskripsi: target.attr('data-deskripsi')
        };

        const effectiveProgress = data.progres || 'Belum Diproses';
        const statusKey = (data.status || '').toLowerCase();
        let statusBadge = '';
        if (statusKey === 'approve') statusBadge = '<span class="badge-status badge-approve">Approve</span>';
        else if (statusKey === 'reject') statusBadge = '<span class="badge-status badge-reject">Reject</span>';
        else if (data.is_pending && effectiveProgress !== 'Belum Diproses') statusBadge = '<span class="badge-status badge-pending">Pending</span>';
        else if (effectiveProgress !== 'Belum Diproses') statusBadge = '<span class="badge-status badge-pending">Progres</span>';
        else if (statusKey === 'pending' || statusKey === 'proses') statusBadge = '<span class="badge-status badge-pending">Belum Diproses</span>';
        else statusBadge = '<span class="badge-status badge-default">' + data.status + '</span>';

        const steps = ['Belum Diproses', 'Tarik Kabel', 'Aktivasi', 'Registrasi'];
        let currentIndex = steps.indexOf(data.progres);
        const isPendingAtStage = data.is_pending && effectiveProgress !== 'Belum Diproses' && currentIndex !== -1;
        
        let stepperHtml = '<div class="col-12"><div class="detail-label">Tahapan Progres</div><div class="stepper-ui">';
        steps.forEach((step, i) => {
            let statusClass = '';
            let icon = i + 1;
            // Jika progres belum diset, tidak ada yg active/completed
            if (currentIndex !== -1) {
                if (i < currentIndex) {
                    statusClass = 'completed';
                    icon = '<i class="ri-check-line"></i>';
                } else if (i === currentIndex) {
                    statusClass = isPendingAtStage ? 'pending' : 'active';
                }
            }
            stepperHtml += `
                <div class="stepper-step ${statusClass}">
                    <div class="stepper-circle">${icon}</div>
                    <div class="stepper-label">${step}</div>
                </div>
            `;
        });
        stepperHtml += '</div></div>';

        const html = `
            <div class="text-center mb-3 pb-3" style="border-bottom: 1px solid var(--border-color);">
                <div class="cell-name" style="font-size: 1rem;">${data.nama}</div>
                <span class="badge-id mt-1">${data.nomerId}</span>
            </div>
            <div class="row g-3">
                ${stepperHtml}
                <div class="col-md-6">
                    <div class="detail-label">Info Kontak</div>
                    <div class="detail-section">
                        <div class="detail-row">
                            <span class="detail-row-label">WhatsApp</span>
                            <a href="https://wa.me/${data.whatsapp}" class="cell-wa" target="_blank">
                                <i class="ri-whatsapp-line"></i> ${data.whatsapp || '-'}
                            </a>
                        </div>
                        <div class="detail-row">
                            <span class="detail-row-label">Status</span>
                            <span>${statusBadge}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-row-label">Tgl Gabung</span>
                            <span class="detail-row-value">${data.tanggal || '-'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-row-label">Diinput Oleh</span>
                            <span class="detail-row-value">${data.marketing || '-'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-row-label">Email User</span>
                            <span class="detail-row-value">${data.email || '-'}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-label">Alamat</div>
                    <div class="detail-section">
                        <div class="detail-row-value mb-1">${data.alamat || '-'}</div>
                        <div class="detail-row-label">RT ${data.rt || '-'}/RW ${data.rw || '-'}, ${data.kecamatan || '-'}, ${data.kabupaten || '-'}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="detail-label">Catatan & Deskripsi</div>
                    <div class="detail-section">
                        <div class="detail-row">
                            <span class="detail-row-label">Catatan Progres</span>
                            <span class="detail-row-value" style="white-space: pre-wrap;">${(data.progress_note || '-').replace(/^\[PENDING\]\s*/i, '')}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-row-label">Deskripsi</span>
                            <span class="detail-row-value" style="white-space: pre-wrap;">${data.deskripsi || '-'}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="detail-label">Foto KTP</div>
                    <div class="detail-section text-center">
                        ${data.foto ? `<img src="${data.foto}" class="img-fluid rounded" style="max-height: 200px;">` : '<span class="detail-row-label">Tidak ada foto</span>'}
                    </div>
                </div>
            </div>
        `;

        $('#detailModal .modal-body').html(html);
        $('#detailModal').modal('show');
    });

    // ====== Delete Confirmation ======
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Hapus Data?',
            text: "Data yang dihapus tidak dapat dikembalikan",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#e2e8f0',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-dark me-2',
                cancelButton: 'btn btn-outline-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>
@endsection

@section('content')
<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner-border text-dark" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>


<div class="card-main">
    <!-- Page Header -->
    @php
        $stageRouteMap = [
            'belum-progres' => 'marketing.progres.belum-progres',
            'tarik-kabel' => 'marketing.progres.tarik-kabel',
            'aktivasi' => 'marketing.progres.aktivasi',
            'registrasi' => 'marketing.progres.registrasi',
        ];
        $stageDescriptions = [
            'belum-progres' => 'Pelanggan yang belum masuk tahapan progres marketing.',
            'tarik-kabel' => 'Pelanggan yang masih ada di tahap penarikan kabel.',
            'aktivasi' => 'Pelanggan yang siap atau sedang proses aktivasi layanan.',
            'registrasi' => 'Pelanggan yang sedang tahap registrasi akhir.',
        ];
        $pageTitle = ($selectedStageKey ?? 'tarik-kabel') === 'belum-progres'
            ? 'Pelanggan Belum Diproses'
            : 'Tahap ' . ($selectedStage ?? 'Progres');
        $pageDescription = $stageDescriptions[$selectedStageKey ?? 'tarik-kabel'] ?? 'Kelola data progres pelanggan sesuai tahap.';
    @endphp
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <div>
            <h4>{{ $pageTitle }}</h4>
            <p>{{ $pageDescription }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route($stageRouteMap[$selectedStageKey ?? 'tarik-kabel']) }}" class="btn-shadcn btn-shadcn-icon" title="Refresh">
                <i class="ri-refresh-line"></i>
            </a>
        </div>
    </div>

    <!-- Toolbar: Search -->
    <div class="toolbar">
        <!-- Server-side search (lintas semua data + pagination) -->
        <form action="{{ route($stageRouteMap[$selectedStageKey ?? 'tarik-kabel']) }}" method="GET" class="d-flex gap-2">
            <div class="search-box">
                <i class="ri-search-line search-icon"></i>
                <input type="search" name="search" placeholder="Cari pelanggan, ID, WhatsApp..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn-shadcn btn-shadcn-icon" title="Cari">
                <i class="ri-search-line"></i>
            </button>
        </form>
    </div>

    <!-- Table Content -->
    <div class="card-body p-0">
        <!-- Desktop Table -->
        <div class="table-responsive d-none d-md-block">
            <table class="table-clean">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">#</th>
                        <th style="min-width: 180px;">Pelanggan</th>
                        <th style="min-width: 140px;">Kontak</th>
                        <th style="min-width: 200px;">Alamat</th>
                        <th style="min-width: 100px;">Status</th>
                        <th style="min-width: 280px;">Update Cepat</th>
                        <th style="min-width: 120px;">Tgl Gabung</th>
                        <th style="min-width: 130px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="pelangganTableBody">
                    @forelse($pelanggan as $key => $p)
                    <tr class="{{ (strtolower($p->status ?? '') !== 'approve' && (blank($p->progres) || $p->progres === \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES)) ? 'row-urgent' : '' }}" data-searchable="{{ strtolower($p->nama_lengkap . ' ' . $p->nomer_id . ' ' . $p->no_whatsapp . ' ' . $p->alamat_jalan . ' ' . $p->kecamatan . ' ' . ($p->status ?? '')) }}"
                        data-nomer-id="{{ $p->nomer_id }}"
                        data-nama="{{ $p->nama_lengkap }}"
                        data-whatsapp="{{ $p->no_whatsapp }}"
                        data-alamat="{{ $p->alamat_jalan }}"
                        data-rt="{{ $p->rt }}"
                        data-rw="{{ $p->rw }}"
                        data-kecamatan="{{ $p->kecamatan }}"
                        data-kabupaten="{{ $p->kabupaten }}"
                        data-tanggal-mulai="{{ $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') : '' }}"
                        data-foto-ktp="{{ $p->foto_ktp ? asset('storage/' . $p->foto_ktp) : '' }}"
                        data-status="{{ $p->status }}"
                        data-marketing-name="{{ optional($p->user)->name }}"
                        data-marketing-email="{{ optional($p->user)->email }}"
                        data-created-at="{{ $p->created_at }}"
                        data-progress-note="{{ $p->progress_note }}"
                        data-progres="{{ $p->progres }}"
                        data-is-pending="{{ \Illuminate\Support\Str::startsWith(strtoupper(trim((string)($p->progress_note ?? ''))), '[PENDING]') ? 1 : 0 }}"
                        data-deskripsi="{{ $p->deskripsi }}">

                        <td style="text-align: center; color: var(--text-muted); font-size: 0.75rem;">{{ $pelanggan->firstItem() + $key }}</td>
                        <td>
                            <div class="cell-name">{{ $p->nama_lengkap }}</div>
                            <div class="cell-sub">{{ $p->nomer_id }}</div>
                            <div class="cell-sub">Dibuat oleh: {{ optional($p->user)->name ?? '-' }}</div>
                        </td>
                        <td>
                            @if($p->no_whatsapp)
                            <a href="https://wa.me/{{ $p->no_whatsapp }}" target="_blank" class="cell-wa">
                                <i class="ri-whatsapp-line"></i> {{ $p->no_whatsapp }}
                            </a>
                            @else
                            <span style="color: var(--text-muted);">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="cell-address" title="{{ $p->alamat_jalan }}">{{ $p->alamat_jalan ?? '-' }}</div>
                            @if($p->kecamatan)
                            <div class="cell-address-sub">{{ $p->kecamatan }}</div>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusKey = strtolower($p->status ?? 'pending');
                                $badgeClass = match($statusKey) {
                                    'approve' => 'badge-approve',
                                    'pending', 'proses' => 'badge-pending',
                                    'reject' => 'badge-reject',
                                    default => 'badge-default',
                                };
                                $currentProgressForStatus = blank($p->progres) ? \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES : $p->progres;
                                $isPendingStage = \Illuminate\Support\Str::startsWith(
                                    strtoupper(trim((string)($p->progress_note ?? ''))),
                                    '[PENDING]'
                                );
                                $statusLabel = $statusKey === 'approve'
                                    ? 'Approve'
                                    : ($statusKey === 'reject'
                                        ? 'Reject'
                                        : (($isPendingStage && $currentProgressForStatus !== \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES) ? 'Pending' : ($currentProgressForStatus !== \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES ? 'Progres' : 'Belum Diproses')));
                            @endphp
                            <span class="badge-status {{ $badgeClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="desktop-quick-cell">
                            @php
                                $currentProgress = blank($p->progres) ? \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES : $p->progres;
                                $isOwner = true; // Allow all marketing users to update
                                $isUrgent = strtolower($p->status ?? '') !== 'approve' && $currentProgress === \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES;
                            @endphp
                            <div class="desktop-quick-card {{ $isUrgent ? 'is-urgent' : '' }}">
                                <div class="desktop-quick-head">
                                    <div class="desktop-quick-title">
                                        <span>Update cepat</span>
                                        <strong>{{ $currentProgress }}</strong>
                                    </div>
                                    @if($isUrgent)
                                    <span class="urgency-chip urgent">Urgent</span>
                                    @endif
                                    <span class="badge-id">{{ $p->nomer_id }}</span>
                                </div>
                                @if($p->progress_note)
                                <div class="quick-note-preview">
                                    <span class="quick-note-preview-label">Catatan terakhir</span>
                                    <p>{{ trim(preg_replace('/^\[PENDING\]\s*/i', '', preg_replace('/\*\(Diupdate oleh:.*?\)\*/s', '', $p->progress_note))) }}</p>
                                </div>
                                @endif
                                @if($isOwner)
                                <form action="{{ route('marketing.pelanggan.progres', $p->id) }}" method="POST" class="quick-progress-form">
                                    @csrf
                                    <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">
                                    <div class="quick-progress-grid">
                                        <div class="quick-progress-inline">
                                            <select name="progres" class="progress-select" aria-label="Tahap progres {{ $p->nama_lengkap }}">
                                                @foreach(\App\Models\Pelanggan::PROGRES_STAGES as $stage)
                                                <option value="{{ $stage }}" {{ $currentProgress === $stage ? 'selected' : '' }}>{{ $stage }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label style="display:inline-flex;align-items:center;gap:0.4rem;font-size:0.75rem;color:var(--text-secondary);font-weight:600;">
                                            <input type="checkbox" name="is_pending" value="1" {{ $isPendingStage && $currentProgress !== \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES ? 'checked' : '' }}>
                                            Tandai Pending (Kendala)
                                        </label>
                                        <textarea
                                            name="progress_note"
                                            class="quick-note-input"
                                            rows="2"
                                            maxlength="1000"
                                            required
                                            placeholder="Wajib isi alasan/keterangan update status">{{ old('progress_note', '') }}</textarea>
                                        <button type="submit" class="btn-shadcn quick-save-btn">
                                            <i class="ri-save-line"></i> Simpan
                                        </button>
                                    </div>
                                </form>
                                @else
                                <div class="cell-sub">Readonly. Data ini diinput oleh user {{ optional($p->user)->name ?? '-' }}.</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="cell-date">{{ $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') : '-' }}</span>
                        </td>
                        <td>
                            <div class="desktop-actions">
                                <button class="btn-shadcn btn-shadcn-sm btn-shadcn-icon btn-detail" title="Detail">
                                    <i class="ri-eye-line"></i>
                                </button>
                                @if($isOwner)
                                <a href="{{ route('marketing.pelanggan.edit', $p->id) }}" class="btn-shadcn btn-shadcn-sm btn-shadcn-icon" title="Edit">
                                    <i class="ri-edit-2-line"></i>
                                </a>
                                <form action="{{ route('marketing.pelanggan.delete', $p->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-shadcn btn-shadcn-sm btn-shadcn-icon btn-shadcn-danger btn-delete" title="Hapus">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="ri-inbox-line"></i>
                                <p>Tidak ada data pelanggan ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    <tr class="no-results-row" id="noResultsRow">
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="ri-search-line"></i>
                                <p>Tidak ada hasil yang cocok dengan pencarian.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile View (Cards) -->
        <div class="d-md-none mobile-cards">
            @forelse($pelanggan as $p)
            <div class="m-card {{ (strtolower($p->status ?? '') !== 'approve' && (blank($p->progres) || $p->progres === \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES)) ? 'is-urgent' : '' }}"
                data-searchable="{{ strtolower($p->nama_lengkap . ' ' . $p->nomer_id . ' ' . $p->no_whatsapp . ' ' . $p->alamat_jalan) }}"
                data-nomer-id="{{ $p->nomer_id }}"
                data-nama="{{ $p->nama_lengkap }}"
                data-whatsapp="{{ $p->no_whatsapp }}"
                data-alamat="{{ $p->alamat_jalan }}"
                data-rt="{{ $p->rt }}"
                data-rw="{{ $p->rw }}"
                data-kecamatan="{{ $p->kecamatan }}"
                data-kabupaten="{{ $p->kabupaten }}"
                data-tanggal-mulai="{{ $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') : '' }}"
                data-foto-ktp="{{ $p->foto_ktp ? asset('storage/' . $p->foto_ktp) : '' }}"
                data-status="{{ $p->status }}"
                data-marketing-name="{{ optional($p->user)->name }}"
                data-marketing-email="{{ optional($p->user)->email }}"
                data-created-at="{{ $p->created_at }}"
                data-progress-note="{{ $p->progress_note }}"
                data-progres="{{ $p->progres }}"
                data-is-pending="{{ \Illuminate\Support\Str::startsWith(strtoupper(trim((string)($p->progress_note ?? ''))), '[PENDING]') ? 1 : 0 }}"
                data-deskripsi="{{ $p->deskripsi }}">

                <div class="m-card-top">
                    <span class="badge-id">{{ $p->nomer_id }}</span>
                    @php
                        $statusKey = strtolower($p->status ?? 'pending');
                        $badgeClass = match($statusKey) {
                            'approve' => 'badge-approve',
                            'pending', 'proses' => 'badge-pending',
                            'reject' => 'badge-reject',
                            default => 'badge-default',
                        };
                        $currentProgressForStatus = blank($p->progres) ? \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES : $p->progres;
                        $isPendingStage = \Illuminate\Support\Str::startsWith(
                            strtoupper(trim((string)($p->progress_note ?? ''))),
                            '[PENDING]'
                        );
                        $statusLabel = $statusKey === 'approve'
                            ? 'Approve'
                            : ($statusKey === 'reject'
                                ? 'Reject'
                                : (($isPendingStage && $currentProgressForStatus !== \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES) ? 'Pending' : ($currentProgressForStatus !== \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES ? 'Progres' : 'Belum Diproses')));
                    @endphp
                    <span class="badge-status {{ $badgeClass }}">{{ $statusLabel }}</span>
                </div>

                <div class="m-card-name">{{ $p->nama_lengkap }}</div>
                @php
                    $stages = ['Belum Diproses', 'Tarik Kabel', 'Aktivasi', 'Registrasi'];
                    $isApproved = strtolower($p->status ?? '') === 'approve';
                    $isPendingStage = \Illuminate\Support\Str::startsWith(
                        strtoupper(trim((string)($p->progress_note ?? ''))),
                        '[PENDING]'
                    );
                    $currentStageLabel = blank($p->progres) ? \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES : $p->progres;
                    $currentStage = array_search($currentStageLabel, $stages);
                    $isOwner = true; // Allow all marketing users to update
                    $isUrgent = !$isApproved && $currentStageLabel === \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES;
                    if ($isApproved) {
                        $currentStageLabel = 'Registrasi';
                    }
                @endphp
                <div class="m-progress-section">
                    <div class="m-progress-head">
                        Tahap Progres
                        <strong>{{ $currentStageLabel }}</strong>
                    </div>
                    @if($isUrgent)
                    <div class="mb-2">
                        <span class="urgency-chip urgent">Urgent</span>
                    </div>
                    @endif
                    <div class="stepper-mini mb-0">
                        @foreach($stages as $index => $stage)
                            @php
                                $dotClass = '';
                                $dotValue = $index + 1;
                                if ($isApproved) {
                                    $dotClass = 'done';
                                    $dotValue = '✓';
                                } elseif ($currentStage !== false) {
                                    if ($index < $currentStage) {
                                        $dotClass = 'done';
                                        $dotValue = '✓';
                                    } elseif ($index === $currentStage) {
                                        $dotClass = $isPendingStage && $currentStageLabel !== \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES ? 'pending-current' : 'current';
                                    }
                                }
                            @endphp
                            <div class="step-dot {{ $dotClass }}" title="{{ $stage }}">{{ $dotValue }}</div>
                            @if(!$loop->last)
                                <div class="step-line {{ ($isApproved || ($currentStage !== false && $index < $currentStage)) ? 'done' : '' }}"></div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <details class="mobile-quick-panel">
                    @php
                        $currentProgress = $p->progres ?? \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES;
                    @endphp
                    <summary class="quick-mobile-title">
                        <span>Update cepat</span>
                        <small>Buka jika perlu</small>
                    </summary>
                    <div class="mobile-quick-body">
                        @if($p->progress_note)
                        <div class="quick-note-preview mb-2">
                            <span class="quick-note-preview-label">Catatan terakhir</span>
                            <p>{{ trim(preg_replace('/^\[PENDING\]\s*/i', '', preg_replace('/\*\(Diupdate oleh:.*?\)\*/s', '', $p->progress_note))) }}</p>
                        </div>
                        @endif
                        @if($isOwner)
                        <form action="{{ route('marketing.pelanggan.progres', $p->id) }}" method="POST" class="quick-progress-form">
                            @csrf
                            <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">
                            <div class="quick-progress-grid">
                                <select name="progres" class="progress-select">
                                    @foreach(\App\Models\Pelanggan::PROGRES_STAGES as $stage)
                                    <option value="{{ $stage }}" {{ $currentProgress === $stage ? 'selected' : '' }}>{{ $stage }}</option>
                                    @endforeach
                                </select>
                                <label style="display:inline-flex;align-items:center;gap:0.4rem;font-size:0.75rem;color:var(--text-secondary);font-weight:600;">
                                    <input type="checkbox" name="is_pending" value="1" {{ $isPendingStage && $currentProgress !== \App\Models\Pelanggan::PROGRES_BELUM_DIPROSES ? 'checked' : '' }}>
                                    Tandai Pending (Kendala)
                                </label>
                                <textarea
                                    name="progress_note"
                                    class="quick-note-input"
                                    rows="3"
                                    maxlength="1000"
                                    required
                                    placeholder="Wajib isi alasan/keterangan update status">{{ old('progress_note', '') }}</textarea>
                                <button type="submit" class="btn-shadcn quick-save-btn" style="width:100%;">
                                    <i class="ri-save-line"></i> Simpan
                                </button>
                            </div>
                        </form>
                        @else
                        <div class="cell-sub">Readonly. Hanya user {{ optional($p->user)->name ?? '-' }} yang bisa ubah data ini.</div>
                        @endif
                    </div>
                </details>

                <div class="m-card-actions">
                    <button class="btn-shadcn btn-shadcn-sm btn-detail">
                        <i class="ri-eye-line"></i> Detail
                    </button>
                    @if($isOwner)
                    <a href="{{ route('marketing.pelanggan.edit', $p->id) }}" class="btn-shadcn btn-shadcn-sm">
                        <i class="ri-edit-2-line"></i> Edit
                    </a>
                    <form action="{{ route('marketing.pelanggan.delete', $p->id) }}" method="POST" style="width: 100%;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-shadcn btn-shadcn-sm btn-shadcn-danger btn-delete">
                            <i class="ri-delete-bin-line"></i> Hapus
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="empty-state">
                <i class="ri-inbox-line"></i>
                <p>Tidak ada data pelanggan.</p>
            </div>
            @endforelse
        </div>

        @include('components.marketing-pagination', ['paginator' => $pelanggan])
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="box-shadow: var(--shadow); border-radius: var(--radius-lg);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-color); padding: 1rem 1.25rem;">
                <h6 class="modal-title" style="font-weight: 700; font-size: 0.9375rem;">Detail Pelanggan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 0.625rem;"></button>
            </div>
            <div class="modal-body" style="padding: 1.25rem;">
                <!-- Content injected via JS -->
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color); padding: 0.75rem 1.25rem; background: var(--secondary-color);">
                <button type="button" class="btn-shadcn" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection
