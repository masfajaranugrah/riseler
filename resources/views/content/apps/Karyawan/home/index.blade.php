@php
  $userName = $userName ?? 'Staff';
  $jabatan = $jabatan ?? 'Karyawan';
  $hariIni = $hariIni ?? now()->translatedFormat('l');
  $tanggalHariIni = $tanggalHariIni ?? now()->translatedFormat('j M Y');
  $jamSekarang = now()->format('H:i:s');
  $timeIn = $timeIn ?? '--:--:--';
  $timeOut = $timeOut ?? '--:--:--';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Home Karyawan')

@section('page-style')
  <style>
    :root {
      --primary: #0D6EFD;
      --bg-color: #F8F9FA;
      --text-main: #111827;
      --text-muted: #6B7280;
      --card-bg: #FFFFFF;
      --border-color: #E5E7EB;
    }

    body {
      background-color: #e5e5e5;
      margin: 0;
      font-family: 'Inter', 'Nunito', sans-serif;
    }

    .mobile-wrapper {
      max-width: 480px;
      margin: 0 auto;
      background-color: var(--bg-color);
      min-height: 100vh;
      position: relative;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
      padding: 16px 20px 80px;
      overflow-x: hidden;
    }

    /* Topbar */
    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      padding-top: 10px;
    }

    .company-brand {
      color: var(--primary);
      font-weight: 700;
      font-size: 15px;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .topbar-icons {
      display: flex;
      gap: 16px;
      color: var(--primary);
      font-size: 20px;
    }

    /* Profile */
    .profile-section {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
    }

    .profile-info {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .profile-avatar {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      object-fit: cover;
    }

    .profile-name {
      font-weight: 800;
      color: var(--text-main);
      font-size: 17px;
      margin-bottom: 2px;
    }

    .profile-role {
      color: var(--text-muted);
      font-size: 13px;
    }

    .profile-date {
      text-align: right;
    }

    .date-day {
      font-weight: 700;
      color: var(--text-main);
      font-size: 13px;
      margin-bottom: 2px;
    }

    .date-time {
      color: var(--text-main);
      font-size: 13px;
    }

    /* Clock Card */
    .clock-card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 20px;
      margin-bottom: 24px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }

    .clock-grid {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      position: relative;
    }

    .clock-grid::after {
      content: '';
      position: absolute;
      top: 0;
      bottom: 0;
      left: 50%;
      width: 1px;
      background: var(--border-color);
    }

    .clock-item {
      text-align: center;
      flex: 1;
    }

    .clock-label {
      color: var(--text-muted);
      font-size: 14px;
      margin-bottom: 12px;
    }

    .clock-value {
      font-size: 22px;
      font-weight: 800;
      color: var(--text-main);
    }

    .clock-actions {
      display: flex;
      gap: 12px;
    }

    .clock-actions form {
      flex: 1;
      margin: 0;
    }

    .btn-clock {
      flex: 1;
      background: var(--primary);
      color: white;
      border: none;
      padding: 12px;
      border-radius: 99px;
      font-weight: 600;
      font-size: 14px;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 6px;
      text-decoration: none;
      transition: 0.2s;
      width: 100%;
      cursor: pointer;
    }

    .btn-clock:hover {
      background: #0b5ed7;
      color: white;
    }

    /* Menus */
    .menu-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 16px 8px;
      margin-bottom: 32px;
    }

    .menu-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-decoration: none;
      gap: 8px;
    }

    .menu-icon-wrapper {
      width: 52px;
      height: 52px;
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 24px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    }

    .menu-label {
      font-size: 11px;
      color: var(--text-muted);
      text-align: center;
      line-height: 1.2;
    }

    /* Colors for icons */
    .ic-orange {
      color: #F59E0B;
    }

    .ic-green {
      color: #10B981;
    }

    .ic-pink {
      color: #EC4899;
    }

    .ic-blue {
      color: #3B82F6;
    }



    /* Bottom Nav */
    .bottom-nav {
      position: fixed;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 100%;
      max-width: 480px;
      background: var(--card-bg);
      display: flex;
      justify-content: space-between;
      padding: 12px 24px;
      border-top: 1px solid var(--border-color);
      z-index: 10;
    }

    .nav-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-decoration: none;
      color: #9CA3AF;
      gap: 4px;
    }

    .nav-item.active {
      color: var(--primary);
    }

    .nav-icon {
      font-size: 22px;
    }

    .nav-label {
      font-size: 10px;
      font-weight: 600;
    }
  </style>
@endsection

@section('content')
  <div class="mobile-wrapper">

    {{-- Topbar --}}
    <div class="topbar">
      <div class="company-brand">
        PT. Classik Creactive <i class="ri-arrow-down-s-line" style="font-size: 18px;"></i>
      </div>
      <div class="topbar-icons">
        <i class="ri-search-line"></i>
        <i class="ri-notification-3-line"></i>
      </div>
    </div>

    {{-- Profile Section --}}
    <div class="profile-section">
      <div class="profile-info">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($userName) }}&background=E5E7EB&color=111827&size=100"
          alt="Avatar" class="profile-avatar">
        <div>
          <div class="profile-name">{{ $userName }}</div>
          <div class="profile-role">{{ $jabatan }}</div>
        </div>
      </div>
      <div class="profile-date">
        <div class="date-day">{{ $hariIni }}, {{ $tanggalHariIni }}</div>
        <div class="date-time" id="realtime-clock">{{ $jamSekarang }} WIB</div>
      </div>
    </div>

    {{-- Clock Card --}}
    <div class="clock-card">
      <div class="clock-grid">
        <div class="clock-item">
          <div class="clock-label">Absen Masuk</div>
          <div class="clock-value">{{ $timeIn }}</div>
        </div>
        <div class="clock-item">
          <div class="clock-label">Absen Keluar</div>
          <div class="clock-value">{{ $timeOut }}</div>
        </div>
      </div>
      <div class="clock-actions">
        <a href="{{ route('absensi.capture', ['action' => 'checkin']) }}" class="btn-clock"
          style="text-decoration: none;">
          <i class="ri-login-box-line" style="font-size: 18px;"></i> Clock In
        </a>
        <a href="{{ route('absensi.capture', ['action' => 'checkout']) }}" class="btn-clock"
          style="text-decoration: none;">
          <i class="ri-logout-box-line" style="font-size: 18px;"></i> Clock Out
        </a>
      </div>
    </div>

    {{-- Menu Grid --}}
    <div class="menu-grid">
      <a href="{{ route('absensi.index') }}" class="menu-item">
        <div class="menu-icon-wrapper"><i class="ri-time-line ic-orange"></i></div>
        <span class="menu-label">Absensi</span>
      </a>
      <a href="{{ url('/dashboard/karyawan/jobs') }}" class="menu-item">
        <div class="menu-icon-wrapper"><i class="ri-briefcase-4-fill ic-blue"></i></div>
        <span class="menu-label">Jobs</span>
      </a>
      <a href="#" class="menu-item">
        <div class="menu-icon-wrapper"><i class="ri-file-text-line ic-green"></i></div>
        <span class="menu-label">Slip Gaji</span>
      </a>
      <a href="{{ route('absensi.capture', ['action' => 'lembur_in']) }}" class="menu-item">
        <div class="menu-icon-wrapper"><i class="ri-moon-line ic-pink"></i></div>
        <span class="menu-label">Lembur</span>
      </a>
    </div>



    {{-- Bottom Navigation --}}
    <nav class="bottom-nav">
      <a href="{{ route('karyawan.home') }}" class="nav-item active">
        <i class="ri-home-5-fill nav-icon"></i>
        <span class="nav-label">Beranda</span>
      </a>
      <a href="#" class="nav-item">
        <i class="ri-checkbox-circle-line nav-icon"></i>
        <span class="nav-label">Aktivitas</span>
      </a>
      <a href="{{ url('/dashboard/karyawan/jobs') }}" class="nav-item">
        <i class="ri-message-3-line nav-icon"></i>
        <span class="nav-label">Tugas</span>
      </a>
      <a href="#" class="nav-item">
        <i class="ri-book-read-line nav-icon"></i>
        <span class="nav-label">Pelatihan</span>
      </a>
      <a href="#" class="nav-item">
        <i class="ri-user-smile-line nav-icon"></i>
        <span class="nav-label">Profil</span>
      </a>
    </nav>

  </div>

  <script>
    function updateClock() {
      const now = new Date();
      const hours = String(now.getHours()).padStart(2, '0');
      const minutes = String(now.getMinutes()).padStart(2, '0');
      const seconds = String(now.getSeconds()).padStart(2, '0');
      document.getElementById('realtime-clock').innerText = `${hours}:${minutes}:${seconds} WIB`;
    }
    
    // Update every second
    setInterval(updateClock, 1000);
  </script>
@endsection