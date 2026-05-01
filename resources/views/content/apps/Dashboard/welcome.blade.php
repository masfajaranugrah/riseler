@extends('layouts/layoutMaster')

@section('title', 'Dashboard')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/plugins/monthSelect/style.css">
<style>
/* ========================================= */
/* SHADCN UI STYLE DASHBOARD - BLACK & WHITE */
/* ========================================= */
:root {
  --background: #ffffff;
  --foreground: #09090b;
  --card: #ffffff;
  --card-foreground: #09090b;
  --muted: #f4f4f5;
  --muted-foreground: #71717a;
  --border: #e4e4e7;
  --ring: #18181b;
  --radius: 0.75rem;
}

body {
  background: var(--background);
}

/* FLATPICKR CUSTOM THEME */
.flatpickr-calendar {
  border: 1px solid rgba(0,0,0,0.05) !important;
  box-shadow: 0 20px 40px -10px rgba(0,0,0,0.15) !important;
  border-radius: 16px !important;
  padding: 16px !important;
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

/* Welcome Hero Card - Dark Theme */
.welcome-hero {
  background: #09090b;
  border-radius: var(--radius);
  padding: 2.5rem;
  color: #fafafa;
  position: relative;
  overflow: hidden;
  border: 1px solid #27272a;
}

.welcome-hero::before {
  content: '';
  position: absolute;
  top: -100%;
  right: -50%;
  width: 80%;
  height: 300%;
  background: radial-gradient(ellipse, rgba(255,255,255,0.05) 0%, transparent 50%);
  transform: rotate(-30deg);
}

.welcome-content {
  position: relative;
  z-index: 1;
}

.welcome-hero .greeting {
  font-size: 0.875rem;
  font-weight: 500;
  color: #a1a1aa;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  margin-bottom: 0.5rem;
}

.welcome-hero h1 {
  font-size: 2.25rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
  color: #fafafa;
}

.welcome-hero .description {
  font-size: 1rem;
  color: #a1a1aa;
  max-width: 480px;
  line-height: 1.6;
}

.welcome-time {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: #18181b;
  padding: 0.5rem 1rem;
  border-radius: 9999px;
  font-size: 0.875rem;
  margin-top: 1.5rem;
  color: #a1a1aa;
  border: 1px solid #27272a;
}

.hero-illustration {
  position: relative;
  z-index: 1;
}

.hero-icon-wrapper {
  width: 160px;
  height: 160px;
  background: #18181b;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #27272a;
}

.hero-icon-wrapper i {
  font-size: 4rem;
  color: #52525b;
}

/* Stats Cards - Clean & Minimal */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

@media (max-width: 1200px) {
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 576px) {
  .stats-grid { grid-template-columns: 1fr; }
}

.stat-card {
  background: var(--card);
  border-radius: var(--radius);
  padding: 1.5rem;
  border: 1px solid var(--border);
  transition: all 0.2s ease;
}

.stat-card:hover {
  border-color: #18181b;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  margin-bottom: 1rem;
  background: var(--muted);
  color: var(--foreground);
}

.stat-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--muted-foreground);
  margin-bottom: 0.25rem;
}

.stat-value {
  font-size: 1.875rem;
  font-weight: 700;
  color: var(--foreground);
  line-height: 1;
}

/* Section Headers */
.section-header {
  display: flex;
  align-items: center;
  gap: 0.625rem;
  margin-bottom: 1rem;
}

.section-header h5 {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--foreground);
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.section-header .icon-wrapper {
  width: 28px;
  height: 28px;
  background: var(--muted);
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--muted-foreground);
  font-size: 0.875rem;
}

/* Quick Links Grid */
.quick-links-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
}

@media (max-width: 1200px) {
  .quick-links-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 576px) {
  .quick-links-grid { grid-template-columns: 1fr; }
}

.quick-link {
  background: var(--card);
  border-radius: var(--radius);
  padding: 1.5rem;
  text-decoration: none;
  color: inherit;
  display: block;
  border: 1px solid var(--border);
  transition: all 0.2s ease;
  position: relative;
}

.quick-link:hover {
  border-color: #18181b;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  color: inherit;
  text-decoration: none;
}

.quick-link-icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  margin-bottom: 1rem;
  background: var(--muted);
  color: var(--foreground);
}

.quick-link h6 {
  font-weight: 600;
  font-size: 0.9375rem;
  color: var(--foreground);
  margin-bottom: 0.25rem;
}

.quick-link p {
  font-size: 0.8125rem;
  color: var(--muted-foreground);
  margin: 0;
}

.quick-link .arrow-icon {
  position: absolute;
  top: 1.25rem;
  right: 1.25rem;
  width: 28px;
  height: 28px;
  background: var(--muted);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--muted-foreground);
  font-size: 0.875rem;
  transition: all 0.2s ease;
}

.quick-link:hover .arrow-icon {
  background: #18181b;
  color: #fafafa;
  transform: translateX(2px);
}

/* Animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-in {
  animation: fadeInUp 0.5s ease-out forwards;
}

.delay-1 { animation-delay: 0.05s; opacity: 0; }
.delay-2 { animation-delay: 0.1s; opacity: 0; }
.delay-3 { animation-delay: 0.15s; opacity: 0; }
.delay-4 { animation-delay: 0.2s; opacity: 0; }
.delay-5 { animation-delay: 0.25s; opacity: 0; }

@keyframes wave-animation {
  0% { transform: rotate( 0.0deg) }
  10% { transform: rotate(14.0deg) }
  20% { transform: rotate(-8.0deg) }
  30% { transform: rotate(14.0deg) }
  40% { transform: rotate(-4.0deg) }
  50% { transform: rotate(10.0deg) }
  60% { transform: rotate( 0.0deg) }
  100% { transform: rotate( 0.0deg) }
}

.wave-emoji {
  display: inline-block;
  animation: wave-animation 2.5s infinite;
  transform-origin: 70% 70%;
}
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  
  <!-- Welcome Hero -->
  <div class="welcome-hero mb-4 animate-in">
    <div class="row align-items-center">
      <div class="col-12 col-lg-8">
        <div class="welcome-content">
          <p class="greeting">Selamat Datang</p>
          <h1>Halo, {{ auth()->user()->name ?? 'Admin' }}! <span class="wave-emoji">👋</span></h1>
          <p class="description">
            Kelola tagihan, pelanggan, dan paket internet Anda dengan mudah. Pantau statistik real-time dan akses cepat ke semua fitur penting.
          </p>
          <div class="welcome-time">
            <i class="ri-calendar-line"></i>
            {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4 d-none d-lg-flex justify-content-center">
        <div class="hero-illustration">
          <div class="hero-icon-wrapper">
            <i class="ri-dashboard-3-line"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Stats Section -->
  <div class="section-header animate-in delay-1 d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
      <div class="icon-wrapper">
        <i class="ri-bar-chart-box-line"></i>
      </div>
      <h5>Ringkasan Statistik</h5>
    </div>

    <!-- Filter Periode Modern -->
    <form action="{{ url()->current() }}" method="GET" id="filterForm">
      <input type="hidden" name="periode" id="periodeInput" value="{{ request('periode') }}">
      
      <div id="periodeTrigger" class="d-flex align-items-center gap-2 px-3 py-2 bg-white border rounded-pill shadow-sm cursor-pointer position-relative" style="transition: all 0.2s;">
        <i class="ri-calendar-event-line text-muted"></i>
        <span class="fw-semibold small text-dark" style="font-size: 0.85rem;">
          {{ request('periode') ? \Carbon\Carbon::createFromFormat('Y-m', request('periode'))->translatedFormat('F Y') : 'Filter Bulan' }}
        </span>
        <i class="ri-arrow-down-s-line text-muted ms-1" style="font-size: 1rem;"></i>

        @if(request('periode'))
          <div onclick="resetFilter(event)" class="d-flex align-items-center justify-content-center bg-light rounded-circle text-danger ms-2" style="width: 20px; height: 20px; transition: 0.2s;" title="Hapus Filter">
            <i class="ri-close-line" style="font-size: 14px;"></i>
          </div>
        @endif
      </div>
    </form>
  </div>

  <div class="stats-grid mb-4">
    <div class="stat-card animate-in delay-1">
      <div class="stat-icon">
        <i class="ri-group-line"></i>
      </div>
      <div class="stat-label">Total Customer</div>
      <div class="stat-value">{{ number_format($totalCustomer) }}</div>
    </div>

    <div class="stat-card animate-in delay-2">
      <div class="stat-icon">
        <i class="ri-user-follow-line"></i>
      </div>
      <div class="stat-label">Pelanggan Aktif</div>
      <div class="stat-value">{{ number_format($activeCustomers ?? 0) }}</div>
    </div>

    <div class="stat-card animate-in delay-3">
      <div class="stat-icon">
        <i class="ri-user-unfollow-line"></i>
      </div>
      <div class="stat-label">Pelanggan Tidak Aktif</div>
      <div class="stat-value">{{ number_format($inactiveCustomers ?? 0) }}</div>
    </div>

    <div class="stat-card animate-in delay-3">
      <div class="stat-icon">
        <i class="ri-checkbox-circle-line"></i>
      </div>
      <div class="stat-label">Tagihan Lunas</div>
      <div class="stat-value">{{ number_format($customerLunas ?? 0) }}</div>
    </div>

    <div class="stat-card animate-in delay-4">
      <div class="stat-icon">
        <i class="ri-error-warning-line"></i>
      </div>
      <div class="stat-label">Belum Lunas</div>
      <div class="stat-value">{{ number_format($belumLunas) }}</div>
    </div>

    <div class="stat-card animate-in delay-5">
      <div class="stat-icon">
        <i class="ri-box-3-line"></i>
      </div>
      <div class="stat-label">Total Paket</div>
      <div class="stat-value">{{ number_format($totalPaket) }}</div>
    </div>
  </div>

  <!-- Quick Links Section -->
  <div class="section-header animate-in delay-4">
    <div class="icon-wrapper">
      <i class="ri-flashlight-line"></i>
    </div>
    <h5>Akses Cepat</h5>
  </div>

  <div class="quick-links-grid">
    <a href="{{ route('tagihan.get') }}" class="quick-link animate-in delay-5">
      <span class="arrow-icon"><i class="ri-arrow-right-line"></i></span>
      <div class="quick-link-icon">
        <i class="ri-bill-line"></i>
      </div>
      <h6>Tagihan Belum Bayar</h6>
      <p>Kelola tagihan yang belum dibayar</p>
    </a>

    <a href="{{ route('tagihan.lunas') }}" class="quick-link animate-in delay-5">
      <span class="arrow-icon"><i class="ri-arrow-right-line"></i></span>
      <div class="quick-link-icon">
        <i class="ri-checkbox-circle-line"></i>
      </div>
      <h6>Tagihan Lunas</h6>
      <p>Lihat riwayat pembayaran</p>
    </a>

    <a href="{{ route('pelanggan') }}" class="quick-link animate-in delay-5">
      <span class="arrow-icon"><i class="ri-arrow-right-line"></i></span>
      <div class="quick-link-icon">
        <i class="ri-user-add-line"></i>
      </div>
      <h6>Pelanggan</h6>
      <p>Kelola data pelanggan</p>
    </a>

    <a href="{{ route('paket.index') }}" class="quick-link animate-in delay-5">
      <span class="arrow-icon"><i class="ri-arrow-right-line"></i></span>
      <div class="quick-link-icon">
        <i class="ri-box-3-line"></i>
      </div>
      <h6>Paket Internet</h6>
      <p>Kelola paket layanan</p>
    </a>
  </div>

</div>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/plugins/monthSelect/index.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
  function resetFilter(e) {
      e.stopPropagation(); // Prevent opening calendar
      window.location.href = "{{ url()->current() }}";
  }

  document.addEventListener('DOMContentLoaded', function() {
    flatpickr('#periodeTrigger', {
        plugins: [new monthSelectPlugin({
            shorthand: true,
            dateFormat: "Y-m",
            altFormat: "F Y",
            theme: "light"
        })],
        locale: "id",
        disableMobile: true,
        defaultDate: "{{ request('periode') }}",
        onChange: function(selectedDates, dateStr) {
            if (dateStr) {
                document.getElementById('periodeInput').value = dateStr;
                document.getElementById('filterForm').submit();
            }
        }
    });
  });
</script>
@endsection
