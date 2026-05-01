@php
    $user = auth('customer')->user();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Home - Dashboard</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<!-- Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: #f8fafc;
    font-family: 'Inter', sans-serif;
    padding: 0 0 90px 0;
    min-height: 100vh;
    color: #0f172a;
}

.container {
    max-width: 680px;
    padding: 0 16px;
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    padding: 32px 20px 40px;
    margin: 0 -16px;
    color: white;
}

.hero-greeting {
    font-size: 0.875rem;
    color: #94a3b8;
    margin-bottom: 4px;
}

.hero-name {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 8px;
    letter-spacing: -0.02em;
}

.hero-subtitle {
    font-size: 0.9375rem;
    color: #cbd5e1;
}

/* Stats Cards */
.stats-section {
    margin: -24px 0 24px;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    transition: all 0.2s ease;
}

.stat-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    border-color: #cbd5e1;
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    font-size: 1.25rem;
}

.stat-icon.primary {
    background: #f0f9ff;
    color: #0369a1;
}

.stat-icon.success {
    background: #f0fdf4;
    color: #15803d;
}

.stat-icon.warning {
    background: #fef3c7;
    color: #d97706;
}

.stat-icon.danger {
    background: #fef2f2;
    color: #dc2626;
}

.stat-label {
    font-size: 0.8125rem;
    color: #64748b;
    margin-bottom: 4px;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.02em;
}

/* Quick Actions */
.section-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 16px;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 32px;
}

.action-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    text-decoration: none;
    color: inherit;
}

.action-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    border-color: #cbd5e1;
    transform: translateY(-2px);
}

.action-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    font-size: 1.75rem;
}

.action-icon.primary {
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    color: white;
}

.action-icon.success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.action-icon.warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.action-icon.purple {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
}

.action-title {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 4px;
}

.action-subtitle {
    font-size: 0.75rem;
    color: #64748b;
}

/* Bottom Navigation */
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 72px;
    background: #ffffff;
    display: flex;
    justify-content: space-around;
    align-items: center;
    box-shadow: 0 -2px 16px rgba(0,0,0,0.08);
    border-top: 1px solid #e2e8f0;
    z-index: 999;
}

.bottom-nav .tab-btn {
    background: none;
    border: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    color: #94a3b8;
    position: relative;
    transition: all 0.2s ease;
    cursor: pointer;
    padding: 8px 16px;
    border-radius: 12px;
}

.bottom-nav .tab-btn:hover {
    background: #f8fafc;
}

.bottom-nav .tab-btn i {
    font-size: 1.5rem;
}

.bottom-nav .tab-btn span {
    font-size: 0.6875rem;
    font-weight: 600;
    letter-spacing: -0.01em;
}

.bottom-nav .tab-btn.active {
    color: #0f172a;
}

.bottom-nav .tab-btn.active::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 32px;
    height: 3px;
    background: #0f172a;
    border-radius: 0 0 3px 3px;
}

/* Profile Overlay */
.profile-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    display: none;
    align-items: flex-end;
    z-index: 1000;
    animation: fadeIn 0.2s ease;
}

.profile-overlay.show {
    display: flex;
}

.profile-modal {
    background: #ffffff;
    border-radius: 24px 24px 0 0;
    width: 100%;
    max-width: 680px;
    margin: 0 auto;
    padding: 24px;
    animation: slideUp 0.3s ease;
    box-shadow: 0 -4px 24px rgba(0,0,0,0.12);
}

.profile-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
}

.profile-avatar {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.profile-avatar i {
    font-size: 2.5rem;
    color: #64748b;
}

.profile-info h5 {
    margin: 0;
    color: #0f172a;
    font-size: 1.125rem;
    font-weight: 700;
    letter-spacing: -0.01em;
}

.profile-info p {
    margin: 4px 0 0 0;
    color: #64748b;
    font-size: 0.875rem;
}

.profile-divider {
    height: 1px;
    background: #f1f5f9;
    margin: 20px 0;
}

.logout-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 16px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 12px;
    color: #dc2626;
    font-size: 0.9375rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-top: 20px;
}

.logout-btn:hover {
    background: #fee2e2;
    border-color: #fca5a5;
}

.logout-btn i {
    font-size: 1.125rem;
}

/* ========== CUSTOM SWEETALERT2 MODAL STYLING - BLACK & WHITE MODERN ========== */
.swal2-popup.custom-modal {
    border-radius: 24px !important;
    padding: 0 !important;
    width: 90% !important;
    max-width: 420px !important;
    box-shadow: 0 25px 80px rgba(0,0,0,0.5) !important;
    font-family: 'Inter', sans-serif !important;
    overflow: hidden !important;
    border: 1px solid #2d2d2d !important;
}

.swal2-popup.custom-modal .swal2-html-container {
    margin: 0 !important;
    padding: 0 !important;
}

.modal-content-wrapper {
    position: relative;
    background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 50%, #0a0a0a 100%);
    padding: 48px 28px 32px;
    color: white;
    overflow: hidden;
}

/* Minimalist Background Accents */
.bg-circle {
    position: absolute;
    border-radius: 50%;
    opacity: 0.03;
    background: white;
}

.bg-circle-1 {
    width: 300px;
    height: 300px;
    top: -150px;
    right: -150px;
    border: 2px solid rgba(255,255,255,0.1);
}

.bg-circle-2 {
    width: 200px;
    height: 200px;
    bottom: -100px;
    left: -100px;
    border: 1px solid rgba(255,255,255,0.08);
}

.bg-circle-3 {
    width: 120px;
    height: 120px;
    top: 40%;
    left: -60px;
    border: 1px solid rgba(255,255,255,0.05);
}

/* Clean Icon Container */
.icon-container {
    position: relative;
    width: 88px;
    height: 88px;
    margin: 0 auto 28px;
    z-index: 2;
}

.icon-bg {
    position: absolute;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.08);
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.15);
    animation: pulseClean 3s ease-in-out infinite;
}

.icon-main {
    position: absolute;
    width: 72px;
    height: 72px;
    top: 8px;
    left: 8px;
    background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    box-shadow: 0 4px 20px rgba(255,255,255,0.2);
    animation: bounceClean 1.2s ease-in-out;
}

.icon-main i {
    color: #0f0f0f !important;
}

@keyframes pulseClean {
    0%, 100% {
        transform: scale(1);
        opacity: 0.15;
    }
    50% {
        transform: scale(1.08);
        opacity: 0.08;
    }
}

@keyframes bounceClean {
    0%, 100% { transform: translateY(0) scale(1); }
    30% { transform: translateY(-8px) scale(1.02); }
    60% { transform: translateY(-4px) scale(0.98); }
}

/* Modern Typography */
.modal-title {
    font-size: 1.875rem;
    font-weight: 800;
    margin-bottom: 12px;
    text-align: center;
    position: relative;
    z-index: 2;
    letter-spacing: -0.03em;
    color: #ffffff;
    text-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

.modal-subtitle {
    font-size: 1.0625rem;
    font-weight: 400;
    text-align: center;
    color: #c4c4c4;
    position: relative;
    z-index: 2;
    line-height: 1.6;
    letter-spacing: -0.01em;
}

/* Clean Feature Cards */
.feature-cards {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-top: 28px;
    position: relative;
    z-index: 2;
}

.feature-card {
    background: rgba(255,255,255,0.06);
    backdrop-filter: blur(10px);
    border-radius: 14px;
    padding: 20px 16px;
    text-align: center;
    border: 1px solid rgba(255,255,255,0.12);
    transition: all 0.3s ease;
}

.feature-card:hover {
    background: rgba(255,255,255,0.1);
    border-color: rgba(255,255,255,0.2);
    transform: translateY(-2px);
}

.feature-icon {
    font-size: 2rem;
    margin-bottom: 10px;
    filter: grayscale(0);
}

.feature-text {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #e8e8e8;
    letter-spacing: -0.01em;
}

/* Minimalist Badge */
.notification-badge {
    display: inline-block;
    background: rgba(255,255,255,0.95);
    color: #0f0f0f;
    font-size: 0.8125rem;
    font-weight: 700;
    padding: 8px 18px;
    border-radius: 24px;
    margin-top: 8px;
    letter-spacing: -0.01em;
    box-shadow: 0 4px 16px rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.2);
}

/* Modern Button Styling */
.swal2-confirm.custom-btn {
    background: linear-gradient(135deg, #ffffff 0%, #f5f5f5 100%) !important;
    color: #0f0f0f !important;
    font-weight: 700 !important;
    font-size: 1.0625rem !important;
    padding: 16px 36px !important;
    border-radius: 14px !important;
    border: 1px solid rgba(255,255,255,0.3) !important;
    box-shadow: 0 6px 24px rgba(255,255,255,0.2) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    margin-top: 28px !important;
    letter-spacing: -0.01em !important;
}

.swal2-confirm.custom-btn:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 10px 32px rgba(255,255,255,0.3) !important;
    background: linear-gradient(135deg, #ffffff 0%, #efefef 100%) !important;
}

.swal2-confirm.custom-btn:active {
    transform: translateY(-1px) !important;
}

.swal2-cancel.custom-cancel-btn {
    background: rgba(255,255,255,0.08) !important;
    color: #e8e8e8 !important;
    font-weight: 600 !important;
    font-size: 1rem !important;
    padding: 16px 32px !important;
    border-radius: 14px !important;
    border: 1px solid rgba(255,255,255,0.2) !important;
    box-shadow: none !important;
    transition: all 0.3s ease !important;
    margin-top: 28px !important;
    margin-right: 12px !important;
    letter-spacing: -0.01em !important;
}

.swal2-cancel.custom-cancel-btn:hover {
    background: rgba(255,255,255,0.15) !important;
    border-color: rgba(255,255,255,0.3) !important;
    transform: translateY(-2px) !important;
}

.swal2-close {
    color: rgba(255,255,255,0.7) !important;
    font-size: 2rem !important;
    opacity: 1 !important;
    transition: all 0.3s ease !important;
    width: 36px !important;
    height: 36px !important;
    background: rgba(255,255,255,0.08) !important;
    border-radius: 50% !important;
}

.swal2-close:hover {
    color: #ffffff !important;
    background: rgba(255,255,255,0.15) !important;
    transform: rotate(90deg) scale(1.1) !important;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { transform: translateY(100%); }
    to { transform: translateY(0); }
}

body.modal-open {
    overflow: hidden;
}

/* Responsive */
@media (max-width: 576px) {
    .hero-name {
        font-size: 1.5rem;
    }

    .stats-section {
        gap: 10px;
    }

    .stat-card {
        padding: 16px;
    }

    .quick-actions {
        gap: 10px;
    }
}
</style>
</head>

<body>
<div class="container">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-greeting">Selamat Datang,</div>
        <div class="hero-name">{{ $user->nama_lengkap ?? 'Pelanggan' }}</div>
        <div class="hero-subtitle">Kelola tagihan Anda dengan mudah</div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-section">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="bi bi-receipt"></i>
            </div>
            <div class="stat-label">Total Tagihan</div>
            <div class="stat-value">{{ $totalTagihan ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon success">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-label">Lunas</div>
            <div class="stat-value">{{ $tagihanLunas ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="bi bi-clock"></i>
            </div>
            <div class="stat-label">Menunggu</div>
            <div class="stat-value">{{ $tagihanMenunggu ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="bi bi-exclamation-circle"></i>
            </div>
            <div class="stat-label">Belum Bayar</div>
            <div class="stat-value">{{ $tagihanBelum ?? 0 }}</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="section-title">Menu Cepat</div>
    <div class="quick-actions">
        <a href="/dashboard/customer/tagihan" class="action-card">
            <div class="action-icon primary">
                <i class="bi bi-receipt"></i>
            </div>
            <div class="action-title">Tagihan</div>
            <div class="action-subtitle">Lihat tagihan aktif</div>
        </a>

        <a href="/dashboard/customer/tagihan/selesai" class="action-card">
            <div class="action-icon success">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <div class="action-title">Kwitansi</div>
            <div class="action-subtitle">Riwayat pembayaran</div>
        </a>

        <a href="https://direct.lc.chat/19403578" class="action-card">
            <div class="action-icon warning">
                <i class="bi bi-chat-dots"></i>
            </div>
            <div class="action-title">Chat CS</div>
            <div class="action-subtitle">Hubungi kami</div>
        </a>
    </div>
</div>

<!-- Bottom Navigation -->
<div class="bottom-nav">
    <button class="tab-btn active" onclick="window.location.href='/dashboard/customer/tagihan/home'">
        <i class="bi bi-house-door-fill"></i>
        <span>Home</span>
    </button>

    <button class="tab-btn" onclick="window.location.href='/dashboard/customer/tagihan'">
        <i class="bi bi-receipt"></i>
        <span>Tagihan</span>
    </button>

    <button class="tab-btn" onclick="window.location.href='/dashboard/customer/tagihan/selesai'">
        <i class="bi bi-file-earmark-text"></i>
        <span>Kwitansi</span>
    </button>

    <button class="tab-btn" onclick="window.location.href='/dashboard/customer/chat'">
        <i class="bi bi-chat-dots"></i>
        <span>Chat</span>
    </button>

    <button id="btn-profile" class="tab-btn">
        <i class="bi bi-person-circle"></i>
        <span>Profile</span>
    </button>
</div>

<!-- Profile Modal -->
<div id="profile-overlay" class="profile-overlay">
    <div class="profile-modal">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="bi bi-person-circle"></i>
            </div>
            <div class="profile-info">
                <h5>{{ $user->nama_lengkap ?? 'Nama Pelanggan' }}</h5>
                <p>{{ $user->whatsapp }}</p>
            </div>
        </div>

        <div class="profile-divider"></div>

        <button id="btn-logout" class="logout-btn">
            <i class="bi bi-box-arrow-right"></i>
            <span>Keluar</span>
        </button>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
// Profile Modal Toggle
const btnProfile = document.getElementById('btn-profile');
const overlay = document.getElementById('profile-overlay');

btnProfile.addEventListener('click', (e) => {
    e.stopPropagation();
    overlay.classList.toggle('show');
    document.body.classList.toggle('modal-open');
});

overlay.addEventListener('click', (e) => {
    if (e.target === overlay) {
        overlay.classList.remove('show');
        document.body.classList.remove('modal-open');
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && overlay.classList.contains('show')) {
        overlay.classList.remove('show');
        document.body.classList.remove('modal-open');
    }
});

// Logout
document.getElementById('btn-logout').addEventListener('click', () => {
    Swal.fire({
        title: 'Keluar dari Akun?',
        text: 'Anda akan keluar dari aplikasi',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#94a3b8',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Keluar...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/customer/logout', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (response.ok) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Anda telah keluar dari akun',
                        icon: 'success',
                        confirmButtonColor: '#0f172a',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '/';
                    });
                } else {
                    throw new Error('Logout failed');
                }
            })
            .catch(() => {
                Swal.fire({
                    title: 'Error',
                    text: 'Gagal keluar dari akun',
                    icon: 'error',
                    confirmButtonColor: '#dc2626'
                });
            });
        }
    });
});
</script>

<!-- WebPushr SDK -->
<script>
(function(w,d,s,id){
    if(typeof w.webpushr!=='undefined') return;
    w.webpushr=w.webpushr||function(){(w.webpushr.q=w.webpushr.q||[]).push(arguments)};
    var js,fjs=d.getElementsByTagName(s)[0];
    js=d.createElement(s); js.id=id; js.async=1;
    js.src="https://cdn.webpushr.com/app.min.js";
    fjs.parentNode.insertBefore(js,fjs);
}(window,document,'script','webpushr-js'));

webpushr('setup',{
    'key':'BA6E203ONU9JRrWFSTUFepnOgRg7JZ0hZKGtfZ_nT_WWOzRCvjlF9BJT8hvmA_Rvbl_W4NbpYiy7SDwoQKK6g2M'
});

// ========== WEBPUSHR READY CALLBACK ==========
window._webpushrScriptReady = function() {
    console.log('?? WebPushr SDK is ready!');
    checkNotificationStatus();
};

// ========== NOTIFICATION MANAGEMENT ==========
const nomerid = "{{ $user->nomer_id }}";
const DEVICE_ID_KEY = 'device_notification_id';
const LAST_SID_KEY = 'last_subscriber_id';

function getDeviceId() {
    let deviceId = localStorage.getItem(DEVICE_ID_KEY);
    if (!deviceId) {
        deviceId = 'device_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem(DEVICE_ID_KEY, deviceId);
        console.log('?? Device ID baru dibuat:', deviceId);
    }
    return deviceId;
}

function updateSubscriberId(forceUpdate = false) {
    const deviceId = getDeviceId();
    let retryCount = 0;
    const maxRetries = 5;

    function attemptFetchSID() {
        webpushr('fetch_id', function(sid) {
            if (sid) {
                const lastSID = localStorage.getItem(LAST_SID_KEY);
                if (forceUpdate || sid !== lastSID) {
                    console.log('?? Updating SID to server...');
                    $.ajax({
                        url: '/pelanggan/' + nomerid + '/update-sid',
                        method: 'POST',
                        data: {
                            sid: sid,
                            device_id: deviceId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log('? SID berhasil diupdate!');
                            localStorage.setItem(LAST_SID_KEY, sid);
                            showSuccessToast();
                        },
                        error: function(xhr, status, error) {
                            console.error('? Gagal update SID:', error);
                            if (retryCount < 3) {
                                retryCount++;
                                setTimeout(() => updateSubscriberId(forceUpdate), 2000);
                            }
                        }
                    });
                }
            } else {
                if (retryCount < maxRetries) {
                    retryCount++;
                    setTimeout(attemptFetchSID, 1000);
                }
            }
        });
    }
    attemptFetchSID();
}

function checkNotificationStatus() {
    if (!('Notification' in window)) return;
    const permission = Notification.permission;
    const deviceId = getDeviceId();
    const hasAskedBefore = localStorage.getItem('notification_asked_' + deviceId);

    if (permission === 'granted') {
        const forceUpdate = !hasAskedBefore;
        setTimeout(() => updateSubscriberId(forceUpdate), 1500);
    } else if (permission === 'default' && !hasAskedBefore) {
        setTimeout(() => showCustomPermissionPopup(), 3000);
    }
}

function showCustomPermissionPopup() {
    const deviceId = getDeviceId();
    Swal.fire({
        html: `
            <div class="modal-content-wrapper">
                <div class="bg-circle bg-circle-1"></div>
                <div class="bg-circle bg-circle-2"></div>
                <div class="bg-circle bg-circle-3"></div>
                <div class="icon-container">
                    <div class="icon-bg"></div>
                    <div class="icon-main">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                </div>
                <div class="modal-title">Aktifkan Notifikasi?</div>
                <div class="modal-subtitle">
                    Dapatkan pemberitahuan instan untuk tagihan baru dan update pembayaran Anda
                </div>
                <div style="text-align: center; margin-top: 16px;">
                    <small style="opacity: 0.8; font-size: 0.85rem;">
                        <i class="bi bi-shield-check"></i> Device baru terdeteksi
                    </small>
                </div>
                <div class="feature-cards">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <div class="feature-text">Update Realtime</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-alarm-fill"></i>
                        </div>
                        <div class="feature-text">Pengingat Tagihan</div>
                    </div>
                </div>
            </div>
        `,
        confirmButtonText: '<i class="bi bi-check-circle-fill" style="margin-right: 6px;"></i> Ya, Aktifkan',
        showCancelButton: true,
        cancelButtonText: '<i class="bi bi-x-circle" style="margin-right: 6px;"></i> Nanti Saja',
        customClass: {
            popup: 'custom-modal',
            confirmButton: 'custom-btn',
            cancelButton: 'custom-cancel-btn'
        },
        showClass: {
            popup: 'animate__animated animate__zoomIn animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__zoomOut animate__faster'
        },
        allowOutsideClick: false,
        showCloseButton: true,
        backdrop: 'rgba(0,0,0,0.6)'
    }).then((result) => {
        localStorage.setItem('notification_asked_' + deviceId, 'true');
        if (result.isConfirmed) {
            requestBrowserPermission();
        }
    });
}

function requestBrowserPermission() {
    Swal.fire({
        title: 'Mohon Tunggu',
        text: 'Memproses permintaan notifikasi...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => Swal.showLoading()
    });

    Notification.requestPermission().then(function(permission) {
        if (permission === 'granted') {
            Swal.close();
            Swal.fire({
                title: 'Mengaktifkan Notifikasi',
                text: 'Menyimpan pengaturan...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading()
            });
            setTimeout(() => updateSubscriberId(true), 2000);
        } else if (permission === 'denied') {
            Swal.fire({
                icon: 'warning',
                title: 'Notifikasi Diblokir',
                html: `
                    Untuk mengaktifkan notifikasi:<br><br>
                    <strong>1.</strong> Klik ikon <i class="bi bi-lock-fill"></i> di address bar<br>
                    <strong>2.</strong> Ubah Notifikasi ke "Izinkan"<br>
                    <strong>3.</strong> Refresh halaman ini
                `,
                confirmButtonText: 'Mengerti'
            });
        }
    });
}

function showSuccessToast() {
    Swal.fire({
        icon: 'success',
        title: 'Notifikasi Aktif!',
        text: 'Anda akan menerima pemberitahuan untuk tagihan baru',
        timer: 3000,
        showConfirmButton: false,
        position: 'top-end',
        toast: true
    });
}

setInterval(function() {
    const currentPermission = Notification.permission;
    const lastPermission = localStorage.getItem('last_permission_status');
    if (lastPermission && lastPermission !== currentPermission) {
        if (currentPermission === 'granted') {
            updateSubscriberId(true);
        }
    }
    localStorage.setItem('last_permission_status', currentPermission);
}, 5000);

function getGreeting() {
    const now = new Date();
    const hour = now.getHours();
    if (hour >= 0 && hour < 11) {
        return 'Selamat Pagi! <i class="bi bi-brightness-high-fill"></i>';
    } else if (hour >= 11 && hour < 15) {
        return 'Selamat Siang! <i class="bi bi-cloud-sun-fill"></i>';
    } else if (hour >= 15 && hour < 18) {
        return 'Selamat Sore! <i class="bi bi-sunset-fill"></i>';
    } else {
        return 'Selamat Malam! <i class="bi bi-moon-stars-fill"></i>';
    }
}

// ========== POLLING UNTUK TAGIHAN BARU ==========
const userNomerId = "{{ $user->nomer_id }}";
let isModalShown = false;

function checkForNewNotifications() {
    if (isModalShown) return;
    $.get('/api/check-pending-notifications/' + userNomerId)
    .done(function(response){
        if(response.has_notification && !isModalShown){
            isModalShown = true;
            Swal.fire({
                html: `
                    <div class="modal-content-wrapper">
                        <div class="bg-circle bg-circle-1"></div>
                        <div class="bg-circle bg-circle-2"></div>
                        <div class="bg-circle bg-circle-3"></div>
                        <div class="icon-container">
                            <div class="icon-bg"></div>
                            <div class="icon-main">
                                <i class="bi bi-wallet2"></i>
                            </div>
                        </div>
                        <div class="modal-title">${getGreeting()}</div>
                        <div class="modal-subtitle">Ada tagihan yang menunggu nih!</div>
                        <div style="text-align: center;">
                            <span class="notification-badge">
                                <i class="bi bi-lightning-charge-fill"></i> Perlu Perhatian
                            </span>
                        </div>
                        <div class="feature-cards">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="bi bi-lightning-charge-fill"></i>
                                </div>
                                <div class="feature-text">Bayar Cepat</div>
                            </div>
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <div class="feature-text">Mudah & Aman</div>
                            </div>
                        </div>
                    </div>
                `,
                confirmButtonText: '<i class="bi bi-arrow-right-circle-fill" style="margin-right: 6px;"></i> Lihat Tagihan Sekarang',
                showCancelButton: true,
                cancelButtonText: '<i class="bi bi-x-circle" style="margin-right: 6px;"></i> Nanti Saja',
                customClass: {
                    popup: 'custom-modal',
                    confirmButton: 'custom-btn',
                    cancelButton: 'custom-cancel-btn'
                },
                showClass: {
                    popup: 'animate__animated animate__zoomIn animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__zoomOut animate__faster'
                },
                allowOutsideClick: false,
                showCloseButton: true,
                backdrop: 'rgba(0,0,0,0.6)'
            }).then(result=>{
                if(result.isConfirmed) {
                    window.location.href='/dashboard/customer/tagihan';
                }
                setTimeout(()=> isModalShown=false, 5000);
            });
        }
    })
    .fail(function(xhr){
        console.error('? Error polling:', xhr.responseText);
    });
}

// ========== POLLING UNTUK INFO/IKLAN BROADCAST ==========
let isInfoModalShown = false;
const INFO_SHOWN_KEY = 'last_info_shown_';

function checkForBroadcastInfo() {
    if (isInfoModalShown) return;
    $.get('/api/check-broadcast-info/' + userNomerId)
    .done(function(response){
        if(response.has_info && response.info && !isInfoModalShown){
            const infoId = response.info.id || 'general';
            const lastShownInfo = localStorage.getItem(INFO_SHOWN_KEY + userNomerId);
            
            // Cek apakah info ini sudah pernah ditampilkan
            if (lastShownInfo === String(infoId)) {
                console.log('?? Info sudah pernah ditampilkan, skip...');
                return;
            }
            
            isInfoModalShown = true;
            
            // Generate feature cards HTML
            let featureCardsHTML = '';
            if (response.info.features && response.info.features.length > 0) {
                featureCardsHTML = '<div class="feature-cards">';
                response.info.features.forEach(feature => {
                    featureCardsHTML += `
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi ${feature.icon || 'bi-star-fill'}"></i>
                            </div>
                            <div class="feature-text">${feature.text}</div>
                        </div>
                    `;
                });
                featureCardsHTML += '</div>';
            }
            
            Swal.fire({
                html: `
                    <div class="modal-content-wrapper">
                        <div class="bg-circle bg-circle-1"></div>
                        <div class="bg-circle bg-circle-2"></div>
                        <div class="bg-circle bg-circle-3"></div>
                        <div class="icon-container">
                            <div class="icon-bg"></div>
                            <div class="icon-main">
                                <i class="bi bi-megaphone-fill"></i>
                            </div>
                        </div>
                        <div class="modal-title">${response.info.title || 'Informasi Penting'}</div>
                        <div class="modal-subtitle">${response.info.message || 'Ada informasi terbaru untuk Anda'}</div>
                        <div style="text-align: center; margin-top: 20px;">
                            <span class="notification-badge">
                                <i class="bi bi-stars"></i> Info Terbaru
                            </span>
                        </div>
                        ${featureCardsHTML}
                    </div>
                `,
                confirmButtonText: '<i class="bi bi-check-circle-fill" style="margin-right: 6px;"></i> Saya Mengerti',
                showCancelButton: false,
                customClass: {
                    popup: 'custom-modal',
                    confirmButton: 'custom-btn'
                },
                showClass: {
                    popup: 'animate__animated animate__zoomIn animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__zoomOut animate__faster'
                },
                allowOutsideClick: false,
                showCloseButton: true,
                backdrop: 'rgba(0,0,0,0.6)'
            }).then(result => {
                // Simpan bahwa info ini sudah ditampilkan
                localStorage.setItem(INFO_SHOWN_KEY + userNomerId, String(infoId));
                
                // Jika ada action URL, redirect
                if (result.isConfirmed && response.info.action_url) {
                    window.location.href = response.info.action_url;
                }
                
                setTimeout(() => isInfoModalShown = false, 3000);
            });
        }
    })
    .fail(function(xhr){
        console.error('? Error checking broadcast info:', xhr.responseText);
    });
}

// ========== INISIALISASI ==========
$(document).ready(function(){
    console.log('?? Aplikasi dimulai');
    console.log('?? User Nomer ID:', nomerid);
    
    if (typeof webpushr !== 'undefined') {
        console.log('? Menunggu WebPushr SDK ready...');
    }
    
    // Polling untuk tagihan baru (setiap 30 detik)
    setTimeout(checkForNewNotifications, 2000);
    setInterval(checkForNewNotifications, 30000);
    
    // Polling untuk broadcast info/iklan (setiap 60 detik)
    setTimeout(checkForBroadcastInfo, 7000); // Delay 7 detik agar tidak bersamaan
    setInterval(checkForBroadcastInfo, 60000);
});
</script>

</body>
</html>
