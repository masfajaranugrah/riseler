@extends('layouts/layoutMaster')

@section('title', 'Daftar Tagihan')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/plugins/monthSelect/style.css">
<style>
/* ========================================= */
/* FLATPICKR CUSTOM THEME */
.flatpickr-calendar {
  border: 1px solid rgba(0,0,0,0.05) !important;
  box-shadow: 0 20px 40px -10px rgba(0,0,0,0.15) !important;
  border-radius: 16px !important;
  padding: 16px !important;
  width: 360px !important;
  max-width: calc(100vw - 24px) !important;
  font-family: inherit !important;
  background: white !important;
}
.flatpickr-months {
  margin-bottom: 12px !important;
}
.flatpickr-current-month {
  font-size: 1rem !important;
  padding: 0 !important;
}
.flatpickr-current-month .flatpickr-monthDropdown-months {
  font-weight: 700 !important;
  font-size: 1rem !important;
}
.flatpickr-days,
.dayContainer {
  width: 100% !important;
}
.flatpickr-months .flatpickr-prev-month, 
.flatpickr-months .flatpickr-next-month {
  top: 0 !important;
  padding: 10px !important;
}
.flatpickr-monthSelect-months {
    gap: 8px;
    display: flex !important;
    flex-wrap: wrap !important;
    justify-content: space-between !important;
}
.flatpickr-monthSelect-month {
    border-radius: 10px !important;
    padding: 12px 0 !important;
    font-weight: 500 !important;
    font-size: 0.9rem !important;
    width: 31% !important;
    margin: 0 !important;
    margin-bottom: 8px !important;
    transition: all 0.2s ease !important;
    background: #f4f4f5 !important;
    color: #52525b !important;
    border: 1px solid transparent !important;
}
.flatpickr-monthSelect-month:hover {
    background: #e4e4e7 !important;
    color: #18181b !important;
}
.flatpickr-monthSelect-month.selected {
    background: #18181b !important;
    color: white !important;
    box-shadow: 0 4px 12px rgba(24,24,27,0.3) !important;
}

/* Broadcast datepicker: prevent right-side clipping on modal */
.flatpickr-calendar.broadcast-date-picker {
  width: 300px !important;
  max-width: calc(100vw - 24px) !important;
  padding: 10px 12px 12px !important;
}

.flatpickr-calendar.broadcast-date-picker .flatpickr-days,
.flatpickr-calendar.broadcast-date-picker .dayContainer {
  width: 100% !important;
}

.light-style .flatpickr-calendar, .light-style .flatpickr-days{
  width: 300px !important;
}
/* MODERN CLEAN STYLES */
/* ========================================= */
:root {
  --card-shadow: 0 2px 8px rgba(0,0,0,0.08);
  --card-hover-shadow: 0 4px 16px rgba(0,0,0,0.12);
  --border-radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --primary-color: #111827;
  --success-color: #28c76f;
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
  transform: translateY(-2px);
}

/* Dashboard Cards with Border Accent */
.card-border-shadow-primary::before,
.card-border-shadow-success::before,
.card-border-shadow-warning::before,
.card-border-shadow-info::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
}

.card-border-shadow-primary::before {
  background: linear-gradient(180deg, #111827 0%, #0b1220 100%);
}

.card-border-shadow-success::before {
  background: linear-gradient(180deg, #d1d5db 0%, #9ca3af 100%);
}

.card-border-shadow-warning::before {
  background: linear-gradient(180deg, #e5e7eb 0%, #d1d5db 100%);
}

.card-border-shadow-info::before {
  background: linear-gradient(180deg, #cbd5e1 0%, #94a3b8 100%);
}

/* Stats Card */
.stats-card {
  border-radius: var(--border-radius);
  padding: 1.5rem;
  background: #ffffff;
  color: #0f172a;
  border: 1px solid #e5e7eb;
  transition: var(--transition);
}

.stats-card p,
.stats-card h2,
.stats-card .text-muted {
  color: #0f172a !important;
}

.stats-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
}

.stats-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  background: #f3f4f6;
  color: #111827;
}

/* Avatar */
.avatar-initial {
  border-radius: 12px;
  transition: var(--transition);
}

.card:hover .avatar-initial {
  transform: scale(1.05);
}

/* ========================================= */
/* SHADCN UI STYLE BUTTONS - ALL BLACK */
/* Override Bootstrap default button colors */
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

.btn.btn-primary:focus,
.btn-primary:focus,
.btn.btn-primary:focus-visible,
.btn-primary:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
  background: #18181b !important;
  color: #fafafa !important;
}

.btn.btn-primary:active,
.btn-primary:active {
  background: #09090b !important;
  color: #fafafa !important;
}

/* Warning Button - Black */
.btn.btn-warning,
.btn-warning {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-warning:hover,
.btn-warning:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

.btn.btn-warning:focus,
.btn-warning:focus,
.btn.btn-warning:focus-visible,
.btn-warning:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
  background: #18181b !important;
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

.btn.btn-success:focus,
.btn-success:focus,
.btn.btn-success:focus-visible,
.btn-success:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
  background: #18181b !important;
  color: #fafafa !important;
}

/* Secondary Button - Black */
.btn.btn-secondary,
.btn-secondary {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-secondary:hover,
.btn-secondary:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

.btn.btn-secondary:focus,
.btn-secondary:focus,
.btn.btn-secondary:focus-visible,
.btn-secondary:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
  background: #18181b !important;
  color: #fafafa !important;
}

/* Danger Button - Black */
.btn.btn-danger,
.btn-danger {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-danger:hover,
.btn-danger:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

.btn.btn-danger:focus,
.btn-danger:focus,
.btn.btn-danger:focus-visible,
.btn-danger:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
  background: #18181b !important;
  color: #fafafa !important;
}

/* Search Button - Black (shadcn-like) */
.btn.btn-search-dark,
.btn-search-dark {
  background: #18181b !important;
  background-color: #18181b !important;
  color: #fafafa !important;
  border: 1px solid #18181b !important;
}

.btn.btn-search-dark:hover,
.btn-search-dark:hover {
  background: #27272a !important;
  background-color: #27272a !important;
  border-color: #27272a !important;
  color: #fafafa !important;
}

.btn.btn-search-dark:focus,
.btn-search-dark:focus,
.btn.btn-search-dark:focus-visible,
.btn-search-dark:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
}

.btn.btn-search-dark:active,
.btn-search-dark:active {
  background: #09090b !important;
}

/* Outline Buttons - Light background, black text */
.btn.btn-outline-primary,
.btn.btn-outline-secondary,
.btn.btn-outline-danger,
.btn-outline-primary,
.btn-outline-secondary,
.btn-outline-danger {
  background: transparent !important;
  background-color: transparent !important;
  border: 1px solid #e4e4e7 !important;
  color: #18181b !important;
}

.btn.btn-outline-primary:hover,
.btn.btn-outline-secondary:hover,
.btn.btn-outline-danger:hover,
.btn-outline-primary:hover,
.btn-outline-secondary:hover,
.btn-outline-danger:hover {
  background: #f4f4f5 !important;
  background-color: #f4f4f5 !important;
  border-color: #a1a1aa !important;
  color: #18181b !important;
}

.btn.btn-outline-primary:focus,
.btn.btn-outline-secondary:focus,
.btn.btn-outline-danger:focus,
.btn-outline-primary:focus-visible,
.btn-outline-secondary:focus-visible,
.btn-outline-danger:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #18181b !important;
}

/* Small Button */
.btn.btn-sm,
.btn-sm {
  padding: 0.375rem 0.75rem !important;
  font-size: 0.8125rem !important;
}

/* Icon Button */
.btn-icon {
  width: 2rem;
  height: 2rem;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

/* ========================================= */
/* SHADCN UI STYLE BADGES & TEXT */
/* ========================================= */

/* Badges */
.badge {
  padding: 0.25rem 0.625rem;
  border-radius: 9999px;
  font-weight: 500;
  font-size: 0.75rem;
  letter-spacing: 0;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
}

/* Neutralize accent labels and badges - shadcn style */
.bg-label-primary,
.bg-label-success,
.bg-label-warning,
.bg-label-dark {
  background: #f4f4f5 !important;
  color: #18181b !important;
  border: 1px solid #e4e4e7 !important;
}

/* Badge Paket - Black background, white text */
.bg-label-info {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

.stats-icon.bg-label-primary,
.stats-icon.bg-label-success,
.stats-icon.bg-label-warning,
.stats-icon.bg-label-info {
  background: #f4f4f5 !important;
  color: #18181b !important;
}

/* Status Lunas - Black */
.badge.bg-success {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

/* Status Belum Bayar - Red with white text, rounded */
.badge.bg-danger {
  background: #dc2626 !important;
  color: #fafafa !important;
  border: none !important;
  border-radius: 9999px !important;
}

/* Solid badges default */
.bg-info,
.bg-warning,
.bg-primary,
.bg-dark {
  background: #18181b !important;
  color: #fafafa !important;
  border: none !important;
}

/* All text colors - Black (shadcn style) */
.text-success,
.text-info,
.text-warning,
.text-primary,
.text-danger,
.text-muted {
  color: #71717a !important;
}
 
/* Form Controls */
.form-select, .form-control {
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  padding: 0.625rem 1rem;
  transition: var(--transition);
}

.form-select:focus, .form-control:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(17, 24, 39, 0.12);
}

/* ========================================= */
/* SHADCN UI STYLE MODAL */
/* ========================================= */
.modal-content {
  border-radius: 12px;
  border: 1px solid #e4e4e7;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
  background: #ffffff;
  overflow: hidden;
}

.modal-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #e4e4e7;
  background: #fafafa;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.modal-header .modal-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #18181b;
  margin: 0;
}

.modal-header.bg-primary {
  background: #18181b !important;
  border-bottom: none;
}

.modal-header.bg-primary .modal-title {
  color: #fafafa;
}

.modal-header.bg-warning {
  background: #18181b !important;
  border-bottom: none;
}

.modal-header.bg-warning .modal-title {
  color: #fafafa;
}

.modal-header .btn-close {
  padding: 0.5rem;
  margin: -0.5rem -0.5rem -0.5rem auto;
  opacity: 0.5;
  transition: opacity 0.15s ease;
}

.modal-header .btn-close:hover {
  opacity: 1;
}

.modal-body {
  padding: 1.5rem;
  padding-top: 2rem;
  max-height: 65vh;
  overflow-y: auto;
}

.modal-footer {
  padding: 1rem 1.5rem;
  margin-top: 0.5rem;
  border-top: 1px solid #e4e4e7;
  background: #fafafa;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.75rem;
}

.btn-close-white {
  filter: brightness(0) invert(1);
}

/* Modal Dialog Centered with proper spacing */
.modal-dialog-centered {
  min-height: calc(100% - 3.5rem);
  margin: 1.75rem auto;
}

/* Modal backdrop with blur effect */
.modal-backdrop.show {
  opacity: 1;
  background-color: rgba(24, 24, 27, 0.4);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

/* Table */
.table {
  border-collapse: separate;
  border-spacing: 0;
}

.table thead th {
  background: #f8fafc;
  border: none;
  padding: 1rem;
  font-weight: 600;
  color: #0f172a;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
}

.table tbody tr {
  transition: var(--transition);
}

.table tbody tr:not(.empty-state-row):hover {
  background: #f1f5f9;
  transform: scale(1.001);
}

.table tbody td {
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
  vertical-align: middle;
}

.table thead th:first-child,
.table tbody td:first-child {
  text-align: center;
}

/* Empty State */
.empty-state-row td {
  background: #fafbfc !important;
  border: none !important;
}

.empty-state-content {
  padding: 3rem 1rem;
}

/* Loading Overlay */
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

/* ========================================= */
/* DETAIL MODAL STYLES */
/* ========================================= */
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
  background: linear-gradient(135deg, #111827 0%, #0b1220 100%);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 2.5rem;
  margin-bottom: 1rem;
  box-shadow: 0 4px 16px rgba(105, 108, 255, 0.4);
  border: 4px solid white;
}

.customer-name {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2c3e50;
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

.detail-section {
  background: #ffffff;
  border: 1px solid #e4e4e7;
  border-radius: 8px;
  padding: 1.25rem;
  margin-bottom: 1.25rem;
  transition: all 0.2s;
}

.detail-section:first-child,
.modal-body > .detail-section:first-of-type {
  margin-top: 0.5rem;
}

.detail-section:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  border-color: #111827;
}

.detail-section h6 {
  color: #111827;
  font-weight: 700;
  margin-bottom: 1.25rem;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  display: flex;
  align-items: center;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid #111827;
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
  min-width: 150px;
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

/* Card Header */
.card-header {
  background: transparent;
  padding: 1.5rem;
  border-bottom: 1px solid #f0f0f0;
}

.card-header-custom {
  background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
  border-radius: var(--border-radius) var(--border-radius) 0 0;
  padding: 1.5rem;
  border-bottom: 1px solid #f0f0f0;
}

/* Input Groups */
.input-group-text {
  border-radius: 8px 0 0 8px;
  background: #f8f9fa;
  border: 1px solid #e0e0e0;
  color: #5a5f7d;
  font-weight: 500;
}

/* ========================================= */
/* PAGINATION STYLES */
/* ========================================= */
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

/* DataTables pagination styles */
.dataTables_wrapper .dataTables_info {
  float: left !important;
  padding-top: 1.25rem;
  padding-bottom: 1rem;
  color: #71717a;
  font-size: 0.875rem;
}

.dataTables_wrapper .dataTables_paginate {
  float: right !important;
  text-align: right !important;
  padding-top: 1rem;
  padding-bottom: 1rem;
}

.dataTables_wrapper .dataTables_paginate .pagination {
  justify-content: flex-end !important;
  margin: 0 !important;
}

.dataTables_wrapper .dataTables_paginate .page-item .page-link {
  border-radius: 50% !important;
  width: 40px !important;
  height: 40px !important;
  padding: 0 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  margin: 0 4px !important;
  border: 1px solid #e4e4e7 !important;
  color: #18181b !important;
  background: #fff !important;
  background-color: #fff !important;
  font-weight: 600 !important;
  transition: all 0.3s ease !important;
}

.dataTables_wrapper .dataTables_paginate .page-item .page-link:hover {
  background: #f4f4f5 !important;
  background-color: #f4f4f5 !important;
  border-color: #18181b !important;
  color: #18181b !important;
}

.dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
  background: #18181b !important;
  background-color: #18181b !important;
  border-color: #18181b !important;
  color: #fafafa !important;
}

.dataTables_wrapper .dataTables_paginate .page-item.disabled .page-link {
  background: #f4f4f5 !important;
  background-color: #f4f4f5 !important;
  border-color: #e4e4e7 !important;
  color: #a1a1aa !important;
  cursor: not-allowed !important;
}

.dataTables_wrapper::after {
  content: '';
  display: table;
  clear: both;
}

/* Hide DataTables default controls if using custom pagination */
.dataTables_length,
.dataTables_filter {
  display: none !important;
}

/* Hide default Laravel pagination results text */
.pagination-wrapper .pagination + div,
.pagination-wrapper nav + div,
.pagination-wrapper div:has(> nav) > p,
.pagination-wrapper > div > nav ~ *:not(.pagination),
.pagination-wrapper > div:last-child p {
  display: none !important;
}

/* Alternative: Hide any 'Showing X to Y of Z results' text */
.pagination-wrapper div:last-child > p,
.pagination-wrapper > div > .text-sm,
nav[role="navigation"] > div:first-child,
nav[role="navigation"] > div > p {
  display: none !important;
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.card {
  animation: fadeIn 0.3s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
  .modal-body {
    padding: 1.5rem;
  }
  .card-body {
    padding: 1.25rem;
  }
  .pagination-wrapper {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
}
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/plugins/monthSelect/index.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
@endsection

@section('page-script')
<style>
/* Force hide SweetAlert deny button */
.swal2-deny, .swal2-styled.swal2-deny { display: none !important; }
</style>
<script>
// Data rekening untuk dropdown verifikasi (rendered by Blade, used in JS)
const rekeningList = @json($rekeningList ?? []);

document.addEventListener("DOMContentLoaded", function () {
    // ========================================
    // HELPER FUNCTIONS
    // ========================================
    function showLoading() {
        $('.loading-overlay').css('display', 'flex');
    }

    function hideLoading() {
        $('.loading-overlay').fadeOut(300);
    }

    const formatDate = d => d.toISOString().split('T')[0];

    // ========================================
    // FLATPICKR INITIALIZATION
    // ========================================
    $(document).on('shown.bs.modal', '[id^="modalEditTagihan-"]', function () {
        flatpickr($(this).find('.flatpickr-edit-start'), {
            dateFormat: "Y-m-d",
        allowInput: true,
        minDate: null,
        disableMobile: true
        });
        flatpickr($(this).find('.flatpickr-edit-end'), {
            dateFormat: "Y-m-d",
        allowInput: true,
        minDate: null,
        disableMobile: true
        });
    });

    flatpickr("#tanggal_mulai", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "j F Y",
        defaultDate: new Date(),
        allowInput: true,
        locale: "id",
        disableMobile: true
    });

    flatpickr("#tanggal_berakhir", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "j F Y",
        allowInput: false,
        locale: "id",
        disableMobile: true
    });

    // ========================================
    // SELECT2 PELANGGAN - AJAX MODE
    // ========================================
    $('#pelangganSelect').select2({
        placeholder: '-- Ketik untuk mencari pelanggan --',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modalTambahTagihan'),
        minimumInputLength: 1,
        ajax: {
            url: '{{ route("pelanggan.search") }}',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    filter_no_tagihan: 1 // Filter pelanggan yang belum punya tagihan
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        },
        language: {
            inputTooShort: function() {
                return 'Ketik minimal 1 karakter untuk mencari...';
            },
            searching: function() {
                return 'Mencari...';
            },
            noResults: function() {
                return 'Tidak ditemukan';
            }
        }
    });

    const tglMulai = document.getElementById('tanggal_mulai');
    if (tglMulai) {
        tglMulai.value = formatDate(new Date());
    }

    function fillFields(selected) {
        if (!selected || !selected.id) {
            $('#nama_lengkap, #alamat_jalan, #rt, #rw, #desa, #kecamatan, #kabupaten, #provinsi, #kode_pos, #no_whatsapp, #nomer_id, #paket, #harga, #masa_pembayaran, #kecepatan, #pelanggan_id, #paket_id, #tanggal_berakhir').val('');
            return;
        }

        // Data dari AJAX response Select2
        $('#nama_lengkap').val(selected.nama || '');
        $('#alamat_jalan').val(selected.alamat_jalan || '');
        $('#rt').val(selected.rt || '');
        $('#rw').val(selected.rw || '');
        $('#desa').val(selected.desa || '');
        $('#kecamatan').val(selected.kecamatan || '');
        $('#kabupaten').val(selected.kabupaten || '');
        $('#provinsi').val(selected.provinsi || '');
        $('#kode_pos').val(selected.kode_pos || '');
        $('#no_whatsapp').val(selected.nowhatsapp || '');
        $('#nomer_id').val(selected.nomorid || '');
        $('#paket').val(selected.paket || '');
        $('#harga').val(selected.harga || '');
        $('#masa_pembayaran').val(selected.masa || '');
        $('#kecepatan').val(selected.kecepatan || '');
        $('#pelanggan_id').val(selected.id);
        $('#paket_id').val(selected.paket_id || '');

        // Hitung tanggal berakhir
        const startDateVal = $('#tanggal_mulai').val();
        if (startDateVal && selected.masa) {
            const startDate = new Date(startDateVal);
            const endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + parseInt(selected.masa));
            $('#tanggal_berakhir').val(formatDate(endDate));
        }
    }

    // Handler saat pelanggan dipilih dari dropdown
    $('#pelangganSelect').on('select2:select', function(e) {
        fillFields(e.params.data);
    });

    // Handler saat pelanggan di-clear
    $('#pelangganSelect').on('select2:clear', function() {
        fillFields(null);
    });

    if (tglMulai) {
        tglMulai.addEventListener('change', function () {
            // Recalculate tanggal_berakhir jika ada pelanggan yang dipilih
            const selectedData = $('#pelangganSelect').select2('data');
            if (selectedData && selectedData.length > 0 && selectedData[0].id) {
                const data = selectedData[0];
                if (data.masa) {
                    const startDate = new Date($('#tanggal_mulai').val());
                    const endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + parseInt(data.masa));
                    $('#tanggal_berakhir').val(formatDate(endDate));
                }
            }
        });
    }

    // Modal shown - focus ke search pelanggan
    $('#modalTambahTagihan').on('shown.bs.modal', function () {
        $('#pelangganSelect').select2('open');
    });

    // ========================================
    // AUTO SUBMIT ON FILTER CHANGE
    // ========================================
    $('#statusFilter').on('change', function() {
        $('#filterForm').submit();
    });

    // Flatpickr for month/year filter
    flatpickr('#periodeTrigger', {
        plugins: [new monthSelectPlugin({
            shorthand: true,
            dateFormat: "Y-m",
            altFormat: "F Y",
            theme: "light"
        })],
        locale: "id", // Use Indonesian locale
        disableMobile: true,
        defaultDate: "{{ request('periode') }}",
        onChange: function(selectedDates, dateStr) {
            if (dateStr) {
                $('#periodeInput').val(dateStr);
                showLoading();
                $('#filterForm').submit();
            }
        }
    });

    window.resetFilterTagihan = function(e) {
        e.stopPropagation();
        showLoading();
        // Remove periode param
        const url = new URL(window.location.href);
        url.searchParams.delete('periode');
        window.location.href = url.toString();
    }

    // ========================================
    // LOADING OVERLAY ON FORM SUBMIT
    // ========================================
    $('#filterForm').on('submit', function() {
        showLoading();
    });

    // ========================================
    // SWEETALERT DELETE
    // ========================================
    $(document).on('submit', '.delete-form', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const form = this;
        const $form = $(form);
        const actionUrl = $form.attr('action');
        const $row = $form.closest('tr');

        Swal.fire({
            title: 'Konfirmasi Penghapusan',
            html: '<p class="mb-0">Yakin ingin menghapus tagihan ini?<br><strong class="text-danger">Data tidak dapat dikembalikan!</strong></p>',
            icon: 'warning',
            showCancelButton: true,
            showConfirmButton: true,
            showDenyButton: false,
            confirmButtonColor: '#f5365c',
            cancelButtonColor: '#8898aa',
            confirmButtonText: '<i class="ri-delete-bin-line me-1"></i>Hapus',
            cancelButtonText: 'Batal',
            allowOutsideClick: false,
            allowEscapeKey: false,
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();

                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'DELETE'
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(resp) {
                        hideLoading();
                        if (resp.success) {
                            // Hapus baris dari tabel dengan animasi fade
                            $row.fadeOut(400, function() {
                                $(this).remove();

                                // Update nomor urut
                                $('table tbody tr').each(function(index) {
                                    $(this).find('td:first').text(index + 1);
                                });

                                // Update badge total tagihan
                                const $totalBadge = $('.card-header-custom .badge:contains("Tagihan")');
                                if ($totalBadge.length) {
                                    const currentTotal = parseInt($totalBadge.text().replace(/\D/g, '')) || 0;
                                    if (currentTotal > 1) {
                                        $totalBadge.html('<i class="ri-database-2-line me-1"></i>' + (currentTotal - 1) + ' Tagihan');
                                    } else {
                                        $totalBadge.remove();
                                    }
                                }
                            });

                            // Tampilkan notifikasi sukses singkat
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: resp.message || 'Tagihan berhasil dihapus!',
                                showConfirmButton: false,
                                timer: 1500,
                                timerProgressBar: true
                            });
                        } else {
                            Swal.fire('Gagal!', resp.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function(xhr) {
                        hideLoading();
                        const msg = xhr.responseJSON?.message || 'Terjadi kesalahan server.';
                        Swal.fire('Gagal!', msg, 'error');
                    }
                });
            }
        });
    });

    // ========================================
    // KONFIRMASI PEMBAYARAN (VERIFIKASI LUNAS)
    // ========================================
    $(document).on('click', '.btn-konfirmasi', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const id = $(this).data('id');
        const nama = $(this).data('nama');

        Swal.fire({
            title: 'Verifikasi Tagihan Lunas',
        html: (() => {
                const opts = rekeningList.map(r =>
                    `<option value="${r.id}">${r.nama_bank}</option>`
                ).join('');
                return `
                    <p class="mb-3">Konfirmasi pembayaran untuk <strong>${nama}</strong>?</p>
                    <div class="text-start">
                        <label class="form-label small fw-semibold mb-1">Tipe Pembayaran</label>
                        <select id="swal-type-bayar" class="form-select form-select-sm mb-3">
                            <option value="">— Pilih Metode —</option>
                            ${opts}
                            <option value="cash">Cash / Tunai</option>
                        </select>
                        <label class="form-label small fw-semibold mb-1">Upload Bukti (Opsional)</label>
                        <input type="file" id="swal-bukti" class="form-control form-control-sm" accept="image/*,.pdf">
                    </div>
                `;
            })(),
            icon: 'question',
            showConfirmButton: true,
            showCancelButton: true,
            showDenyButton: false,
            confirmButtonText: '<i class="ri-checkbox-circle-line me-1"></i>Verifikasi Lunas',
            cancelButtonText: 'Batal',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-sm me-2',
                cancelButton: 'btn btn-sm btn-secondary'
            },
            allowOutsideClick: false,
            allowEscapeKey: true,
            didOpen: () => {
                // Force style confirm button
                const confirmBtn = Swal.getConfirmButton();
                if (confirmBtn) {
                    confirmBtn.style.background = '#16a34a';
                    confirmBtn.style.color = '#fff';
                    confirmBtn.style.border = '1px solid #16a34a';
                }
                // Force hide deny button if somehow rendered
                const denyBtn = Swal.getDenyButton();
                if (denyBtn) denyBtn.style.display = 'none';
            },
            preConfirm: () => {
                const typeBayar = document.getElementById('swal-type-bayar').value;
                if (!typeBayar) {
                    Swal.showValidationMessage('Pilih tipe pembayaran terlebih dahulu');
                    return false;
                }
                return typeBayar;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();

                const formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('type_pembayaran', result.value);

                const buktiFile = document.getElementById('swal-bukti') ? document.getElementById('swal-bukti').files[0] : null;
                if (buktiFile) {
                    formData.append('bukti_pembayaran', buktiFile);
                }

                $.ajax({
                    url: `/dashboard/admin/tagihan/konfirmasi/${id}`,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(resp) {
                        hideLoading();
                        if (resp.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tagihan Lunas!',
                                html: `
                                <p>Pembayaran <strong>${nama}</strong> berhasil dikonfirmasi.</p>
                                `,
                                showConfirmButton: false,
                                showDenyButton: false,
                                showCancelButton: false,
                                timer: 4000,
                                timerProgressBar: true,
                                allowOutsideClick: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire('Gagal!', resp.message || 'Terjadi kesalahan.', 'error');
                        }
                    },
                    error: function(xhr) {
                        hideLoading();
                        const msg = xhr.responseJSON?.message || 'Terjadi kesalahan server.';
                        Swal.fire('Gagal!', msg, 'error');
                    }
                });
            }
        });
    });

    // ========================================
    // MASS TAGIHAN
    // ========================================
    $('#modalMassTagihan').on('shown.bs.modal', function () {
        flatpickr(".flatpickr-select-start-all", {
            dateFormat: "Y-m-d",
            defaultDate: new Date(),
        allowInput: true,
        minDate: null,
        disableMobile: true
        });
        flatpickr(".flatpickr-select-start-end", {
            dateFormat: "Y-m-d",
            defaultDate: new Date().fp_incr(7),
        allowInput: true,
        minDate: null,
        disableMobile: true
        });

        // Reset search dan checkbox saat modal dibuka
        $('#searchPelanggan').val('');
        $('#selectAllPelanggan').prop('checked', false);
        $('.pelanggan-checkbox').prop('checked', false);
        $('.pelanggan-item').show();
        updateSelectedCount();
    });

    // ========================================
    // SEARCH PELANGGAN
    // ========================================
    $(document).on('keyup input paste', '#searchPelanggan', function() {
        const searchTerm = $(this).val().toLowerCase().trim();

        // Hapus pesan "tidak ada hasil" jika ada
        $('#noResultMessage').remove();

        if (searchTerm === '') {
            $('.pelanggan-item').show();
            updateSelectAllState();
            return;
        }

        let visibleCount = 0;
        $('.pelanggan-item').each(function() {
            const $item = $(this);
            const nama = String($item.attr('data-nama') || '').toLowerCase();
            const nomerId = String($item.attr('data-nomer-id') || '').toLowerCase();
            const wa = String($item.attr('data-wa') || '').toLowerCase();

            // Normalize search term (hapus spasi, dash, dll untuk nomor)
            const normalizedSearch = searchTerm.replace(/[\s\-+]/g, '');
            const normalizedWa = wa.replace(/[\s\-+]/g, '');

            if (nama.includes(searchTerm) ||
                nomerId.includes(searchTerm) ||
                wa.includes(searchTerm) ||
                normalizedWa.includes(normalizedSearch)) {
                $item.show();
                visibleCount++;
            } else {
              // Sembunyikan saja tanpa menghapus pilihan supaya tidak hilang saat berganti search
              $item.hide();
            }
        });

        // Update select all state setelah filter
        updateSelectAllState();
        updateSelectedCount();

        // Jika tidak ada hasil, tampilkan pesan
        if (visibleCount === 0) {
            $('#pelangganList').append('<div id="noResultMessage" class="text-center py-3 text-muted"><i class="ri-search-line me-1"></i>Tidak ada hasil ditemukan</div>');
        }
    });

    // ========================================
    // SELECT ALL
    // ========================================
    $('#selectAllPelanggan').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.pelanggan-item:visible .pelanggan-checkbox').prop('checked', isChecked);
        updateSelectedCount();
    });

    // ========================================
    // INDIVIDUAL CHECKBOX
    // ========================================
    $(document).on('change', '.pelanggan-checkbox', function() {
        updateSelectedCount();
        updateSelectAllState();
    });

    // ========================================
    // UPDATE SELECTED COUNT
    // ========================================
    function updateSelectedCount() {
        const count = $('.pelanggan-checkbox:checked').length;
        $('#selectedCount').text(count + ' dipilih');
        $('#submitCount').text(count);

        // Disable submit jika tidak ada yang dipilih
        if (count === 0) {
            $('#btnSubmitMass').prop('disabled', true).addClass('opacity-50');
        } else {
            $('#btnSubmitMass').prop('disabled', false).removeClass('opacity-50');
        }
    }

    // ========================================
    // UPDATE SELECT ALL STATE
    // ========================================
    function updateSelectAllState() {
        const visibleCheckboxes = $('.pelanggan-item:visible .pelanggan-checkbox');
        const checkedCheckboxes = $('.pelanggan-item:visible .pelanggan-checkbox:checked');

        if (visibleCheckboxes.length === 0) {
            $('#selectAllPelanggan').prop('checked', false);
        } else {
            $('#selectAllPelanggan').prop('checked', visibleCheckboxes.length === checkedCheckboxes.length);
        }
    }

    // ========================================
    // FORM SUBMIT VALIDATION
    // ========================================
    $('#formMassTagihan').on('submit', function(e) {
        const selectedCount = $('.pelanggan-checkbox:checked').length;
        if (selectedCount === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih minimal 1 pelanggan untuk dibuatkan tagihan.',
                confirmButtonText: 'OK'
            });
            return false;
        }

        showLoading();
    });

    // ========================================
    // ? BUTTON DETAIL - SHOW MODAL
    // ========================================
    $(document).on('click', '.btn-detail', function() {
        const $row = $(this).closest('tr');

        // Ambil data dari table cells
        // Ambil data utama sesuai urutan kolom tabel
        const nomorId = $row.find('.badge.bg-label-dark').text().trim();
        const namaLengkap = $row.find('td:nth-child(4) strong').text().trim();
        const noWhatsapp = $row.find('code').text().trim().replace(/\D/g, '');
        const noWhatsappDisplay = $row.find('code').text().trim();
        const status = $row.find('td:nth-child(6) .badge').text().trim();
        const paket = $row.find('td:nth-child(7) .badge').text().trim();
        const harga = $row.find('td:nth-child(8) strong').text().trim();

        // Data dari attribute
        const alamat = $row.data('alamat') || '-';
        const kecamatan = $row.data('kecamatan') || '-';
        const kabupaten = $row.data('kabupaten') || '-';
        const provinsi = $row.data('provinsi') || '-';
        const kecepatan = $row.data('kecepatan') || '-';
        const tanggalMulai = $row.data('tanggal-mulai') || '-';
        const jatuhTempo = $row.data('jatuh-tempo') || '-';
        const catatan = $row.data('catatan') || '-';
        const buktiUrl = $row.data('bukti') || '';

        // Badge status color
        const statusClass = status.toLowerCase().includes('lunas') ? 'bg-success' : 'bg-danger';
        const statusIcon = status.toLowerCase().includes('lunas') ? 'checkbox-circle' : 'close-circle';

        // Build modal content
        const modalContent = `
            <div class="customer-header-info">
                <div class="customer-avatar">
                    ${namaLengkap.charAt(0).toUpperCase()}
                </div>
                <h5 class="customer-name">${namaLengkap}</h5>
                <span class="badge ${statusClass} customer-status">
                    <i class="ri-${statusIcon}-line me-1"></i>
                    ${status}
                </span>
            </div>

            <!-- Informasi Dasar -->
            <div class="detail-section">
                <h6><i class="ri-user-3-line"></i>Informasi Dasar</h6>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-barcode-line"></i>
                        Nomor ID
                    </div>
                    <div class="detail-value"><strong>${nomorId}</strong></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-user-line"></i>
                        Nama Lengkap
                    </div>
                    <div class="detail-value">${namaLengkap}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-whatsapp-line"></i>
                        WhatsApp
                    </div>
                    <div class="detail-value">
                        <a href="https://wa.me/${noWhatsapp}" target="_blank" class="text-success text-decoration-none">
                            <i class="ri-whatsapp-line me-1"></i>${noWhatsappDisplay}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alamat -->
            <div class="detail-section">
                <h6><i class="ri-map-pin-line"></i>Alamat Lengkap</h6>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-map-2-line"></i>
                        Alamat
                    </div>
                    <div class="detail-value">${alamat}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-building-line"></i>
                        Kecamatan
                    </div>
                    <div class="detail-value">${kecamatan}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-map-pin-range-line"></i>
                        Kabupaten
                    </div>
                    <div class="detail-value">${kabupaten}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-global-line"></i>
                        Provinsi
                    </div>
                    <div class="detail-value">${provinsi}</div>
                </div>
            </div>

            <!-- Paket Internet -->
            <div class="detail-section">
                <h6><i class="ri-wifi-line"></i>Paket Internet</h6>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-box-3-line"></i>
                        Nama Paket
                    </div>
                    <div class="detail-value">${paket}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-speed-line"></i>
                        Kecepatan
                    </div>
                    <div class="detail-value"><strong>${kecepatan}</strong></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-money-dollar-circle-line"></i>
                        Harga
                    </div>
                    <div class="detail-value"><strong class="text-primary">${harga}</strong></div>
                </div>
            </div>

            <!-- Tagihan -->
            <div class="detail-section">
                <h6><i class="ri-calendar-check-line"></i>Detail Tagihan</h6>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-calendar-line"></i>
                        Tanggal Mulai
                    </div>
                    <div class="detail-value">${tanggalMulai}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-calendar-close-line"></i>
                        Jatuh Tempo
                    </div>
                    <div class="detail-value"><strong class="text-danger">${jatuhTempo}</strong></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="ri-file-text-line"></i>
                        Catatan
                    </div>
                    <div class="detail-value">${catatan}</div>
                </div>

            </div>
        `;

        // Populate modal dan tampilkan
        $('#detailModal .modal-body').html(modalContent);
        $('#detailModal').modal('show');
    });
});
</script>
@endsection

@section('content')
<!-- Loading Overlay -->
<div class="loading-overlay">
    <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div class="container-fluid px-4 py-4">
  <!-- FILTER & SEARCH REMOVED -->

  <!-- ========================================= -->
  <!-- DAFTAR TAGIHAN -->
  <!-- ========================================= -->
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-header border-0 bg-white pt-4 pb-3 px-4">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <h4 class="mb-1 fw-bold text-dark d-flex align-items-center">
            <i class="ri-file-list-3-line me-2"></i>Daftar Tagihan
          </h4>
          <p class="mb-0 text-muted small">Kelola seluruh tagihan pelanggan secara efisien.</p>
        </div>

        <div class="d-flex align-items-center gap-2">
          @if($tagihans->total() > 0)
            <span class="badge rounded-pill bg-light text-dark border px-3 py-2 fw-medium me-2" style="font-size: 0.85rem;">
              <i class="ri-database-2-line me-1"></i>
              {{ $tagihans->total() }} Tagihan
            </span>
          @endif

          <button type="button" id="btnExportBelumLunas" class="btn btn-dark rounded-3 px-3 py-2 fw-medium shadow-sm">
            <i class="ri-file-excel-line me-1"></i>Export Belum Lunas
          </button>
          <button type="button" class="btn btn-dark rounded-3 px-3 py-2 fw-medium shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahTagihan">
            <i class="ri-add-line me-1"></i>Tambah Tagihan
          </button>
          <button type="button" class="btn btn-dark rounded-3 px-3 py-2 fw-medium shadow-sm" data-bs-toggle="modal" data-bs-target="#modalMassTagihan">
            <i class="ri-user-settings-line me-1"></i>Tagihan Massal
          </button>
        </div>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="width: 100%;">
          <thead style="background-color: #f8f9fa; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
            <tr>
              <th class="text-uppercase text-muted fw-bold py-3 px-4" style="font-size: 0.75rem; letter-spacing: 0.5px; border: none;">No</th>
              <th class="text-uppercase text-muted fw-bold py-3" style="font-size: 0.75rem; letter-spacing: 0.5px; border: none;"><i class="ri-eye-line me-1"></i>Detail</th>
              <th class="text-uppercase text-muted fw-bold py-3" style="font-size: 0.75rem; letter-spacing: 0.5px; border: none;"><i class="ri-barcode-line me-1"></i>No. ID</th>
              <th class="text-uppercase text-muted fw-bold py-3" style="font-size: 0.75rem; letter-spacing: 0.5px; border: none;"><i class="ri-user-3-line me-1"></i>Nama</th>
              <th class="text-uppercase text-muted fw-bold py-3" style="font-size: 0.75rem; letter-spacing: 0.5px; border: none;"><i class="ri-whatsapp-line me-1"></i>No. WA</th>
              <th class="text-uppercase text-muted fw-bold py-3" style="font-size: 0.75rem; letter-spacing: 0.5px; border: none;"><i class="ri-shield-check-line me-1"></i>Status</th>
              <th class="text-uppercase text-muted fw-bold py-3" style="font-size: 0.75rem; letter-spacing: 0.5px; border: none;"><i class="ri-money-dollar-circle-line me-1"></i>Harga</th>
              <th class="text-uppercase text-muted fw-bold py-3" style="font-size: 0.75rem; letter-spacing: 0.5px; border: none;"><i class="ri-settings-3-line me-1"></i>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($tagihans as $item)
            @php
              $status = strtolower($item['status_pembayaran'] ?? '');
              $badgeClass = match($status) {
                'lunas' => 'badge bg-success',
                'belum bayar' => 'badge bg-danger',
                default => 'badge bg-secondary',
              };

              $alamatParts = [];
              if($item['alamat_jalan']) $alamatParts[] = $item['alamat_jalan'];
              if($item['rt'] || $item['rw']) $alamatParts[] = 'RT '.$item['rt'].' / RW '.$item['rw'];
              if($item['desa']) $alamatParts[] = 'Desa '.$item['desa'];
              if($item['kecamatan']) $alamatParts[] = 'Kecamatan '.$item['kecamatan'];
              if($item['kabupaten']) $alamatParts[] = 'Kabupaten '.$item['kabupaten'];
              if($item['provinsi']) $alamatParts[] = $item['provinsi'];
              $alamatLengkap = implode(', ', $alamatParts);

              $buktiUrl = !empty($item['bukti_pembayaran']) ? asset('storage/kwitansi/' . $item['bukti_pembayaran']) : '';
            @endphp
            <tr
              data-alamat="{{ $alamatLengkap }}"
              data-kecamatan="{{ $item['kecamatan'] ?? '-' }}"
              data-kabupaten="{{ $item['kabupaten'] ?? '-' }}"
              data-provinsi="{{ $item['provinsi'] ?? '-' }}"
              data-kecepatan="{{ $item['paket']['kecepatan'] ?? '-' }} Mbps"
              data-tanggal-mulai="{{ $item['tanggal_mulai'] ? \Carbon\Carbon::parse($item['tanggal_mulai'])->format('d M Y') : '-' }}"
              data-jatuh-tempo="{{ $item['tanggal_berakhir'] ? \Carbon\Carbon::parse($item['tanggal_berakhir'])->format('d M Y') : '-' }}"
              data-catatan="{{ $item['catatan'] ?? '-' }}"
              data-bukti="{{ $buktiUrl }}"
            >
              <td class="text-muted fw-semibold px-4" style="width: 60px;">{{ ($tagihans->firstItem() ?? 1) + $loop->index }}</td>
              <td>
                <button class="btn btn-sm btn-outline-secondary rounded-2 btn-detail px-2 py-1" style="border-color: #e2e8f0; color: #64748b;" title="Lihat Detail">
                  <i class="ri-eye-line"></i>
                </button>
              </td>
              <td><span class="badge rounded-pill bg-light text-dark border px-3 py-1 fw-medium" style="font-size: 0.8rem;">{{ $item['nomer_id'] }}</span></td>
              <td><strong class="text-dark text-uppercase fw-bold">{{ $item['nama_lengkap'] }}</strong></td>
              <td style="min-width: 180px;">
                <a href="https://wa.me/{{ $item['no_whatsapp'] }}" target="_blank" class="text-decoration-none">
                  <span class="badge rounded-pill bg-dark text-white px-3 py-2 fw-medium shadow-sm d-inline-flex align-items-center" style="font-size: 0.8rem;">
                    <i class="ri-whatsapp-fill fs-6 me-1"></i>{{ $item['no_whatsapp'] }}
                  </span>
                </a>
              </td>
              <td>
                <span class="{{ $badgeClass }} rounded-pill px-3 py-2 fw-medium shadow-sm" style="font-size: 0.8rem;">
                  <i class="ri-{{ $status == 'lunas' ? 'checkbox-circle' : 'close-circle' }}-line me-1"></i>
                  {{ ucfirst($status ?: '-') }}
                </span>
              </td>
              <td style="min-width: 120px;">
                <div class="fw-bold text-secondary" style="font-size: 0.75rem; line-height: 1.2;">Rp</div>
                <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ number_format($item['paket']['harga'] ?? 0, 0, ',', '.') }}</div>
              </td>
              <td style="min-width: 140px;">
                <div class="d-flex flex-column gap-2">
                  <button type="button"
                    class="btn btn-sm btn-success rounded-3 w-100 fw-medium shadow-sm btn-konfirmasi"
                    style="background:#16a34a; border-color:#16a34a;"
                    data-id="{{ $item['id'] }}"
                    data-nama="{{ $item['nama_lengkap'] }}"
                    title="Verifikasi Lunas">
                    <i class="ri-checkbox-circle-line me-1"></i>Verifikasi Lunas
                  </button>
                  <div class="d-flex gap-2">
                    <button type="button"
                      class="btn btn-sm btn-outline-secondary rounded-2 px-2 py-1 flex-fill"
                      style="border-color: #e2e8f0; color: #64748b;"
                      data-bs-toggle="modal"
                      data-bs-target="#modalEditTagihan-{{ $item['id'] }}"
                      title="Edit">
                      <i class="ri-edit-2-line"></i>
                    </button>

                    <form action="{{ route('tagihan.destroy', $item['id']) }}" method="POST" class="delete-form flex-fill m-0">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-secondary rounded-2 px-2 py-1 w-100" style="border-color: #e2e8f0; color: #64748b;" title="Hapus">
                        <i class="ri-delete-bin-line"></i>
                      </button>
                    </form>
                  </div>
                </div>
              </td>
            </tr>
            @empty
            <tr class="empty-state-row">
              <td colspan="8" class="text-center">
                <div class="empty-state-content">
                  <div class="mb-3">
                    <i class="ri-inbox-line" style="font-size: 4rem; color: #ddd;"></i>
                  </div>

                  @if(request()->hasAny(['search', 'periode']))
                    <h5 class="text-muted mb-2">
                      <i class="ri-search-eye-line me-2"></i>Data Tidak Ditemukan
                    </h5>
                    <p class="text-muted mb-3">
                      Tidak ada data yang sesuai dengan filter Anda.
                    </p>

                    <div class="mb-3">
                      @if(request('search'))
                        <span class="badge bg-label-primary me-2" style="padding: 8px 16px;">
                          <i class="ri-search-line me-1"></i>
                          Pencarian: "{{ request('search') }}"
                        </span>
                      @endif
                      @if(request('periode'))
                        @php
                          try {
                              $dateObj = \Carbon\Carbon::createFromFormat('Y-m', request('periode'));
                              $periodeLabel = $dateObj->translatedFormat('F Y');
                          } catch (\Exception $e) {
                              $periodeLabel = request('periode');
                          }
                        @endphp
                        <span class="badge bg-label-primary me-2" style="padding: 8px 16px;">
                          <i class="ri-calendar-line me-1"></i>
                          Periode: {{ $periodeLabel }}
                        </span>
                      @endif
                    </div>

                    <a href="{{ route('tagihan.get') }}" class="btn btn-primary mt-2">
                      <i class="ri-refresh-line me-1"></i>Reset & Tampilkan Semua Data
                    </a>
                  @else
                    <h5 class="text-muted mb-2">
                      <i class="ri-file-list-line me-2"></i>Belum Ada Data Tagihan
                    </h5>
                    <p class="text-muted">
                      Saat ini belum ada data tagihan yang terdaftar dalam sistem.
                    </p>
                  @endif
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if($tagihans->hasPages())
      <div class="pagination-wrapper">
        <div class="pagination-info">
          Menampilkan <strong>{{ $tagihans->firstItem() ?? 0 }}</strong> - <strong>{{ $tagihans->lastItem() ?? 0 }}</strong>
          dari <strong>{{ $tagihans->total() }}</strong> tagihan
        </div>
        <div>
          {{ $tagihans->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
      </div>
    @endif
  </div>
</div>


{{-- MODAL DETAIL --}}
<div class="modal fade" id="detailModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary py-4">
        <h5 class="modal-title text-white fw-bold">
          <i class="ri-information-line me-2"></i>Detail Pelanggan
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Content will be inserted via JavaScript -->
      </div>
      {{-- <div class="modal-footer py-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i>Tutup
        </button>
      </div> --}}
    </div>
  </div>
</div>


<!-- ========================================= -->
<!-- MODAL: TAMBAH TAGIHAN -->
<!-- ========================================= -->
<div class="modal fade" id="modalTambahTagihan" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form action="{{ route('tagihan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="modal-header bg-primary">
          <h5 class="modal-title text-white fw-bold">
            <i class="ri-add-circle-line me-2"></i>Tambah Tagihan Baru
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <!-- Pilih Pelanggan -->
            <div class="col-12">
              <label class="form-label fw-semibold">Pilih Pelanggan <span class="text-danger">*</span></label>
              <select id="pelangganSelect" name="pelanggan_id" class="form-select select2" required>
                <option value=""></option>
                <!-- Options diload via AJAX Select2 -->
              </select>
            </div>

            <input type="hidden" name="paket_id" id="paket_id">

            <!-- Info Pelanggan -->
            <div class="col-12 mt-4">
              <h6 class="text-primary fw-bold mb-3">
                <i class="ri-user-3-line me-2"></i>Informasi Pelanggan
              </h6>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Nama Lengkap</label>
              <input type="text" id="nama_lengkap" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Nomor ID</label>
              <input type="text" id="nomer_id" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Nomor WhatsApp</label>
              <input type="text" id="no_whatsapp" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Kode Pos</label>
              <input type="text" id="kode_pos" class="form-control bg-light" readonly>
            </div>

            <!-- Alamat -->
            <div class="col-12 mt-4">
              <h6 class="text-primary fw-bold mb-3">
                <i class="ri-map-pin-line me-2"></i>Alamat Lengkap
              </h6>
            </div>

            <div class="col-12">
              <label class="form-label small text-muted">Alamat Jalan</label>
              <input type="text" id="alamat_jalan" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-3">
              <label class="form-label small text-muted">RT</label>
              <input type="text" id="rt" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-3">
              <label class="form-label small text-muted">RW</label>
              <input type="text" id="rw" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Desa/Kelurahan</label>
              <input type="text" id="desa" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-4">
              <label class="form-label small text-muted">Kecamatan</label>
              <input type="text" id="kecamatan" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-4">
              <label class="form-label small text-muted">Kabupaten/Kota</label>
              <input type="text" id="kabupaten" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-4">
              <label class="form-label small text-muted">Provinsi</label>
              <input type="text" id="provinsi" class="form-control bg-light" readonly>
            </div>

            <!-- Paket -->
            <div class="col-12 mt-4">
              <h6 class="text-primary fw-bold mb-3">
                <i class="ri-box-3-line me-2"></i>Informasi Paket
              </h6>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Nama Paket</label>
              <input type="text" id="paket" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Harga Paket</label>
              <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" id="harga" name="harga" class="form-control bg-light" readonly>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Kecepatan</label>
              <div class="input-group">
                <input type="text" id="kecepatan" class="form-control bg-light" readonly>
                <span class="input-group-text">Mbps</span>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label small text-muted">Masa Pembayaran</label>
              <div class="input-group">
                <input type="text" id="masa_pembayaran" class="form-control bg-light" readonly>
                <span class="input-group-text">Hari</span>
              </div>
            </div>

            <!-- Tagihan -->
            <div class="col-12 mt-4">
              <h6 class="text-primary fw-bold mb-3">
                <i class="ri-calendar-check-line me-2"></i>Detail Tagihan
              </h6>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
              <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
              <input type="date" id="tanggal_berakhir" name="tanggal_berakhir" class="form-control bg-light" readonly required>
            </div>

            <div class="col-12">
              <label class="form-label">Catatan (Opsional)</label>
              <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Upload Bukti Pembayaran (Opsional)</label>
              <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*,.pdf">
              <small class="text-muted">Format: JPG, PNG, PDF | Max: 2MB</small>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="ri-close-line me-1"></i>Batal
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="ri-save-line me-1"></i>Simpan Tagihan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========================================= -->
<!-- MODAL: EDIT TAGIHAN (FOREACH) -->
<!-- ========================================= -->
@foreach($tagihans as $tagihan)
<div class="modal fade" id="modalEditTagihan-{{ $tagihan['id'] }}" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('tagihan.update', $tagihan['id']) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-bold">
            <i class="ri-edit-2-line me-2"></i>Edit Tagihan
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Nama Pelanggan</label>
              <input type="text" class="form-control bg-light" value="{{ $tagihan['nama_lengkap'] ?? '-' }}" readonly>
            </div>

            <input type="hidden" name="pelanggan_id" value="{{ $tagihan['pelanggan_id'] ?? '' }}">
            <input type="hidden" name="paket_id" value="{{ $tagihan['paket']['id'] ?? '' }}">

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Mulai</label>
              <input type="text" name="tanggal_mulai" class="form-control flatpickr-edit-start" value="{{ $tagihan['tanggal_mulai'] }}" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Jatuh Tempo</label>
              <input type="text" name="tanggal_berakhir" class="form-control flatpickr-edit-end" value="{{ $tagihan['tanggal_berakhir'] }}" required>
            </div>

            <div class="col-12">
              <label class="form-label">Catatan</label>
              <textarea class="form-control" name="catatan" rows="2">{{ $tagihan['catatan'] ?? '' }}</textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Bukti Pembayaran</label>
              <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*,.pdf">
              <small class="text-muted">Format: JPG, PNG, PDF | Max: 2MB</small>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning">
            <i class="ri-save-line me-1"></i>Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

<!-- ========================================= -->
<!-- MODAL: BROADCAST TAGIHAN (AJAX-BASED) -->
<!-- ========================================= -->
<!-- ========================================= -->
<!-- MODAL: BROADCAST TAGIHAN (BATCH + MANUAL) -->
<!-- ========================================= -->
<div class="modal fade" id="modalMassTagihan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
      
      <!-- Modern Header -->
      <div class="modal-header border-0 py-4 px-5" style="background: linear-gradient(135deg, #18181b 0%, #27272a 100%);">
        <div>
            <h4 class="modal-title fw-bold text-white mb-1">
            <i class="ri-rocket-line me-2 text-warning"></i>Generator Tagihan Massal
            </h4>
            <p class="text-white-50 mb-0 small">Buat tagihan untuk banyak pelanggan sekaligus dengan cepat dan aman.</p>
        </div>
        <button type="button" class="btn btn-icon btn-sm btn-dark rounded-circle text-white-50 hover-white" data-bs-dismiss="modal">
            <i class="ri-close-line fs-5"></i>
        </button>
      </div>

      <div class="modal-body p-0">
        <div class="d-flex flex-column flex-lg-row h-100" style="min-height: 500px;">
            
            <!-- Sidebar / Mode Selection -->
            <div class="col-lg-3 bg-light border-end p-4">
                <label class="small fw-bold text-uppercase text-muted mb-3 tracking-wide">Pilih Metode</label>
                
                <div class="d-grid gap-3">
                    <label class="mode-card p-3 rounded-3 border bg-white cursor-pointer position-relative active-mode" id="labelModeAll">
                        <input type="radio" name="broadcastMode" value="all" class="d-none" checked>
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-box bg-success-subtle text-success rounded-circle p-2 me-3">
                                <i class="ri-broadcast-line fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">Broadcast Semua</h6>
                        </div>
                        <p class="small text-muted mb-0 lh-sm">
                            Generate tagihan otomatis untuk setiap pelanggan yang eligible.
                        </p>
                        <div class="active-indicator"></div>
                    </label>

                    <label class="mode-card p-3 rounded-3 border bg-white cursor-pointer position-relative" id="labelModeManual">
                        <input type="radio" name="broadcastMode" value="manual" class="d-none">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-box bg-primary-subtle text-primary rounded-circle p-2 me-3">
                                <i class="ri-checkbox-multiple-line fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">Pilih Manual</h6>
                        </div>
                        <p class="small text-muted mb-0 lh-sm">
                            Cari dan pilih pelanggan spesifik secara manual.
                        </p>
                        <div class="active-indicator"></div>
                    </label>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-lg-9 p-5 position-relative">
                
                <!-- SECTION: ALL -->
                <div id="sectionAll" class="mode-section animate__animated animate__fadeIn">
                    <div class="text-center py-5">
                       <div class="mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle" style="width: 80px; height: 80px;">
                                <i class="ri-user-star-line fs-1"></i>
                            </div>
                       </div>
                       
                       <h3 class="fw-bold text-dark mb-2">Siap untuk Broadcast?</h3>
                       <p class="text-muted w-75 mx-auto mb-4">
                            Sistem akan memindai <span class="fw-bold text-dark">semua pelanggan</span> yang belum memiliki tagihan di bulan ini.
                       </p>

                       <div class="card bg-dark bg-gradient text-white border-0 d-inline-block px-5 py-3 rounded-4 shadow-lg">
                            <h1 class="display-4 fw-bold mb-0" id="broadcastCount">
                                <span class="spinner-border spinner-border-sm"></span>
                            </h1>
                            <span class="text-white small text-uppercase spacing-2">Pelanggan Eligible</span>
                       </div>
                    </div>
                </div>

                <!-- SECTION: MANUAL -->
                <div id="sectionManual" class="mode-section d-none animate__animated animate__fadeIn">
                    <div class="d-flex justify-content-between align-items-end mb-4">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Pilih Pelanggan</h5>
                            <p class="text-muted small mb-0">Cari pelanggan yang ingin dibuatkan tagihan.</p>
                        </div>
                        <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm" id="manualSelectedCount">0 Terpilih</span>
                    </div>

                    <div class="input-group input-group-lg border rounded-3 overflow-hidden shadow-sm mb-4">
                        <span class="input-group-text bg-white border-0 ps-4"><i class="ri-search-line text-muted"></i></span>
                        <input type="text" class="form-control border-0 py-3" id="manualSearchInput" placeholder="Ketik nama, ID, atau alamat pelanggan..." style="font-size: 0.95rem;">
                    </div>

                    <div class="border rounded-3 overflow-hidden bg-white shadow-sm" style="height: 350px; overflow-y: auto;">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light sticky-top" style="z-index: 5;">
                                <tr>
                                    <th width="60" class="text-center py-3">
                                        <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" id="checkAllManual" style="cursor: pointer;">
                                        </div>
                                    </th>
                                    <th class="py-3 text-secondary small text-uppercase">Pelanggan</th>
                                    <th class="py-3 text-secondary small text-uppercase">Paket</th>
                                    <th class="py-3 text-secondary small text-uppercase">Lokasi</th>
                                </tr>
                            </thead>
                            <tbody id="manualTableBody" class="border-top-0">
                                <!-- AJAX CONTENT -->
                            </tbody>
                        </table>
                        
                        <!-- Empty States & Loading -->
                        <div id="manualLoading" class="text-center py-5 d-none">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="small text-muted mt-2">Memuat data...</p>
                        </div>
                        <div id="manualEmpty" class="text-center py-5 text-muted d-none">
                            <div class="mb-2"><i class="ri-search-2-line fs-1 opacity-25"></i></div>
                            <p class="mb-0">Tidak ditemukan data yang cocok</p>
                        </div>
                         <div class="text-center p-3 border-top bg-light">
                             <button type="button" id="btnLoadMoreManual" class="btn btn-sm btn-outline-dark rounded-pill px-4 d-none">
                                Muat Lebih Banyak
                             </button>
                        </div>
                    </div>
                </div>

                <!-- Footer Config (Always Visible) -->
                <div class="mt-4 pt-4 border-top">
                    <div class="row g-4 align-items-end">
                        <div class="col-md-6">
                             <label class="form-label small fw-bold text-muted text-uppercase mb-2">Tanggal Mulai</label>
                             <div class="input-group">
                                 <span class="input-group-text bg-light border-end-0"><i class="ri-calendar-line"></i></span>
                                 <input type="text" id="broadcastTanggalMulai" class="form-control border-start-0 ps-0 date-picker-flat" placeholder="YYYY-MM-DD" style="font-weight: 500;">
                             </div>
                        </div>
                        <div class="col-md-6">
                             <label class="form-label small fw-bold text-muted text-uppercase mb-2">Jatuh Tempo</label>
                             <div class="input-group">
                                 <span class="input-group-text bg-light border-end-0"><i class="ri-calendar-check-line"></i></span>
                                 <input type="text" id="broadcastTanggalBerakhir" class="form-control border-start-0 ps-0 text-danger date-picker-flat" placeholder="YYYY-MM-DD" style="font-weight: 600;">
                             </div>
                        </div>
                        <div class="col-12">
                             <button type="button" class="btn btn-dark btn-lg w-100 rounded-3 shadow-lg" id="btnBroadcastSubmit">
                                <span class="d-flex align-items-center justify-content-center gap-2">
                                    <i class="ri-flashlight-fill text-warning"></i>
                                    <span>Proses Tagihan</span>
                                </span>
                             </button>
                        </div>
                    </div>
                </div>
                
                 <!-- Progress Overlay (Absolute) -->
                <div id="broadcastProgressSection" class="position-absolute top-0 start-0 w-100 h-100 bg-white d-flex flex-column justify-content-center align-items-center d-none" style="z-index: 50; border-radius: 0 0 16px 0;">
                     <div class="text-center w-50">
                          <div class="mb-4">
                               <div class="spinner-grow text-warning" role="status" style="width: 3rem; height: 3rem;"></div>
                          </div>
                          <h4 class="fw-bold mb-2">Sedang Memproses...</h4>
                          <p class="text-muted mb-4 small">Mohon jangan tutup halaman ini. Sistem sedang membuat tagihan secara bertahap.</p>
                          
                          <div class="progress rounded-pill bg-light shadow-inner" style="height: 12px;">
                            <div class="progress-bar bg-warning rounded-pill progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" id="broadcastProgressBar"></div>
                          </div>
                          <div class="mt-2 d-flex justify-content-between text-muted x-small fw-bold">
                              <span>PROGRESS</span>
                              <span id="progressText">0%</span>
                          </div>
                     </div>
                </div>
                
                <!-- Result & Success (Absolute) -->
                <div id="broadcastResult" class="position-absolute top-0 start-0 w-100 h-100 bg-white d-flex flex-column justify-content-center align-items-center d-none" style="z-index: 51;">
                    <!-- Injected via JS -->
                </div>

            </div> <!-- End Col-9 -->
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* Custom Styles for Modal */
.cursor-pointer { cursor: pointer; }
.hover-white:hover { background: rgba(255,255,255,0.2) !important; color: white !important; }
.spacing-2 { letter-spacing: 2px; }
.shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.06); }
.x-small { font-size: 0.75rem; }

/* Mode Card Styling */
.mode-card {
    transition: all 0.2s ease;
    border: 1px solid #e5e7eb;
}
.mode-card:hover {
    border-color: #f59e0b; /* Warning color */
    background-color: #fffbeb;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
.mode-card.active-mode {
    border-color: #f59e0b;
    background-color: #fffbeb;
    box-shadow: 0 0 0 1px #f59e0b;
}
.active-indicator {
    display: none;
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    width: 8px;
    height: 8px;
    background: #f59e0b;
    border-radius: 50%;
}
.mode-card.active-mode .active-indicator { display: block; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- VARIABLES ---
    const modal = document.getElementById('modalMassTagihan');
    const bsModal = modal ? new bootstrap.Modal(modal) : null;
    
    // UI Elements
    const modeInputs = document.querySelectorAll('input[name="broadcastMode"]');
    const labelAll = document.getElementById('labelModeAll');
    const labelManual = document.getElementById('labelModeManual');
    
    const sectionAll = document.getElementById('sectionAll');
    const sectionManual = document.getElementById('sectionManual');
    const countEl = document.getElementById('broadcastCount');
    const btnSubmit = document.getElementById('btnBroadcastSubmit');
    const progressSection = document.getElementById('broadcastProgressSection');
    const progressBar = document.getElementById('broadcastProgressBar');
    const progressText = document.getElementById('progressText');
    const resultEl = document.getElementById('broadcastResult');
    
    // Manual Selection Logic Items
    let manualPage = 1;
    let manualQuery = '';
    let manualSelectedIds = new Set(); 
    let isLoadingManual = false;
    
    // Flatpickr Instances
    let broadcastStartPicker = null;
    let broadcastEndPicker = null;

    // Init Flatpickr
    if(document.querySelector('#broadcastTanggalMulai')) {
        broadcastStartPicker = flatpickr('#broadcastTanggalMulai', {
            mode: 'single',
            dateFormat: 'Y-m-d',
            defaultDate: 'today',
            position: 'auto center',
            appendTo: document.body,
            onReady: function(selectedDates, dateStr, instance) {
                instance.calendarContainer.classList.add('broadcast-date-picker');
            }
        });
    }
    if(document.querySelector('#broadcastTanggalBerakhir')) {
        broadcastEndPicker = flatpickr('#broadcastTanggalBerakhir', {
            mode: 'single',
            dateFormat: 'Y-m-d',
            position: 'auto center',
            appendTo: document.body,
            onReady: function(selectedDates, dateStr, instance) {
                instance.calendarContainer.classList.add('broadcast-date-picker');
            }
        });
    }

    // --- INITIALIZATION ---
    if (modal) {
        modal.addEventListener('shown.bs.modal', function() {
            // Reset to defaults
            fetchBroadcastCount(); // Get All Count
            
            // Date Initialization handled by Flatpickr below
            const today = new Date();
            if(broadcastStartPicker) broadcastStartPicker.setDate(today);
            
            // Reset UI
            progressSection.classList.add('d-none');
            resultEl.classList.add('d-none');
            btnSubmit.disabled = false;
            
            // Default Mode: All
            document.querySelector('input[value="all"]').checked = true;
            updateModeUI('all');
            
            // Load Manual Data (Init) if empty
            if (manualSelectedIds.size === 0) {
                 loadManualCustomers(true);
            }
        });
    }

    // --- MODE SWITCHING ---
    modeInputs.forEach(input => {
        input.addEventListener('change', function() {
            updateModeUI(this.value);
        });
    });

    function updateModeUI(mode) {
        // Update Labels
        if (mode === 'all') {
            labelAll.classList.add('active-mode');
            labelManual.classList.remove('active-mode');
            
            sectionAll.classList.remove('d-none');
            sectionManual.classList.add('d-none');
        } else {
            labelAll.classList.remove('active-mode');
            labelManual.classList.add('active-mode');
            
            sectionAll.classList.add('d-none');
            sectionManual.classList.remove('d-none');
        }
    }

    // --- LOGIC: FETCH COUNT (ALL) ---
    function fetchBroadcastCount() {
        countEl.innerHTML = '<span class="spinner-border spinner-border-sm text-warning"></span>';
        fetch('{{ route("tagihan.broadcast.count") }}')
            .then(res => res.json())
            .then(data => {
                countEl.textContent = data.count;
            })
            .catch(err => {
                console.error(err);
                countEl.textContent = '-';
            });
    }

    // --- LOGIC: MANUAL SELECTION ---
    const manualSearchInput = document.getElementById('manualSearchInput');
    const btnLoadMore = document.getElementById('btnLoadMoreManual');

    let searchTimeout;
    manualSearchInput.addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        manualQuery = this.value;
        searchTimeout = setTimeout(() => loadManualCustomers(true), 500);
    });

    btnLoadMore.addEventListener('click', () => loadManualCustomers(false));

    function loadManualCustomers(reset) {
        if (isLoadingManual) return;
        isLoadingManual = true;
        
        if (reset) {
            manualPage = 1;
            document.getElementById('manualTableBody').innerHTML = '';
            document.getElementById('manualEmpty').classList.add('d-none');
        } else {
            manualPage++;
        }

        document.getElementById('manualLoading').classList.remove('d-none');
        btnLoadMore.classList.add('d-none');

        const url = new URL('{{ route("pelanggan.search") }}');
        url.searchParams.set('q', manualQuery);
        url.searchParams.set('page', manualPage);
        url.searchParams.set('filter_no_tagihan', 1);

        fetch(url)
            .then(res => res.json())
            .then(data => {
                document.getElementById('manualLoading').classList.add('d-none');
                isLoadingManual = false;
                
                const customers = data.results || [];
                
                if (reset && customers.length === 0) {
                    document.getElementById('manualEmpty').classList.remove('d-none');
                    return;
                }

                customers.forEach(cx => {
                    const isChecked = manualSelectedIds.has(String(cx.id)) ? 'checked' : '';
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input manual-check" value="${cx.id}" ${isChecked} style="width: 1.2em; height: 1.2em; cursor: pointer;">
                        </td>
                        <td>
                            <div class="fw-bold text-dark text-truncate" style="max-width: 200px;">${cx.nama || cx.text}</div>
                            <div class="x-small text-muted font-monospace">${cx.nomorid || '-'}</div>
                        </td>
                        <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">${cx.paket || '-'}</span></td>
                        <td>
                            <div class="small text-muted text-truncate" style="max-width:180px;">
                                <i class="ri-map-pin-line me-1"></i>${cx.alamat_jalan || '-'}
                            </div>
                        </td>
                    `;
                    document.getElementById('manualTableBody').appendChild(tr);
                });

                if (data.pagination && data.pagination.more) {
                    btnLoadMore.classList.remove('d-none');
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('manualLoading').classList.add('d-none');
                isLoadingManual = false;
            });
    }

    document.getElementById('manualTableBody').addEventListener('change', function(e) {
        if (e.target.classList.contains('manual-check')) {
            const id = e.target.value;
            if (e.target.checked) manualSelectedIds.add(id);
            else manualSelectedIds.delete(id);
            updateManualCounter();
        }
    });

    document.getElementById('checkAllManual').addEventListener('change', function(e) {
        const isChecked = e.target.checked;
        const checks = document.querySelectorAll('.manual-check');
        checks.forEach(chk => {
            chk.checked = isChecked;
            if (isChecked) manualSelectedIds.add(chk.value);
            else manualSelectedIds.delete(chk.value);
        });
        updateManualCounter();
    });

    function updateManualCounter() {
        document.getElementById('manualSelectedCount').textContent = `${manualSelectedIds.size} Terpilih`;
    }

    // --- LOGIC: SUBMIT & BATCH PROCESSING ---
    btnSubmit.addEventListener('click', async function() {
        const mode = document.querySelector('input[name="broadcastMode"]:checked').value;
        const start = document.getElementById('broadcastTanggalMulai').value;
        const end = document.getElementById('broadcastTanggalBerakhir').value;

        if (!start || !end) {
            Swal.fire({
                icon: 'error',
                title: 'Tanggal Belum Lengkap',
                text: 'Harap isi Tanggal Mulai dan Jatuh Tempo.',
                confirmButtonColor: '#18181b'
            });
            return;
        }

        if (new Date(start) > new Date(end)) {
            Swal.fire({
                icon: 'error',
                title: 'Tanggal Tidak Valid',
                text: 'Tanggal Jatuh Tempo harus sama atau setelah Tanggal Mulai.',
                confirmButtonColor: '#18181b'
            });
            return;
        }

        let targetIds = [];

        if (mode === 'manual') {
            targetIds = Array.from(manualSelectedIds);
            if (targetIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum Ada Pelanggan',
                    text: 'Silakan pilih minimal 1 pelanggan pada mode manual.',
                    confirmButtonColor: '#18181b'
                });
                return;
            }
        } else {
             try {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                
                const res = await fetch('{{ route("tagihan.broadcast.ids") }}');
                const data = await res.json();
                targetIds = data.ids || [];
                
                if (targetIds.length === 0) {
                    Swal.fire('Info', 'Tidak ada pelanggan eligible untuk diproses.', 'info');
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = `<span class="d-flex align-items-center justify-content-center gap-2"><i class="ri-flashlight-fill text-warning"></i><span>Proses Tagihan</span></span>`;
                    return;
                }
            } catch (e) {
                console.error(e);
                Swal.fire('Error', 'Gagal mengambil data pelanggan.', 'error');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = `<span class="d-flex align-items-center justify-content-center gap-2"><i class="ri-flashlight-fill text-warning"></i><span>Proses Tagihan</span></span>`;
                return;
            }
        }

        // Confirmation (Using standard Swal but visually consistent)
        const result = await Swal.fire({
            title: 'Konfirmasi Proses',
            html: `Anda akan membuat tagihan untuk <strong>${targetIds.length} pelanggan</strong>.<br>Pastikan tanggal sudah benar.`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#18181b', // Dark
            cancelButtonColor: '#e4e4e7', // Light gray
            confirmButtonText: 'Ya, Proses Sekarang',
            cancelButtonText: '<span style="color:#3f3f46">Batal</span>',
            reverseButtons: true
        });

        if (!result.isConfirmed) {
             btnSubmit.disabled = false;
             btnSubmit.innerHTML = `<span class="d-flex align-items-center justify-content-center gap-2"><i class="ri-flashlight-fill text-warning"></i><span>Proses Tagihan</span></span>`;
             return;
        }

        // START BATCHING
        progressSection.classList.remove('d-none');
        
        let processed = 0;
        let successTotal = 0;
        let failedTotal = 0;
        const total = targetIds.length;
        const batchSize = 25; 
        
        for (let i = 0; i < total; i += batchSize) {
            const chunk = targetIds.slice(i, i + batchSize);
            try {
                const res = await fetch('{{ route("tagihan.broadcast.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        tanggal_mulai: start,
                        tanggal_berakhir: end,
                        pelanggan_ids: chunk
                    })
                });
                
                const json = await res.json();
                if (json.success) {
                    successTotal += (json.processed || 0);
                    failedTotal += (json.failed || 0);
                } else {
                    failedTotal += chunk.length;
                }
            } catch (err) {
                console.error(err);
                failedTotal += chunk.length;
            }

            processed = Math.min(i + batchSize, total);
            // Update UI
            const percent = Math.round((processed / total) * 100);
            progressBar.style.width = `${percent}%`;
            progressText.textContent = `${percent}% (${processed}/${total})`;
        }

        // Finish
        // Show Success in resultEl
        progressSection.classList.add('d-none');
        resultEl.classList.remove('d-none');
        
        resultEl.innerHTML = `
            <div class="text-center animate__animated animate__zoomIn">
                <div class="mb-3 text-success">
                    <i class="ri-checkbox-circle-fill" style="font-size: 5rem;"></i>
                </div>
                <h2 class="fw-bold text-dark mb-2">Selesai!</h2>
                <p class="text-muted mb-4">
                    Berhasil memproses <strong>${successTotal}</strong> tagihan.<br>
                    <span class="text-danger small">${failedTotal > 0 ? failedTotal + ' gagal.' : ''}</span>
                </p>
                <button onclick="window.location.reload()" class="btn btn-dark btn-lg rounded-pill px-5 shadow-lg">
                    <i class="ri-refresh-line me-2"></i>Refresh Halaman
                </button>
            </div>
        `; 
    });

    // Export Belum Lunas Handler
    $('#btnExportBelumLunas').on('click', function(e) {
        e.preventDefault();
        const search = $('input[name="search"]').val();
        const periode = $('input[name="periode"]').val();
        
        let url = '{{ route("tagihan.export.belumlunas") }}';
        const params = new URLSearchParams();
        
        if (search) params.append('search', search);
        if (periode) params.append('periode', periode);
        
        if (params.toString()) {
            url += '?' + params.toString();
        }
        
        window.location.href = url;
    });
});
</script>
@endsection
