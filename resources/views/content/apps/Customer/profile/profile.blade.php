@php
    $user = auth('customer')->user();
    $paket = $user->paket ?? null;
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profile - {{ $user->nama_lengkap ?? 'Pelanggan' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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

        /* Hero */
        .hero-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 40px 20px 80px;
            margin: 0 -16px;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            top: -100px;
            right: -80px;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .profile-avatar-wrap {
            position: relative;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            margin: 0 auto 16px;
        }

        .profile-avatar-inner {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffffff 0%, #e0f2fe 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
        }

        .hero-name {
            font-size: 1.375rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 4px;
            position: relative;
            z-index: 2;
        }

        .hero-sub {
            font-size: 0.875rem;
            color: #94a3b8;
            position: relative;
            z-index: 2;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 14px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 10px;
            position: relative;
            z-index: 2;
        }

        .status-pill.aktif {
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .status-pill.nonaktif {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* Card Container */
        .profile-card {
            background: white;
            border-radius: 20px 20px 0 0;
            margin-top: -36px;
            padding: 24px 0 8px;
            position: relative;
            z-index: 3;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
        }

        /* Paket card */
        .paket-card {
            margin: 0 20px 20px;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
            border-radius: 16px;
            padding: 20px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .paket-card::after {
            content: '';
            position: absolute;
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 50%;
            top: -40px;
            right: -30px;
        }

        .paket-label {
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            margin-bottom: 6px;
        }

        .paket-name {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .paket-speed {
            font-size: 0.8125rem;
            color: #94a3b8;
            margin-bottom: 14px;
        }

        .paket-price {
            font-size: 1.125rem;
            font-weight: 700;
            color: #38bdf8;
        }

        .paket-price span {
            font-size: 0.75rem;
            font-weight: 500;
            color: #94a3b8;
        }

        /* Section title */
        .section-title {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 0 20px;
            margin-bottom: 12px;
        }

        /* Info items */
        .info-list {
            border-top: 1px solid #f1f5f9;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .info-icon.blue {
            background: #eff6ff;
            color: #3b82f6;
        }

        .info-icon.green {
            background: #f0fdf4;
            color: #22c55e;
        }

        .info-icon.amber {
            background: #fffbeb;
            color: #f59e0b;
        }

        .info-icon.slate {
            background: #f1f5f9;
            color: #64748b;
        }

        .info-icon.rose {
            background: #fff1f2;
            color: #f43f5e;
        }

        .info-meta {
            flex: 1;
            min-width: 0;
        }

        .info-label {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 500;
            margin-bottom: 1px;
        }

        .info-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Menu items */
        .menu-section {
            padding: 16px 20px 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.15s ease;
            margin-bottom: 8px;
            text-decoration: none;
            color: #0f172a;
        }

        .menu-item:hover {
            background: #f1f5f9;
            border-color: #e2e8f0;
            color: #0f172a;
        }

        .menu-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .menu-icon.blue {
            background: #eff6ff;
            color: #3b82f6;
        }

        .menu-icon.amber {
            background: #fffbeb;
            color: #f59e0b;
        }

        .menu-icon.green {
            background: #f0fdf4;
            color: #22c55e;
        }

        .menu-text {
            flex: 1;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .menu-arrow {
            color: #cbd5e1;
            font-size: 0.875rem;
        }

        /* Logout */
        .logout-section {
            padding: 20px 20px 0;
        }

        .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px 20px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 14px;
            color: #dc2626;
            font-size: 0.9375rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }

        .logout-btn:hover {
            background: #fee2e2;
            border-color: #fca5a5;
        }

        .app-version {
            text-align: center;
            padding: 16px 20px;
            color: #cbd5e1;
            font-size: 0.75rem;
        }

        /* Bottom nav */
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
            box-shadow: 0 -2px 16px rgba(0, 0, 0, 0.08);
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
    </style>
</head>

<body>
    <div class="container">

        <!-- Hero Section -->
        <div class="hero-section">
            <div class="profile-avatar-wrap">
                <div class="profile-avatar-inner">
                    {{ strtoupper(substr($user->nama_lengkap ?? 'P', 0, 1)) }}
                </div>
            </div>
            <div class="hero-name">{{ $user->nama_lengkap ?? 'Pelanggan' }}</div>
            <div class="hero-sub">ID: {{ $user->nomer_id ?? '-' }}</div>

            @if(in_array($user->status, ['active', 'aktif', 'approve']))
                <span class="status-pill aktif">
                    <i class="bi bi-check-circle-fill"></i> {{ ucfirst($user->status) }}
                </span>
            @else
                <span class="status-pill nonaktif">
                    <i class="bi bi-x-circle-fill"></i> {{ ucfirst($user->status ?? 'Tidak diketahui') }}
                </span>
            @endif
        </div>

        <!-- Profile Card -->
        <div class="profile-card">



            <!-- Info Akun -->
            <div class="section-title">Informasi Akun</div>
            <div class="info-list">
                <div class="info-item">
                    <div class="info-icon blue"><i class="bi bi-person-fill"></i></div>
                    <div class="info-meta">
                        <div class="info-label">Nama Lengkap</div>
                        <div class="info-value">{{ $user->nama_lengkap ?? '-' }}</div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon green"><i class="bi bi-fingerprint"></i></div>
                    <div class="info-meta">
                        <div class="info-label">Nomer ID</div>
                        <div class="info-value">{{ $user->nomer_id ?? '-' }}</div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon amber"><i class="bi bi-whatsapp"></i></div>
                    <div class="info-meta">
                        <div class="info-label">Nomor WhatsApp</div>
                        <div class="info-value">{{ $user->no_whatsapp ?? '-' }}</div>
                    </div>
                </div>

                <!-- Menu -->
                <div class="menu-section">
                    <a href="/dashboard/customer/riwayat" class="menu-item">
                        <div class="menu-icon blue"><i class="bi bi-clock-history"></i></div>
                        <span class="menu-text">Riwayat Pembayaran</span>
                        <i class="bi bi-chevron-right menu-arrow"></i>
                    </a>
                    <a href="/dashboard/customer/faq" class="menu-item">
                        <div class="menu-icon amber"><i class="bi bi-question-circle"></i></div>
                        <span class="menu-text">FAQ & Bantuan</span>
                        <i class="bi bi-chevron-right menu-arrow"></i>
                    </a>
                </div>

                <!-- Logout -->
                <div class="logout-section">
                    <button class="logout-btn" id="btn-logout">
                        <i class="bi bi-box-arrow-right"></i>
                        Keluar dari Akun
                    </button>
                </div>

                <div class="app-version">Billing JMK v1.0 � PT Jernih Multi Komunikasi</div>
            </div>
        </div>

        @include('content.apps.Customer.tagihan.bottom-navbar', ['active' => 'profile'])

        <script>
            document.getElementById('btn-logout').addEventListener('click', () => {
                Swal.fire({
                    title: 'Yakin Ingin Keluar?',
                    html: `?? <strong>Peringatan:</strong><br><span style="font-size: 0.9rem; color: #64748b;">Jika Anda keluar, Anda <b>tidak akan menerima notifikasi</b> tagihan baru dan pengingat jatuh tempo secara realtime. Tetap login untuk selalu update informasi tagihan Anda.</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tetap Keluar',
                    cancelButtonText: 'Batal, Tetap Login',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#0f172a',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Keluar...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                        fetch('/customer/logout', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        }).then(r => {
                            if (r.ok) {
                                Swal.fire({ title: 'Berhasil!', text: 'Anda telah keluar', icon: 'success', timer: 1500, showConfirmButton: false })
                                    .then(() => window.location.href = '/');
                            } else throw new Error();
                        }).catch(() => Swal.fire({ title: 'Error', text: 'Gagal keluar', icon: 'error', confirmButtonColor: '#dc2626' }));
                    }
                });
            });

            // Enable location permission
            const enableLocBtn = document.getElementById('enableLocationBtn');
            if (enableLocBtn) {
                enableLocBtn.addEventListener('click', async () => {
                    if (!navigator.geolocation) {
                        Swal.fire('Perangkat tidak mendukung', 'Browser Anda tidak mendukung akses lokasi.', 'warning');
                        return;
                    }

                    const requestOnce = () => new Promise((resolve) => {
                        navigator.geolocation.getCurrentPosition(
                            (pos) => resolve({ ok: true, pos }),
                            (err) => resolve({ ok: false, err }),
                            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                        );
                    });

                    const res = await requestOnce();
                    if (res.ok) {
                        Swal.fire('Lokasi aktif', 'Izin lokasi sudah diizinkan.', 'success');
                        return;
                    }

                    const steps = `
            <ol style="text-align:left; padding-left:20px; margin:0">
              <li>Ketuk ikon gembok / info situs di address bar.</li>
              <li>Ubah izin <b>Location</b> menjadi <b>Allow</b>.</li>
              <li>Muat ulang halaman, lalu tekan lagi tombol ini.</li>
            </ol>
        `;
                    Swal.fire({
                        icon: 'info',
                        title: 'Izin lokasi diblokir',
                        html: steps,
                        confirmButtonText: 'Oke, saya coba'
                    });
                });
            }
        </script>
</body>

</html>
