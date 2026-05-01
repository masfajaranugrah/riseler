<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Invoice Tagihan</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f8fafc;
            font-family: 'Inter', sans-serif;
            padding: 24px 0 100px;
            min-height: 100vh;
            color: #0f172a;
        }

        .container {
            max-width: 680px;
        }

        .invoice-container {
            display: flex;
            flex-direction: column;
        }

        /* Header Section */
        .header-section {
            margin-bottom: 32px;
        }

        .header-section h4 {
            color: #0f172a;
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 6px;
        }

        .header-section p {
            color: #64748b;
            font-size: 0.95rem;
        }

        /* Card Invoice */
        .card-invoice {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            margin-bottom: 20px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .card-invoice:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        /* Card Priority untuk belum bayar */
        .card-invoice.priority {
            border: 2px solid #fecaca;
            box-shadow: 0 4px 16px rgba(239, 68, 68, 0.12);
            order: -1;
        }

        .card-invoice.priority:hover {
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.16);
        }

        /* Card Header */
        .card-header-invoice {
            background: #0f172a;
            padding: 20px 24px;
            color: white;
            border-bottom: 1px solid #1e293b;
        }

        .card-header-invoice h5 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 4px;
            letter-spacing: -0.01em;
        }

        .card-header-invoice small {
            font-size: 0.875rem;
            color: #94a3b8;
        }

        /* Card Body */
        .card-body {
            padding: 24px;
        }

        /* Info Section */
        .info-section {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #f1f5f9;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-item:first-child {
            padding-top: 0;
        }

        .info-label {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
        }

        .info-value {
            font-size: 0.875rem;
            color: #0f172a;
            font-weight: 600;
        }

        /* PPN Notice */
        .ppn-notice {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            border-left: 3px solid #f59e0b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-top: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ppn-notice i {
            color: #f59e0b;
            font-size: 1.1rem;
        }

        .ppn-notice p {
            margin: 0;
            color: #92400e;
            font-weight: 500;
            font-size: 0.875rem;
        }

        /* Price Section */
        .price-section {
            text-align: center;
            padding: 24px 0;
            margin: 20px 0;
        }

        .price-section .period-label {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .price-amount {
            font-size: 2rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .price-text {
            font-size: 0.8125rem;
            color: #94a3b8;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: #f1f5f9;
            margin: 20px 0;
        }

        /* Status Badge */
        .status-wrapper {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 20px;
            font-size: 0.8125rem;
            font-weight: 600;
            border-radius: 100px;
            letter-spacing: 0.02em;
        }

        .status-lunas {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .status-verifikasi {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fef3c7;
        }

        .status-belum {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Button Bayar */
        .btn-bayar {
            margin-top: 16px;
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 600;
            font-size: 0.9375rem;
            background: #0f172a;
            border: none;
            color: white;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-bayar:hover {
            background: #1e293b;
            transform: translateY(-1px);
        }

        .btn-bayar:active {
            transform: translateY(0);
        }

        /* Empty State */
        .empty-state {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 48px 24px;
            text-align: center;
            margin-top: 40px;
        }

        .empty-state i {
            font-size: 3.5rem;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .empty-state h5 {
            color: #0f172a;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 1.125rem;
        }

        .empty-state p {
            color: #64748b;
            margin-bottom: 24px;
            line-height: 1.6;
            font-size: 0.9375rem;
        }

        .empty-state .btn {
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 600;
            background: #0f172a;
            border: none;
            transition: all 0.2s ease;
            font-size: 0.9375rem;
        }

        .empty-state .btn:hover {
            background: #1e293b;
            transform: translateY(-1px);
        }

        /* Bottom Navbar */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 72px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 1000;
        }

        .bottom-nav button {
            background: none;
            border: none;
            text-align: center;
            color: #94a3b8;
            transition: all 0.2s ease;
            padding: 8px 16px;
            border-radius: 8px;
        }

        .bottom-nav button:hover,
        .bottom-nav button.active {
            color: #0f172a;
        }

        .bottom-nav button i {
            font-size: 1.5rem;
            display: block;
            margin-bottom: 4px;
        }

        .bottom-nav button span {
            font-size: 0.6875rem;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 480px) {
            body {
                padding: 16px 0 100px;
            }

            .header-section h4 {
                font-size: 1.5rem;
            }

            .price-amount {
                font-size: 1.75rem;
            }

            .card-body {
                padding: 20px 16px;
            }

            .info-section {
                padding: 16px;
            }
        }

        .cursor-pointer {
            cursor: pointer;
        }

        /* Bank selector */
        .bank-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .bank-card {
            display: flex;
            gap: 12px;
            align-items: center;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .bank-card:hover {
            border-color: #94a3b8;
            background: #f8fafc;
        }

        .bank-card.active {
            border-color: #0f172a;
            background: #f1f5f9;
        }

        .bank-radio {
            display: none;
        }

        .bank-indicator {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
            color: #fff;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .bank-content {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
            min-width: 0;
        }

        .bank-name {
            font-weight: 600;
            color: #0f172a;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .bank-number {
            font-weight: 700;
            color: #334155;
            font-size: 0.95rem;
            font-family: 'SF Mono', 'Monaco', 'Consolas', monospace;
            letter-spacing: 0.03em;
        }

        .bank-owner {
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 500;
        }

        @media (max-width: 480px) {
            .bank-card {
                padding: 12px;
                gap: 10px;
            }

            .bank-indicator {
                width: 38px;
                height: 38px;
                font-size: 1rem;
                border-radius: 8px;
            }

            .bank-name {
                font-size: 0.85rem;
            }

            .bank-number {
                font-size: 0.88rem;
            }

            .bank-owner {
                font-size: 0.75rem;
            }
        }

        /* File Upload Styling */
        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 24px 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #f8fafc;
            position: relative;
        }

        .upload-area:hover {
            border-color: #0f172a;
            background: #f1f5f9;
        }

        .upload-area.has-file {
            border-color: #22c55e;
            background: #f0fdf4;
        }

        .upload-icon {
            width: 56px;
            height: 56px;
            margin: 0 auto 12px;
            background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.5rem;
        }

        .upload-area.has-file .upload-icon {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        }

        .upload-title {
            font-weight: 600;
            color: #0f172a;
            font-size: 0.95rem;
            margin-bottom: 4px;
        }

        .upload-subtitle {
            color: #64748b;
            font-size: 0.8rem;
        }

        .upload-filename {
            margin-top: 10px;
            padding: 8px 12px;
            background: #e2e8f0;
            border-radius: 6px;
            font-size: 0.8rem;
            color: #334155;
            font-weight: 500;
            display: none;
            word-break: break-all;
        }

        .upload-area.has-file .upload-filename {
            display: block;
        }

        .upload-preview {
            display: none;
            margin-top: 10px;
            width: 100%;
            height: 160px;
            border-radius: 12px;
            background: #f1f5f9 center / cover no-repeat;
            border: 1px dashed #cbd5e1;
        }

        .upload-area.has-file .upload-preview {
            display: block;
        }

        .upload-source-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 12px;
        }

        .upload-source-btn {
            height: 50px;
            border-radius: 14px;
            border: 1px solid #cbd5e1;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            color: #334155;
            font-weight: 700;
            font-size: 1.05rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform .15s ease, box-shadow .2s ease, background .2s ease, color .2s ease;
        }

        .upload-source-btn i {
            font-size: 1.1rem;
        }

        .upload-source-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
        }

        .upload-source-btn:active {
            transform: translateY(1px);
        }

        .upload-source-btn-camera {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            border-color: #0f172a;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.2);
        }

        .upload-source-btn-file {
            background: linear-gradient(180deg, #ffffff 0%, #f1f5f9 100%);
            color: #334155;
        }

        .camera-overlay {
            position: fixed;
            inset: 0;
            background: #000;
            display: none;
            flex-direction: column;
            align-items: stretch;
            justify-content: stretch;
            z-index: 2000;
        }

        .camera-box {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100%;
            background: #000;
            position: relative;
        }

        .camera-preview {
            width: 100%;
            flex: 1;
            object-fit: cover;
            display: block;
            border-radius: 0;
            background: #000;
            border: none;
            max-height: none;
        }

        /* Camera fullscreen header */
        .camera-head {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            padding-top: calc(16px + env(safe-area-inset-top, 0px));
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.65) 0%, transparent 100%);
            z-index: 10;
            margin-bottom: 0;
        }

        .camera-title {
            font-weight: 700;
            color: #ffffff;
            font-size: 1.1rem;
            letter-spacing: 0.2px;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.4);
        }

        .camera-close-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: none;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all .2s ease;
            font-size: 1.1rem;
        }

        .camera-close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: #fff;
        }

        /* Camera bottom controls */
        .camera-action-row {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 28px 32px;
            padding-bottom: calc(28px + env(safe-area-inset-bottom, 0px));
            background: linear-gradient(0deg, rgba(0, 0, 0, 0.75) 0%, transparent 100%);
            gap: 0;
            z-index: 10;
            margin-top: 0;
        }

        /* Side buttons (Ulangi & Gunakan) */
        .camera-btn {
            height: auto;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.85rem;
            border: none;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            transition: transform .15s ease, opacity .2s ease;
            white-space: nowrap;
            min-width: 72px;
        }

        .camera-btn:active {
            transform: scale(0.9);
        }

        /* Center capture  big shutter circle */
        .camera-btn-capture {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #fff;
            color: #0f172a;
            font-size: 0;
            box-shadow: 0 0 0 5px rgba(255, 255, 255, 0.25), 0 8px 28px rgba(0, 0, 0, 0.5);
            padding: 0;
            flex-shrink: 0;
            border: 4px solid rgba(255, 255, 255, 0.6);
            position: relative;
        }

        /* Inner circle indicator */
        .camera-btn-capture::before {
            content: '';
            position: absolute;
            inset: 6px;
            border-radius: 50%;
            background: #fff;
            transition: all 0.15s ease;
        }

        .camera-btn-capture:active::before {
            inset: 10px;
        }

        .camera-btn-retake {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            background: transparent;
            color: #fff;
            border: none;
            opacity: 0.9;
            padding: 8px;
        }

        .camera-btn-retake .cam-btn-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            transition: background 0.2s ease;
        }

        .camera-btn-retake:hover .cam-btn-icon {
            background: rgba(255, 255, 255, 0.28);
        }

        .camera-btn-retake .cam-btn-label {
            font-size: 0.72rem;
            font-weight: 600;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
            letter-spacing: 0.3px;
        }

        .camera-btn-retake[disabled] {
            opacity: 0.35;
            cursor: not-allowed;
        }

        .camera-btn-use {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            background: transparent;
            color: #fff;
            border: none;
            opacity: 0.9;
            padding: 8px;
        }

        .camera-btn-use .cam-btn-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(22, 163, 74, 0.7);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            border: 1.5px solid rgba(22, 163, 74, 0.5);
            transition: background 0.2s ease;
        }

        .camera-btn-use:hover .cam-btn-icon {
            background: rgba(22, 163, 74, 0.9);
        }

        .camera-btn-use .cam-btn-label {
            font-size: 0.72rem;
            font-weight: 600;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
            letter-spacing: 0.3px;
        }

        .camera-btn-use[disabled] {
            opacity: 0.35;
            cursor: not-allowed;
        }

        .camera-btn[disabled] {
            opacity: 0.35;
            cursor: not-allowed;
        }

        .camera-hint {
            position: absolute;
            bottom: calc(120px + env(safe-area-inset-bottom, 0px));
            left: 0;
            right: 0;
            margin: 0;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.82rem;
            text-align: center;
            font-weight: 600;
            z-index: 10;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
            letter-spacing: 0.2px;
        }

        @media (max-width: 520px) {
            .upload-source-row {
                grid-template-columns: 1fr;
            }
        }

        /* SweetAlert Custom Styling */
        .swal2-container.swal2-backdrop-show {
            background: rgba(15, 23, 42, 0.45) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .swal2-popup {
            border-radius: 18px;
            padding: 24px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 24px 64px rgba(15, 23, 42, 0.22);
        }

        .swal2-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #0f172a;
        }

        .swal2-actions {
            gap: 10px;
        }

        .swal2-confirm {
            border-radius: 12px !important;
            font-weight: 700 !important;
            padding: 11px 24px !important;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.2);
            transition: transform .15s ease, box-shadow .2s ease, opacity .2s ease !important;
        }

        .swal2-confirm:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 26px rgba(15, 23, 42, 0.25);
        }

        .swal2-cancel {
            border-radius: 12px !important;
            font-weight: 700 !important;
            padding: 11px 24px !important;
            box-shadow: 0 10px 22px rgba(100, 116, 139, 0.18);
            transition: transform .15s ease, box-shadow .2s ease, opacity .2s ease !important;
        }

        .swal2-cancel:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 26px rgba(100, 116, 139, 0.22);
        }

        /* Badge Tunggakan dengan Animasi Ring + Shake */
        .badge-tunggakan {
            position: relative;
            background: #dc2626;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.8125rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            z-index: 1;

            /* Gabungan animasi shake dan pulse */
            animation: shake 0.8s ease-in-out infinite, pulse-badge 2s ease-in-out infinite;
        }

        .badge-tunggakan i {
            font-size: 0.875rem;
        }

        /* Efek Ring Berdering (Pulse Ring) */
        .badge-tunggakan::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 8px;
            border: 2px solid #dc2626;
            opacity: 0.7;
            z-index: -1;
            animation: pulse-ring 2s ease-out infinite;
        }

        /* Ring kedua untuk efek lebih dramatis */
        .badge-tunggakan::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 8px;
            border: 2px solid #dc2626;
            opacity: 0;
            z-index: -1;
            animation: pulse-ring 2s 0.5s ease-out infinite;
        }

        /* Keyframe: Pulse Ring - Efek Berdering */
        @keyframes pulse-ring {
            0% {
                transform: scale(1);
                opacity: 0.7;
            }

            50% {
                transform: scale(1.15);
                opacity: 0.4;
            }

            100% {
                transform: scale(1.3);
                opacity: 0;
            }
        }

        /* Keyframe: Shake - Efek Bergetar */
        @keyframes shake {

            0%,
            100% {
                transform: translateX(0) rotate(0deg);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-2px) rotate(-1deg);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(2px) rotate(1deg);
            }
        }

        /* Keyframe: Pulse Badge - Efek Zoom Halus */
        @keyframes pulse-badge {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        /* Hover: Stop semua animasi */
        .badge-tunggakan:hover {
            animation: none;
        }

        .badge-tunggakan:hover::before,
        .badge-tunggakan:hover::after {
            animation: none;
            opacity: 0;
        }

        /* Responsive untuk mobile */
        @media (max-width: 480px) {
            .badge-tunggakan {
                padding: 6px 12px;
                font-size: 0.75rem;
            }

            .badge-tunggakan::before,
            .badge-tunggakan::after {
                inset: -3px;
                border-width: 1.5px;
            }
        }

        /* Copy rekening button */
        .copy-rek-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }

        .copy-rek-btn:hover {
            background: #0f172a;
            border-color: #0f172a;
            color: white;
        }

        .copy-rek-btn.copied {
            background: #22c55e;
            border-color: #22c55e;
            color: white;
        }

        /* Bukti Action Buttons */
        .bukti-actions {
            display: flex;
            gap: 10px;
            margin-top: 14px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* ======================================
   VERIFICATION ALERT - Shadcn UI Style
====================================== */
        .verification-alert {
            margin-top: 16px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            /* slate-200 */
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .verif-alert-body {
            display: flex;
            gap: 14px;
            margin-bottom: 18px;
            align-items: flex-start;
        }

        .verif-alert-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #f1f5f9;
            /* slate-100 */
            color: #0f172a;
            /* slate-900 */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
            border: 1px solid #e2e8f0;
        }

        .verif-alert-content {
            flex: 1;
            padding-top: 3px;
        }

        .verif-alert-title {
            margin: 0 0 4px 0;
            font-size: 0.95rem;
            font-weight: 600;
            color: #0f172a;
            /* slate-900 */
            line-height: 1.3;
        }

        .verif-alert-text {
            margin: 0;
            font-size: 0.85rem;
            color: #64748b;
            /* slate-500 */
            line-height: 1.4;
        }

        /* Right: action buttons */
        .verif-alert-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .verif-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 40px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            background: transparent;
        }

        .verif-btn:active {
            transform: scale(0.97);
        }

        .verif-btn i {
            font-size: 1rem;
        }

        .verif-btn-view {
            color: #0f172a;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        .verif-btn-view:hover {
            background: #f8fafc;
            /* slate-50 */
            border-color: #cbd5e1;
        }

        .verif-btn-change {
            color: #64748b;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .verif-btn-change:hover {
            color: #0f172a;
            background: #e2e8f0;
        }

        @media (max-width: 380px) {
            .verif-alert-actions {
                grid-template-columns: 1fr;
            }
        }

        .btn-lihat-bukti {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 20px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1.5px solid #3b82f6;
            background: #eff6ff;
            color: #1d4ed8;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-lihat-bukti:hover {
            background: #dbeafe;
            border-color: #2563eb;
            color: #1e40af;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.18);
        }

        .btn-ganti-bukti {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 20px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1.5px solid #f59e0b;
            background: #fffbeb;
            color: #92400e;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-ganti-bukti:hover {
            background: #fef3c7;
            border-color: #d97706;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.18);
        }

        /* Proof Preview Modal */
        .proof-preview-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.72);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 3000;
            padding: 16px;
        }

        .proof-preview-box {
            background: #ffffff;
            border-radius: 20px;
            padding: 20px;
            width: min(500px, 96vw);
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.22);
            border: 1px solid #e2e8f0;
            max-height: 90vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .proof-preview-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .proof-preview-title {
            font-weight: 700;
            color: #0f172a;
            font-size: 1.05rem;
        }

        .proof-close-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .2s ease;
            font-size: 1rem;
        }

        .proof-close-btn:hover {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #dc2626;
        }

        .proof-img-container {
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .proof-img-container img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 12px;
        }

        .proof-pdf-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 40px 20px;
            color: #64748b;
        }

        .proof-pdf-placeholder i {
            font-size: 3rem;
            color: #cbd5e1;
        }

        .proof-pdf-placeholder span {
            font-size: 0.875rem;
            font-weight: 500;
        }

        .proof-actions-row {
            display: flex;
            gap: 10px;
        }

        .proof-btn-ganti {
            flex: 1;
            padding: 11px 16px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all .2s ease;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #fff;
            box-shadow: 0 6px 16px rgba(245, 158, 11, 0.25);
        }

        .proof-btn-ganti:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(245, 158, 11, 0.32);
        }

        .proof-btn-tutup {
            padding: 11px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            color: #64748b;
            cursor: pointer;
            transition: all .2s ease;
        }

        .proof-btn-tutup:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header-section d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0" style="font-weight: 700; color: #334155; font-size: 1.75rem;">Tagihan aktif</h4>
        </div>

        <div class="invoice-container">
            @forelse($tagihans as $tagihan)
            @php
            $pelanggan = $tagihan->pelanggan ?? null;
            $paket = $tagihan->paket ?? null;
            $isPriority = $tagihan->status_pembayaran !== 'lunas' && $tagihan->status_pembayaran !==
            'proses_verifikasi';
            @endphp

            <div class="card card-invoice {{ $isPriority ? 'priority' : '' }}" style="border: 1px solid #f1f5f9; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 20px;">
                @php
                $jatuhTempo = \Carbon\Carbon::parse($tagihan->tanggal_berakhir);
                $sekarang = \Carbon\Carbon::now();

                // Cek apakah sudah lewat bulan (bukan hanya tanggal)
                // Tunggakan = jatuh tempo di bulan sebelumnya atau lebih lama
                $isPastMonth = $jatuhTempo->format('Y-m') < $sekarang->format('Y-m');

                $isUnpaid = $tagihan->status_pembayaran !== 'lunas' && $tagihan->status_pembayaran !== 'proses_verifikasi';

                // Tunggakan muncul hanya jika: belum bayar DAN sudah lewat bulan
                $isTunggakan = $isUnpaid && $isPastMonth;
                @endphp

                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 style="font-weight: 700; color: #334155; font-size: 1.15rem; margin-bottom: 4px;">Periode {{ \Carbon\Carbon::parse($tagihan->tanggal_mulai)->translatedFormat('F Y') }}</h5>
                        <div style="color: #94a3b8; font-size: 0.95rem;">
                            {{ \Carbon\Carbon::parse($tagihan->tanggal_mulai)->translatedFormat('j M') }} - {{ \Carbon\Carbon::parse($tagihan->tanggal_berakhir)->translatedFormat('j M Y') }}
                        </div>
                    </div>
                    @if($isTunggakan)
                    <div style="background: #fee2e2; color: #dc2626; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                        Tunggakan
                    </div>
                    @elseif($isUnpaid)
                    <div style="background: #e0f2fe; color: #0284c7; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                        Tagihan Baru
                    </div>
                    @endif
                </div>

                <hr style="border-color: #f1f5f9; margin: 16px 0;">

                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <div style="color: #94a3b8; font-size: 1.05rem;">Batas Waktu Bayar</div>
                    <div style="color: #334155; font-size: 1.05rem; font-weight: 500;">{{ \Carbon\Carbon::parse($tagihan->tanggal_berakhir)->translatedFormat('j M Y') }}</div>
                </div>
                
                <div class="d-flex justify-content-between mb-4 align-items-center">
                    <div style="color: #94a3b8; font-size: 1.05rem;">Total Tagihan</div>
                    <div style="color: #16a34a; font-size: 1.3rem; font-weight: 700;">+Rp{{ number_format($paket->harga ?? 0, 0, ',', '.') }}</div>
                </div>

                @if($tagihan->status_pembayaran === 'lunas')
                    <div class="status-wrapper mt-0 pt-0 border-0">
                        <span class="status-badge status-lunas w-100 justify-content-center">
                            <i class="bi bi-check-circle-fill"></i> Lunas
                        </span>
                    </div>
                @elseif($tagihan->status_pembayaran === 'proses_verifikasi')
                    <div class="verification-alert mt-0">
                        <div class="verif-alert-body">
                            <div class="verif-alert-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div class="verif-alert-content">
                                <h6 class="verif-alert-title">Verifikasi Pembayaran</h6>
                                <p class="verif-alert-text">Bukti pembayaran Anda sedang ditinjau. Mohon tunggu konfirmasi admin.</p>
                            </div>
                        </div>
                        <div class="verif-alert-actions">
                            @if($tagihan->bukti_pembayaran)
                            <button class="verif-btn verif-btn-view lihat-bukti-btn"
                                data-url="{{ asset('storage/' . $tagihan->bukti_pembayaran) }}"
                                data-type="{{ pathinfo($tagihan->bukti_pembayaran, PATHINFO_EXTENSION) }}"
                                data-rekening-id="{{ $tagihan->type_pembayaran ?? '' }}"
                                data-id="{{ $tagihan->id }}">
                                <i class="bi bi-eye"></i> Lihat Bukti
                            </button>
                            @endif
                            <button class="verif-btn verif-btn-change ganti-bukti-btn"
                                data-rekening-id="{{ $tagihan->type_pembayaran ?? '' }}"
                                data-id="{{ $tagihan->id }}">
                                <i class="bi bi-arrow-repeat"></i> Ganti Bukti
                            </button>
                        </div>
                    </div>
                @else
                    <div class="d-flex gap-2">
                        <button type="button" class="btn flex-grow-1" data-bs-toggle="modal" data-bs-target="#caraBayarModal" style="background: white; color: #0f172a; border: 1px solid #0f172a; font-weight: 600; border-radius: 8px; padding: 10px 0;">Lihat Cara Bayar</button>
                        <button class="btn flex-grow-1 bayar-btn" data-id="{{ $tagihan->id }}" style="background: #0f172a; color: white; border: 1px solid #0f172a; font-weight: 600; border-radius: 8px; padding: 10px 0;">Bayar Sekarang</button>
                    </div>
                @endif
            </div>

            @empty
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h5>Tidak Ada Tagihan</h5>
                <p>Saat ini tidak ada tagihan yang perlu dibayar.<br>Untuk melihat riwayat pembayaran, klik tombol di
                    bawah.</p>
                <a href="https://layanan.jernih.net.id/dashboard/customer/tagihan/selesai">
                    <button class="btn btn-primary">
                        <i class="bi bi-receipt"></i> Lihat Kwitansi
                    </button>
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Cara Bayar -->
    <div class="modal fade" id="caraBayarModal" tabindex="-1" aria-labelledby="caraBayarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content" style="background: #f8fafc;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title" id="caraBayarModalLabel" style="font-weight: 700; color: #0f172a; font-size: 1.25rem;">Langkah-langkah Upload Bukti Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pt-4 pb-4">
                    <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #f1f5f9;">
                        <ol style="line-height: 1.8; font-size: 0.95rem; color: #334155; margin-bottom: 0; padding-left: 20px;">
                            <li class="mb-3">Klik tombol <strong>Bayar Sekarang</strong>.</li>
                            <li class="mb-3">Pilih <strong>Bank Tujuan</strong> transfer kemana.</li>
                            <li class="mb-3">Klik kamera jika ingin langsung foto struk / bisa pilih foto melalui galeri.</li>
                            <li class="mb-3">Setelah bukti pembayaran diunggah, admin akan melakukan konfirmasi.</li>
                            <li class="mb-0">Jika pembayaran sudah dikonfirmasi, kwitansi akan otomatis muncul di menu Kwitansi.</li>
                        </ol>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn w-100" data-bs-dismiss="modal" style="background: #0f172a; color: white; border-radius: 8px; padding: 12px 24px; font-weight: 600;">Mengerti</button>
                </div>
            </div>
        </div>
    </div>

    @include('content.apps.Customer.tagihan.bottom-navbar', ['active' => 'tagihan'])

    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

        /* ================================
           FUNGSI KOMPRES GAMBAR (iOS/Android)
        ================================== */
        function compressImage(file, maxWidth = 1280, quality = 0.7) {
            return new Promise((resolve, reject) => {
                // Skip PDF files
                if (file.type === "application/pdf") {
                    resolve(file);
                    return;
                }

                // Check if file is an image
                if (!file.type.startsWith("image/")) {
                    resolve(file);
                    return;
                }

                const reader = new FileReader();

                reader.onload = (event) => {
                    const img = new Image();

                    img.onload = () => {
                        try {
                            const canvas = document.createElement("canvas");
                            const ctx = canvas.getContext("2d");

                            // Calculate new dimensions
                            let width = img.width;
                            let height = img.height;

                            if (width > maxWidth) {
                                height = Math.round((height * maxWidth) / width);
                                width = maxWidth;
                            }

                            canvas.width = width;
                            canvas.height = height;

                            // Fill white background for JPG (handles transparency)
                            ctx.fillStyle = "#FFFFFF";
                            ctx.fillRect(0, 0, width, height);

                            // Draw image
                            ctx.drawImage(img, 0, 0, width, height);

                            // Determine output type - keep original format or use JPEG
                            let outputType = "image/jpeg";
                            let outputQuality = quality;

                            // For PNG with transparency, keep as PNG
                            if (file.type === "image/png") {
                                outputType = "image/jpeg"; // Convert PNG to JPEG for smaller size
                            }

                            canvas.toBlob(
                                (blob) => {
                                    if (!blob) {
                                        console.warn("Blob creation failed, using original file");
                                        resolve(file);
                                        return;
                                    }

                                    // Amankan filename saat compress
                                    let baseName = "image_comp";
                                    if (file && typeof file.name === 'string' && file.name.trim() !== '') {
                                        try {
                                            // Safari kadangkala gagal di regex kompleks jika string kotor
                                            let parts = file.name.split('.');
                                            parts.pop(); // buang ext
                                            let nameOnly = parts.join('.');
                                            baseName = nameOnly.replace(/[^a-zA-Z0-9_-]/g, "") || "image_comp";
                                        } catch (e) {
                                            baseName = "img_safe";
                                        }
                                    }
                                    let newFileName = baseName + ".jpg";

                                    const compressedFile = new File([blob], newFileName, {
                                        type: outputType,
                                        lastModified: Date.now()
                                    });

                                    console.log(`Compressed: ${file.size} -> ${compressedFile.size} bytes`);
                                    resolve(compressedFile);
                                },
                                outputType,
                                outputQuality
                            );
                        } catch (err) {
                            console.error("Canvas error:", err);
                            resolve(file); // Return original on error
                        }
                    };

                    img.onerror = () => {
                        console.warn("Image load failed, using original file");
                        resolve(file);
                    };

                    img.src = event.target.result;
                };

                reader.onerror = () => {
                    console.warn("FileReader error, using original file");
                    resolve(file);
                };

                reader.readAsDataURL(file);
            });
        }

        function sanitizeFileName(name, fallbackExt = 'jpg') {
            const ts = Date.now();
            if (!name || typeof name !== 'string') {
                return `image_${ts}.${fallbackExt}`;
            }
            const trimmed = name.trim();
            if (!trimmed) {
                return `image_${ts}.${fallbackExt}`;
            }
            const parts = trimmed.split('.');
            let ext = parts.length > 1 ? parts.pop() : fallbackExt;
            let base = parts.join('.') || 'image';
            base = base.replace(/[^a-zA-Z0-9_-]/g, '') || 'image';
            ext = ext.replace(/[^a-zA-Z0-9]/g, '') || fallbackExt;
            return `${base}.${ext}`;
        }

        function appendProofFile(formData, field, file) {
            const fallbackExt = file && file.type === 'application/pdf' ? 'pdf' : 'jpg';
            const safeName = sanitizeFileName(file && file.name, fallbackExt);
            try {
                formData.append(field, file, safeName);
            } catch (e) {
                const blob = file instanceof Blob ? file : new Blob([file], { type: (file && file.type) || 'application/octet-stream' });
                formData.append(field, blob, safeName);
            }
        }

        function buildTagihanUrl(tagihanId) {
            if (tagihanId === undefined || tagihanId === null || tagihanId === '') return null;
            return `/dashboard/customer/tagihan/${encodeURIComponent(String(tagihanId))}`;
        }

        // HELPER: Upload menggunakan XMLHttpRequest untuk bypass bug Fetch di Safari/iOS
        function uploadFormDataXhr(url, formData) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', url, true);
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.content);
                }
                xhr.setRequestHeader('Accept', 'application/json');

                xhr.onload = function () {
                    let data;
                    try { data = JSON.parse(xhr.responseText); }
                    catch (e) { return reject(new Error("Server mengembalikan respons tidak valid.")); }

                    if (xhr.status >= 200 && xhr.status < 300) {
                        if (data.success === false) reject(new Error(data.message || "Gagal memproses data."));
                        else resolve(data);
                    } else {
                        reject(new Error(data.message || `Server error (${xhr.status}).`));
                    }
                };
                xhr.onerror = function () { reject(new Error("Koneksi gagal atau terputus.")); };
                xhr.send(formData);
            });
        }

        /* ================================
           EVENT BAYAR
        ================================== */
        document.querySelectorAll('.bayar-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const tagihanId = btn.dataset.id;
                const rekenings = @json($rekenings);

                let htmlRekening = '<div class="bank-list">';
                rekenings.forEach(r => {
                    htmlRekening += `
            <label class="bank-card">
                <input type="radio" class="bank-radio" name="type_pembayaran" value="${r.id}">
                <div class="bank-indicator"><i class="bi bi-bank"></i></div>
                <div class="bank-content">
                    <div class="bank-name">${r.nama_bank}</div>
                    <div class="bank-number">${r.nomor_rekening}</div>
                    <div class="bank-owner">a.n ${r.nama_pemilik}</div>
                </div>
          
            </label>`;
                });
                htmlRekening += '</div>';

                Swal.fire({
                    title: 'Pilih Rekening Tujuan',
                    html: htmlRekening,
                    showCancelButton: true,
                    confirmButtonText: 'Lanjutkan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#0f172a',
                    cancelButtonColor: '#94a3b8',
                    preConfirm: () => {
                        const selected = document.querySelector('input[name="type_pembayaran"]:checked');
                        if (!selected) Swal.showValidationMessage('Pilih salah satu rekening!');
                        return selected ? selected.value : null;
                    }
                }).then(result => {
                    if (!result.isConfirmed) return;
                    const selectedRekening = rekenings.find(r => r.id == result.value);
                    let previewUrl = null;
                    let camOverlay = null;
                    let stopCamera = () => { };
                    let setCameraMode = () => { };

                    Swal.fire({
                        title: 'Upload Bukti Pembayaran',
                        customClass: { popup: 'upload-proof-popup' },
                        html: `
                    <div style="background: #f8fafc; padding: 14px 16px; border-radius: 10px; margin-bottom: 16px; text-align: left; border: 1px solid #e2e8f0;">
                        <p style="margin: 0; color: #0f172a; font-weight: 600; font-size: 0.9rem;">${selectedRekening.nama_bank}</p>
                        <p style="margin: 3px 0 0 0; color: #334155; font-size: 0.85rem; font-family: 'SF Mono', monospace; font-weight: 600;">${selectedRekening.nomor_rekening}</p>
                        <p style="margin: 3px 0 0 0; color: #64748b; font-size: 0.8rem;">a.n ${selectedRekening.nama_pemilik}</p>
                    </div>
                    <div class="upload-area" id="upload-area">
                        <div class="upload-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                        <div class="upload-title">Pilih file atau ambil foto</div>
                        <div class="upload-subtitle">Foto kamera (langsung), atau ambil dari galeri/file (JPG, PNG, PDF maks 5MB)</div>
                        <div class="upload-source-row">
                            <button type="button" id="btn-camera" class="upload-source-btn upload-source-btn-camera">
                                <i class="bi bi-camera"></i> Kamera
                            </button>
                            <button type="button" id="btn-file" class="upload-source-btn upload-source-btn-file">
                                <i class="bi bi-folder2-open"></i> Galeri / File
                            </button>
                        </div>
                        <div class="upload-filename" id="upload-filename"></div>
                        <div class="upload-preview" id="upload-preview"></div>
                        <input type="file" id="bukti-pembayaran" accept="image/*,application/pdf" style="display: none;">
                        <input type="file" id="bukti-pembayaran-kamera" accept="image/*" capture="environment" style="display: none;">
                    </div>

                    <div class="camera-overlay" id="camera-overlay">
                        <div class="camera-box">
                            <div class="camera-head">
                                <div class="camera-title">Ambil Foto Bukti</div>
                                <button type="button" id="btn-cam-close" class="camera-close-btn"><i class="bi bi-x-lg"></i></button>
                            </div>
                            <video id="cam-video" class="camera-preview" autoplay playsinline></video>
                            <canvas id="cam-canvas" class="camera-preview" style="display:none;"></canvas>
                            <div class="camera-action-row">
                                <button type="button" id="btn-retake" class="camera-btn camera-btn-retake" disabled>
                                    <span class="cam-btn-icon"><i class="bi bi-arrow-counterclockwise"></i></span>
                                    <span class="cam-btn-label">Ulangi</span>
                                </button>
                                <button type="button" id="btn-capture" class="camera-btn camera-btn-capture"></button>
                                <button type="button" id="btn-use" class="camera-btn camera-btn-use" disabled>
                                    <span class="cam-btn-icon"><i class="bi bi-check-lg"></i></span>
                                    <span class="cam-btn-label">Gunakan</span>
                                </button>
                            </div>
                            <div id="cam-hint" class="camera-hint">Arahkan kamera ke bukti pembayaran, tekan Potret.</div>
                        </div>
                    </div>
                `,
                        didOpen: () => {
                            const popup = Swal.getPopup();
                            const actions = popup ? popup.querySelector('.swal2-actions') : null;
                            const uploadArea = document.getElementById('upload-area');
                            const fileInput = document.getElementById('bukti-pembayaran');
                            const cameraInput = document.getElementById('bukti-pembayaran-kamera');
                            const btnCamera = document.getElementById('btn-camera');
                            const btnFile = document.getElementById('btn-file');
                            const filenameEl = document.getElementById('upload-filename');
                            const previewEl = document.getElementById('upload-preview');
                            camOverlay = document.getElementById('camera-overlay');
                            const camVideo = document.getElementById('cam-video');
                            const camCanvas = document.getElementById('cam-canvas');
                            const btnCamClose = document.getElementById('btn-cam-close');
                            const btnCapture = document.getElementById('btn-capture');
                            const btnRetake = document.getElementById('btn-retake');
                            const btnUse = document.getElementById('btn-use');
                            const camHint = document.getElementById('cam-hint');

                            let selectedCameraFile = null;
                            let cameraStream = null;

                            setCameraMode = (active) => {
                                if (popup) popup.classList.toggle('camera-active', active);
                                if (actions) actions.style.display = active ? 'none' : '';
                            };

                            stopCamera = () => {
                                if (cameraStream) {
                                    cameraStream.getTracks().forEach(t => t.stop());
                                    cameraStream = null;
                                }
                            };

                            async function openCameraFlow() {
                                selectedCameraFile = null;
                                stopCamera();

                                try {
                                    cameraStream = await navigator.mediaDevices.getUserMedia({
                                        video: {
                                            facingMode: { ideal: 'environment' },
                                            width: { ideal: 1920, min: 1280 },
                                            height: { ideal: 1080, min: 720 },
                                            advanced: [{ focusMode: 'continuous' }]
                                        }
                                    });
                                    camVideo.srcObject = cameraStream;
                                    await camVideo.play();
                                } catch (e) {
                                    Swal.showValidationMessage('Izin kamera ditolak atau kamera tidak tersedia di browser ini.');
                                    return;
                                }

                                camCanvas.style.display = 'none';
                                camVideo.style.display = 'block';
                                btnRetake.disabled = true;
                                camHint.textContent = 'Arahkan kamera ke bukti pembayaran, tekan Potret.';
                                camHint.style.color = 'rgba(255,255,255,0.85)';

                                // Pindahkan ke body agar benar-benar fullscreen (escape SweetAlert stacking context)
                                if (camOverlay.parentNode !== document.body) {
                                    camOverlay._origParent = camOverlay.parentNode;
                                    document.body.appendChild(camOverlay);
                                }
                                camOverlay.style.display = 'flex';
                                setCameraMode(true);
                            }

                            // Default tap opens file picker (lebih familiar)
                            uploadArea.addEventListener('click', () => {
                                cameraInput.value = '';
                                fileInput.click();
                            });
                            btnFile.addEventListener('click', (e) => {
                                e.stopPropagation();
                                cameraInput.value = '';
                                fileInput.click();
                            });
                            btnCamera.addEventListener('click', async (e) => {
                                e.stopPropagation();
                                fileInput.value = '';
                                cameraInput.value = '';
                                await openCameraFlow();
                            });

                            btnCamClose.addEventListener('click', () => {
                                selectedCameraFile = null;
                                camOverlay.style.display = 'none';
                                // Kembalikan ke parent asli
                                if (camOverlay._origParent) { camOverlay._origParent.appendChild(camOverlay); camOverlay._origParent = null; }
                                stopCamera();
                                setCameraMode(false);
                            });

                            btnCapture.addEventListener('click', () => {
                                if (!cameraStream) return;
                                camCanvas.width = camVideo.videoWidth;
                                camCanvas.height = camVideo.videoHeight;
                                const ctx = camCanvas.getContext('2d');
                                ctx.drawImage(camVideo, 0, 0, camCanvas.width, camCanvas.height);
                                camCanvas.toBlob(blob => {
                                    if (!blob) return;

                                    // Gunakan format minimal untuk File object agar tembus multipart form-data tanpa exception
                                    const ext = "jpg";
                                    const timestamp = new Date().getTime();
                                    const fileName = `bukti_${timestamp}.${ext}`;

                                    selectedCameraFile = new File([blob], fileName, {
                                        type: 'image/jpeg'
                                    });

                                    camCanvas.style.display = 'block';
                                    camVideo.style.display = 'none';
                                    btnRetake.disabled = false;
                                    btnUse.disabled = false;
                                    camHint.textContent = 'Foto diambil. Gunakan atau Ulangi.';
                                }, 'image/jpeg', 0.85);
                            });

                            btnRetake.addEventListener('click', () => {
                                selectedCameraFile = null;
                                camCanvas.style.display = 'none';
                                camVideo.style.display = 'block';
                                btnRetake.disabled = true;
                                btnUse.disabled = true;
                                camHint.textContent = 'Arahkan kamera ke bukti pembayaran, tekan Potret.';
                            });

                            btnUse.addEventListener('click', () => {
                                if (!selectedCameraFile) {
                                    camHint.textContent = 'Ambil foto dulu sebelum digunakan.';
                                    camHint.style.color = '#dc2626';
                                    return;
                                }
                                camHint.style.color = '#64748b';
                                camOverlay.style.display = 'none';
                                stopCamera();
                                setCameraMode(false);
                                uploadArea.selectedCameraFile = selectedCameraFile;
                                fileInput.value = '';
                                cameraInput.value = '';
                                updateFileDisplay(selectedCameraFile, 'kamera');
                            });

                            uploadArea.addEventListener('dragover', (e) => {
                                e.preventDefault();
                                uploadArea.style.borderColor = '#0f172a';
                                uploadArea.style.background = '#f1f5f9';
                            });

                            uploadArea.addEventListener('dragleave', () => {
                                if (!uploadArea.classList.contains('has-file')) {
                                    uploadArea.style.borderColor = '#cbd5e1';
                                    uploadArea.style.background = '#f8fafc';
                                }
                            });

                            uploadArea.addEventListener('drop', (e) => {
                                e.preventDefault();
                                if (e.dataTransfer.files.length) {
                                    fileInput.files = e.dataTransfer.files;
                                    updateFileDisplay(e.dataTransfer.files[0], 'file');
                                    uploadArea.selectedCameraFile = null;
                                }
                            });

                            fileInput.addEventListener('change', () => {
                                if (fileInput.files.length) {
                                    updateFileDisplay(fileInput.files[0], 'file');
                                    uploadArea.selectedCameraFile = null;
                                }
                            });

                            cameraInput.addEventListener('change', () => {
                                if (cameraInput.files.length) {
                                    updateFileDisplay(cameraInput.files[0], 'kamera');
                                    uploadArea.selectedCameraFile = cameraInput.files[0];
                                }
                            });

                            function updateFileDisplay(file, source) {
                                uploadArea.classList.add('has-file');
                                filenameEl.textContent = file.name;
                                uploadArea.querySelector('.upload-title').textContent =
                                    source === 'kamera' ? 'Foto terpilih' : 'File terpilih';
                                uploadArea.querySelector('.upload-icon i').className = 'bi bi-check-lg';

                                // Show preview for images
                                if (previewUrl) {
                                    URL.revokeObjectURL(previewUrl);
                                    previewUrl = null;
                                }
                                if (file.type.startsWith('image/')) {
                                    previewUrl = URL.createObjectURL(file);
                                    previewEl.style.backgroundImage = `url(${previewUrl})`;
                                    previewEl.style.display = 'block';
                                } else {
                                    previewEl.style.display = 'none';
                                }
                            }

                            function getSelectedFile() {
                                if (uploadArea.selectedCameraFile) return uploadArea.selectedCameraFile;
                                if (cameraInput.files.length) return cameraInput.files[0];
                                if (fileInput.files.length) return fileInput.files[0];
                                return null;
                            }

                            uploadArea.getSelectedFile = getSelectedFile;
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Kirim',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#0f172a',
                        cancelButtonColor: '#94a3b8',
                        showLoaderOnConfirm: true,
                        willClose: () => {
                            if (previewUrl) URL.revokeObjectURL(previewUrl);
                            if (camOverlay) camOverlay.style.display = 'none';
                            setCameraMode(false);
                            stopCamera();
                        },

                        preConfirm: async () => {
                            const url = buildTagihanUrl(tagihanId);
                            if (!url) return Swal.showValidationMessage('Tagihan tidak ditemukan.');

                            const uploadArea = document.getElementById('upload-area');
                            const selectedFile = uploadArea.getSelectedFile ? uploadArea.getSelectedFile() : null;
                            if (!selectedFile) return Swal.showValidationMessage('Pilih file atau ambil foto bukti pembayaran!');

                            let file = selectedFile;

                            // Cek batas file lokal sblm di kompres
                            if (file.size > 15 * 1024 * 1024) {
                                return Swal.showValidationMessage('File terlalu besar (Maksimal 15MB)');
                            }

                            try {
                                file = await compressImage(file);
                            } catch (e) {
                                console.warn("Gagal kompresi, pakai aslinya:", e);
                            }

                            // Validasi Limit akhir ke backend
                            if (file.size > 5 * 1024 * 1024) {
                                return Swal.showValidationMessage('File hasil foto/pilihan masih melebihi 5MB, gunakan file yang lebih kecil.');
                            }
                            const formData = new FormData();
                            appendProofFile(formData, 'bukti_pembayaran', file);
                            formData.append('type_pembayaran', selectedRekening.id);
                            formData.append('_method', 'PUT');

                            return uploadFormDataXhr(url, formData)
                                .catch(err => Swal.showValidationMessage(`Gagal upload: ${err.message}`));
                        }
                    }).then(uploadResult => {
                        if (uploadResult.isConfirmed) {
                            const statusWrapper = btn.closest('.status-wrapper');
                            const cardInvoice = btn.closest('.card-invoice');

                            if (statusWrapper) {
                                statusWrapper.innerHTML = `
                            <span class="status-badge status-verifikasi">
                                <i class="bi bi-clock-fill"></i> Menunggu Verifikasi
                            </span>
                        `;
                            }
                            if (cardInvoice) {
                                cardInvoice.classList.remove('priority');
                            }

                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Bukti pembayaran terkirim. Status berubah ke Menunggu Verifikasi.',
                                icon: 'success',
                                confirmButtonColor: '#0f172a'
                            });
                        }
                    });
                });
            });
        });

        // Hover & active effect untuk rekening cards
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('change', function (e) {
                if (e.target.classList.contains('bank-radio')) {
                    document.querySelectorAll('.bank-card').forEach(card => {
                        const radio = card.querySelector('.bank-radio');
                        card.classList.toggle('active', radio && radio.checked);
                    });
                }
            });
        });

        // Copy nomor rekening
        function copyRekening(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                const icon = btn.querySelector('i');
                icon.className = 'bi bi-check-lg';
                btn.classList.add('copied');
                setTimeout(() => {
                    icon.className = 'bi bi-clipboard';
                    btn.classList.remove('copied');
                }, 2000);
            }).catch(() => {
                // Fallback
                const ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                const icon = btn.querySelector('i');
                icon.className = 'bi bi-check-lg';
                btn.classList.add('copied');
                setTimeout(() => {
                    icon.className = 'bi bi-clipboard';
                    btn.classList.remove('copied');
                }, 2000);
            });
        }

        /* ================================
           PROOF PREVIEW OVERLAY (DOM)
        ================================== */
        (function () {
            const overlay = document.createElement('div');
            overlay.className = 'proof-preview-overlay';
            overlay.id = 'proof-preview-overlay';
            overlay.innerHTML = `
        <div class="proof-preview-box">
            <div class="proof-preview-head">
                <div class="proof-preview-title"><i class="bi bi-receipt" style="margin-right:6px;"></i>Bukti Pembayaran</div>
                <button class="proof-close-btn" id="proof-close-btn"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="proof-img-container" id="proof-img-container"></div>
            <div class="proof-actions-row">
                <button class="proof-btn-ganti" id="proof-trigger-ganti"><i class="bi bi-arrow-repeat"></i> Ganti Bukti Pembayaran</button>
                <button class="proof-btn-tutup" id="proof-close-btn2">Tutup</button>
            </div>
        </div>
    `;
            document.body.appendChild(overlay);

            function closeProofOverlay() {
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            }

            document.getElementById('proof-close-btn').addEventListener('click', closeProofOverlay);
            document.getElementById('proof-close-btn2').addEventListener('click', closeProofOverlay);
            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) closeProofOverlay();
            });

            // Lihat Bukti
            document.querySelectorAll('.lihat-bukti-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const url = this.dataset.url;
                    const ext = (this.dataset.type || '').toLowerCase();
                    const tagihanId = this.dataset.id;
                    const rekeningId = this.dataset.rekeningId;
                    const container = document.getElementById('proof-img-container');

                    if (ext === 'pdf') {
                        container.innerHTML = `
                    <div class="proof-pdf-placeholder">
                        <i class="bi bi-file-earmark-pdf"></i>
                        <span>File PDF tidak dapat dipratinjau</span>
                        <a href="${url}" target="_blank" style="color:#3b82f6;font-weight:600;font-size:0.875rem;">Buka PDF &#8594;</a>
                    </div>`;
                    } else {
                        container.innerHTML = '<img src="' + url + '" alt="Bukti Pembayaran" loading="lazy" style="width:100%;border-radius:12px;display:block;" onerror="this.style.display=\'none\'">';
                    }

                    // Simpan tagihanId di tombol ganti
                    const btnGanti = document.getElementById('proof-trigger-ganti');
                    btnGanti.dataset.id = tagihanId;
                    btnGanti.dataset.rekeningId = rekeningId;

                    overlay.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                });
            });

            // Tombol Ganti dari overlay preview
            document.getElementById('proof-trigger-ganti').addEventListener('click', function () {
                const tagihanId = this.dataset.id;
                const rekeningId = this.dataset.rekeningId;
                closeProofOverlay();
                triggerGantiBukti(tagihanId, rekeningId);
            });
        })();

        /* ================================
           GANTI BUKTI PEMBAYARAN
        ================================== */
        function triggerGantiBukti(tagihanId, rekeningId) {
            let previewUrl = null;
            let camOverlay = null;
            let stopCamera = () => { };
            let setCameraMode = () => { };

            Swal.fire({
                title: 'Ganti Bukti Pembayaran',
                customClass: { popup: 'upload-proof-popup' },
                html: `
            <p style="color:#92400e;background:#fffbeb;border:1px solid #fef3c7;border-left:3px solid #f59e0b;border-radius:8px;padding:10px 14px;font-size:0.85rem;font-weight:600;text-align:left;margin-bottom:14px;">
                <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i>
                Mengganti bukti akan menghapus file sebelumnya dan mengubah status kembali ke Menunggu Verifikasi.
            </p>
            <div class="upload-area" id="upload-area-ganti">
                <div class="upload-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                <div class="upload-title">Pilih file atau ambil foto baru</div>
                <div class="upload-subtitle">JPG, PNG, PDF maks 5MB</div>
                <div class="upload-source-row">
                    <button type="button" id="btn-camera-ganti" class="upload-source-btn upload-source-btn-camera">
                        <i class="bi bi-camera"></i> Kamera
                    </button>
                    <button type="button" id="btn-file-ganti" class="upload-source-btn upload-source-btn-file">
                        <i class="bi bi-folder2-open"></i> Galeri / File
                    </button>
                </div>
                <div class="upload-filename" id="upload-filename-ganti"></div>
                <div class="upload-preview" id="upload-preview-ganti"></div>
                <input type="file" id="bukti-pembayaran-ganti" accept="image/*,application/pdf" style="display: none;">
                <input type="file" id="bukti-pembayaran-kamera-ganti" accept="image/*" capture="environment" style="display: none;">
            </div>

            <div class="camera-overlay" id="camera-overlay-ganti">
                <div class="camera-box">
                    <div class="camera-head">
                        <div class="camera-title">Ambil Foto Bukti</div>
                        <button type="button" id="btn-cam-close-ganti" class="camera-close-btn"><i class="bi bi-x-lg"></i></button>
                    </div>
                    <video id="cam-video-ganti" class="camera-preview" autoplay playsinline></video>
                    <canvas id="cam-canvas-ganti" class="camera-preview" style="display:none;"></canvas>
                    <div class="camera-action-row">
                        <button type="button" id="btn-retake-ganti" class="camera-btn camera-btn-retake" disabled>
                            <span class="cam-btn-icon"><i class="bi bi-arrow-counterclockwise"></i></span>
                            <span class="cam-btn-label">Ulangi</span>
                        </button>
                        <button type="button" id="btn-capture-ganti" class="camera-btn camera-btn-capture"></button>
                        <button type="button" id="btn-use-ganti" class="camera-btn camera-btn-use" disabled>
                            <span class="cam-btn-icon"><i class="bi bi-check-lg"></i></span>
                            <span class="cam-btn-label">Gunakan</span>
                        </button>
                    </div>
                    <div id="cam-hint-ganti" class="camera-hint">Arahkan kamera ke bukti pembayaran, tekan Potret.</div>
                </div>
            </div>
        `,
                didOpen: () => {
                    const popup = Swal.getPopup();
                    const actions = popup ? popup.querySelector('.swal2-actions') : null;
                    const uploadArea = document.getElementById('upload-area-ganti');
                    const fileInput = document.getElementById('bukti-pembayaran-ganti');
                    const cameraInput = document.getElementById('bukti-pembayaran-kamera-ganti');
                    const btnCamera = document.getElementById('btn-camera-ganti');
                    const btnFile = document.getElementById('btn-file-ganti');
                    const filenameEl = document.getElementById('upload-filename-ganti');
                    const previewEl = document.getElementById('upload-preview-ganti');
                    camOverlay = document.getElementById('camera-overlay-ganti');
                    const camVideo = document.getElementById('cam-video-ganti');
                    const camCanvas = document.getElementById('cam-canvas-ganti');
                    const btnCamClose = document.getElementById('btn-cam-close-ganti');
                    const btnCapture = document.getElementById('btn-capture-ganti');
                    const btnRetake = document.getElementById('btn-retake-ganti');
                    const btnUse = document.getElementById('btn-use-ganti');
                    const camHint = document.getElementById('cam-hint-ganti');

                    let selectedCameraFile = null;
                    let cameraStream = null;

                    setCameraMode = (active) => {
                        if (popup) popup.classList.toggle('camera-active', active);
                        if (actions) actions.style.display = active ? 'none' : '';
                    };

                    stopCamera = () => {
                        if (cameraStream) {
                            cameraStream.getTracks().forEach(t => t.stop());
                            cameraStream = null;
                        }
                    };

                    async function openCameraFlow() {
                        selectedCameraFile = null;
                        stopCamera();
                        try {
                            cameraStream = await navigator.mediaDevices.getUserMedia({
                                video: {
                                    facingMode: { ideal: 'environment' },
                                    width: { ideal: 1920, min: 1280 },
                                    height: { ideal: 1080, min: 720 },
                                    advanced: [{ focusMode: 'continuous' }]
                                }
                            });
                            camVideo.srcObject = cameraStream;
                            await camVideo.play();
                        } catch (e) {
                            Swal.showValidationMessage('Izin kamera ditolak atau kamera tidak tersedia.');
                            return;
                        }
                        camCanvas.style.display = 'none';
                        camVideo.style.display = 'block';
                        btnRetake.disabled = true;
                        camHint.textContent = 'Arahkan kamera ke bukti pembayaran, tekan Potret.';
                        camHint.style.color = 'rgba(255,255,255,0.85)';

                        // Pindahkan ke body agar benar-benar fullscreen
                        if (camOverlay.parentNode !== document.body) {
                            camOverlay._origParent = camOverlay.parentNode;
                            document.body.appendChild(camOverlay);
                        }
                        camOverlay.style.display = 'flex';
                        setCameraMode(true);
                    }

                    uploadArea.addEventListener('click', () => { fileInput.click(); });
                    btnFile.addEventListener('click', (e) => { e.stopPropagation(); fileInput.click(); });
                    btnCamera.addEventListener('click', async (e) => { e.stopPropagation(); await openCameraFlow(); });
                    btnCamClose.addEventListener('click', () => {
                        selectedCameraFile = null;
                        camOverlay.style.display = 'none';
                        // Kembalikan ke parent asli
                        if (camOverlay._origParent) { camOverlay._origParent.appendChild(camOverlay); camOverlay._origParent = null; }
                        stopCamera();
                        setCameraMode(false);
                    });
                    btnCapture.addEventListener('click', () => {
                        if (!cameraStream) return;
                        camCanvas.width = camVideo.videoWidth;
                        camCanvas.height = camVideo.videoHeight;
                        camCanvas.getContext('2d').drawImage(camVideo, 0, 0, camCanvas.width, camCanvas.height);
                        camCanvas.toBlob(blob => {
                            if (!blob) return;

                            const timestamp = new Date().getTime();
                            const fileName = `ganti_${timestamp}.jpg`;

                            selectedCameraFile = new File([blob], fileName, {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });

                            camCanvas.style.display = 'block';
                            camVideo.style.display = 'none';
                            btnRetake.disabled = false;
                            btnUse.disabled = false;
                            camHint.textContent = 'Foto diambil. Gunakan atau Ulangi.';
                        }, 'image/jpeg', 0.85);
                    });

                    btnRetake.addEventListener('click', () => {
                        selectedCameraFile = null;
                        camCanvas.style.display = 'none';
                        camVideo.style.display = 'block';
                        btnRetake.disabled = true;
                        btnUse.disabled = true;
                        camHint.textContent = 'Arahkan kamera ke bukti pembayaran, tekan Potret.';
                    });

                    btnUse.addEventListener('click', () => {
                        if (!selectedCameraFile) {
                            camHint.textContent = 'Ambil foto dulu sebelum digunakan.';
                            camHint.style.color = '#dc2626';
                            return;
                        }
                        camHint.style.color = '#64748b';
                        camOverlay.style.display = 'none';
                        stopCamera();
                        setCameraMode(false);
                        uploadArea.selectedCameraFile = selectedCameraFile;
                        fileInput.value = '';
                        cameraInput.value = '';
                        updateFileDisplay(selectedCameraFile, 'kamera');
                    });

                    uploadArea.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        uploadArea.style.borderColor = '#0f172a';
                        uploadArea.style.background = '#f1f5f9';
                    });

                    uploadArea.addEventListener('dragleave', () => {
                        if (!uploadArea.classList.contains('has-file')) {
                            uploadArea.style.borderColor = '#cbd5e1';
                            uploadArea.style.background = '#f8fafc';
                        }
                    });

                    uploadArea.addEventListener('drop', (e) => {
                        e.preventDefault();
                        if (e.dataTransfer.files.length) {
                            fileInput.files = e.dataTransfer.files;
                            updateFileDisplay(e.dataTransfer.files[0], 'file');
                            uploadArea.selectedCameraFile = null;
                        }
                    });

                    fileInput.addEventListener('change', () => {
                        if (fileInput.files.length) {
                            updateFileDisplay(fileInput.files[0], 'file');
                            uploadArea.selectedCameraFile = null;
                        }
                    });

                    cameraInput.addEventListener('change', () => {
                        if (cameraInput.files.length) {
                            updateFileDisplay(cameraInput.files[0], 'kamera');
                            uploadArea.selectedCameraFile = cameraInput.files[0];
                        }
                    });

                    function updateFileDisplay(file, source) {
                        uploadArea.classList.add('has-file');
                        filenameEl.textContent = file.name;
                        uploadArea.querySelector('.upload-title').textContent = source === 'kamera' ? 'Foto terpilih' : 'File terpilih';
                        uploadArea.querySelector('.upload-icon i').className = 'bi bi-check-lg';

                        if (previewUrl) {
                            URL.revokeObjectURL(previewUrl);
                            previewUrl = null;
                        }
                        if (file.type.startsWith('image/')) {
                            previewUrl = URL.createObjectURL(file);
                            previewEl.style.backgroundImage = `url(${previewUrl})`;
                            previewEl.style.display = 'block';
                        } else {
                            previewEl.style.display = 'none';
                        }
                    }

                    uploadArea.getSelectedFile = function () {
                        if (uploadArea.selectedCameraFile) return uploadArea.selectedCameraFile;
                        if (cameraInput.files.length) return cameraInput.files[0];
                        if (fileInput.files.length) return fileInput.files[0];
                        return null;
                    };
                },
                showCancelButton: true,
                confirmButtonText: '<i class="bi bi-arrow-repeat"></i> Ganti Sekarang',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d97706',
                cancelButtonColor: '#94a3b8',
                showLoaderOnConfirm: true,
                willClose: () => {
                    if (previewUrl) URL.revokeObjectURL(previewUrl);
                    if (camOverlay) camOverlay.style.display = 'none';
                    setCameraMode(false);
                    stopCamera();
                },
                preConfirm: async () => {
                    const url = buildTagihanUrl(tagihanId);
                    if (!url) return Swal.showValidationMessage('Tagihan tidak ditemukan.');

                    const uploadArea = document.getElementById('upload-area-ganti');
                    const selectedFile = uploadArea.getSelectedFile ? uploadArea.getSelectedFile() : null;
                    if (!selectedFile) return Swal.showValidationMessage('Pilih file atau ambil foto bukti pembayaran baru!');

                    let file = selectedFile;

                    if (file.size > 15 * 1024 * 1024) {
                        return Swal.showValidationMessage('File terlalu besar (Maksimal 15MB)');
                    }

                    try {
                        file = await compressImage(file);
                    } catch (e) {
                        console.error('Gagal kompresi:', e);
                    }

                    if (file.size > 5 * 1024 * 1024) {
                        return Swal.showValidationMessage('File hasil foto/pilihan masih melebihi 5MB, gunakan file lain.');
                    }

                    const formData = new FormData();
                    appendProofFile(formData, 'bukti_pembayaran', file);
                    if (rekeningId) {
                        formData.append('type_pembayaran', rekeningId);
                    }
                    formData.append('_method', 'PUT');

                    return uploadFormDataXhr(url, formData)
                        .catch(err => Swal.showValidationMessage(`Gagal ganti bukti: ${err.message}`));
                }
            }).then(result => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Bukti pembayaran berhasil diganti. Menunggu verifikasi ulang dari admin.',
                        icon: 'success',
                        confirmButtonColor: '#0f172a'
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }

        // Tombol Ganti Bukti di card
        document.querySelectorAll('.ganti-bukti-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                triggerGantiBukti(this.dataset.id, this.dataset.rekeningId);
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
