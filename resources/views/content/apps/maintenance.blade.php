<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - JernihNet</title>
    <meta name="description" content="Sistem sedang dalam pemeliharaan.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            text-align: center;
            max-width: 520px;
            width: 100%;
        }

        /* Logo/Brand */
        .brand {
            margin-bottom: 40px;
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            background: #1a1a1a;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .brand-logo svg {
            width: 32px;
            height: 32px;
            stroke: #fff;
            fill: none;
            stroke-width: 2;
        }

        .brand-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a1a1a;
            letter-spacing: -0.01em;
        }

        /* Main Card */
        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 48px 40px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        /* Status Icon */
        .status-icon {
            width: 64px;
            height: 64px;
            background: #fef3c7;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }

        .status-icon svg {
            width: 28px;
            height: 28px;
            stroke: #d97706;
            fill: none;
            stroke-width: 2;
        }

        /* Typography */
        .title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }

        .description {
            font-size: 0.95rem;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        /* Info Box */
        .info-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 32px;
        }

        .info-label {
            font-size: 0.75rem;
            font-weight: 500;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 0.9rem;
            font-weight: 500;
            color: #374151;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 28px 0;
        }

        /* Contact */
        .contact-section {
            text-align: left;
        }

        .contact-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 14px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
            text-decoration: none;
            color: #374151;
            transition: color 0.15s;
        }

        .contact-item:last-child {
            border-bottom: none;
        }

        .contact-item:hover {
            color: #111827;
        }

        .contact-item svg {
            width: 18px;
            height: 18px;
            stroke: #9ca3af;
            fill: none;
            stroke-width: 2;
            flex-shrink: 0;
        }

        .contact-item span {
            font-size: 0.875rem;
        }

        /* Footer */
        .footer {
            margin-top: 32px;
            font-size: 0.75rem;
            color: #9ca3af;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .card {
                padding: 36px 24px;
            }

            .title {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Brand -->
        <div class="brand">
            <div class="brand-logo">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
            </div>
            <div class="brand-name">JernihNet</div>
        </div>

        <!-- Main Card -->
        <div class="card">
            <!-- Status Icon -->
            <div class="status-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 6v6l4 2"/>
                </svg>
            </div>

            <!-- Title -->
            <h1 class="title">Sistem Sedang Maintenance</h1>
            
            <!-- Description -->
            <p class="description">
                Kami sedang melakukan pemeliharaan sistem untuk meningkatkan kualitas layanan. 
                Mohon maaf atas ketidaknyamanan ini.
            </p>

            <!-- Info Box -->
            <div class="info-box">
                <div class="info-label">Estimasi Waktu</div>
                <div class="info-value">Pemeliharaan akan selesai dalam waktu dekat</div>
            </div>

            <!-- Divider -->
            <div class="divider"></div>

            <!-- Contact Section -->
            <div class="contact-section">
                <div class="contact-title">Butuh Bantuan?</div>
                
                <a href="https://wa.me/6281234567890" class="contact-item" target="_blank">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>
                    </svg>
                    <span>WhatsApp: 0812-3456-7890</span>
                </a>
                
                <a href="mailto:support@jernihnet.com" class="contact-item">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    <span>support@jernihnet.com</span>
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            © {{ date('Y') }} JernihNet. All rights reserved.
        </div>
    </div>
</body>
</html>
