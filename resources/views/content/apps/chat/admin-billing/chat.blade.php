@extends('layouts/layoutMaster')

@section('title', 'Chat Pembayaran - Admin')

@use('Illuminate\Support\Facades\Auth')

@section('vendor-style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('vendor-script')
    @vite(['resources/js/bootstrap.js', 'resources/js/echo.js'])
@endsection

@section('page-style')
    <style>
        :root {
            --bg: #0b1020;
            --ink: #0f172a;
            --muted: #94a3b8;
            --card: #0f172a;
            --accent: #22c55e;
            --accent-dark: #16a34a;
            --surface: #0c111c;
            --pill: #e2e8f0;
            --pill-active: #0f172a;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .admin-chat-container {
            display: flex;
            width: 100%;
            height: calc(100vh - 140px);
            background: #f8fafc;
            border-radius: 16px;
            overflow: hidden;
            margin: 0 auto;
            max-width: 1400px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
        }

        .users-sidebar {
            width: 410px;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            background: linear-gradient(180deg, #0b1020 0%, #111b32 52%, #0f172a 100%);
        }

        .tab-switcher {
            display: flex;
            gap: 8px;
            padding: 12px 14px 0 14px;
            background: transparent;
        }

        .tab-button {
            flex: 1;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.06);
            color: #e2e8f0;
            font-weight: 700;
            border-radius: 11px;
            padding: 10px 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .tab-button.active {
            background: #22c55e;
            color: #0b1020;
            border-color: #22c55e;
            box-shadow: 0 12px 28px rgba(34, 197, 94, 0.28);
        }

        .pill-toggle {
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.08);
            color: #e2e8f0;
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .pill-toggle:hover {
            background: rgba(255, 255, 255, 0.14);
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.08);
            color: #cbd5e1;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
        }

        .card-glass {
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 18px;
            padding: 16px;
            color: #e2e8f0;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.28);
            position: relative;
            overflow: hidden;
        }

        .card-glass::before {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: 18px;
            background: linear-gradient(125deg, rgba(34, 197, 94, 0.26), rgba(14, 165, 233, 0.16), transparent 45%);
            z-index: 0;
            pointer-events: none;
        }

        .broadcast-form {
            position: relative;
            z-index: 1;
        }

        .broadcast-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .broadcast-title {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 18px;
            font-weight: 800;
            color: #f8fafc;
        }

        .broadcast-title i {
            color: #22c55e;
        }

        .broadcast-field {
            margin-top: 10px;
        }

        .broadcast-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #94a3b8;
            margin-bottom: 6px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 13px;
            pointer-events: none;
            z-index: 2;
        }

        .input-wrap .input-compact {
            padding-left: 42px !important;
            min-height: 44px;
            line-height: 1.3;
        }

        .input-wrap select.input-compact {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        .input-compact {
            width: 100%;
            background: #0b1020;
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            padding: 11px 12px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
        }

        .input-compact:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.16);
        }

        .btn-inline {
            width: 100%;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
            font-weight: 800;
            font-size: 16px;
            padding: 12px 14px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 12px 24px rgba(22, 163, 74, 0.3);
        }

        .btn-inline:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 30px rgba(22, 163, 74, 0.38);
        }

        .btn-inline:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .inline-progress {
            margin-top: 10px;
        }

        .inline-progress-head {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #cbd5e1;
            margin-bottom: 6px;
        }

        .inline-progress-track {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 99px;
            overflow: hidden;
        }

        .inline-progress-bar {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #22c55e, #0ea5e9);
            transition: width 0.2s ease;
        }


        .sidebar-header {
            color: #ffffff;
            padding: 26px 22px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .admin-avatar {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: linear-gradient(135deg, #22c55e, #0ea5e9);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 14px;
            color: #ffffff;
            box-shadow: 0 12px 30px rgba(14, 165, 233, 0.3);
        }

        .admin-info h2 {
            font-size: 20px;
            margin-bottom: 6px;
            font-weight: 700;
            color: #ffffff;
        }

        .admin-status {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
            box-shadow: 0 0 0 6px rgba(34, 197, 94, 0.2);
        }

        .search-box {
            padding: 14px 14px 12px;
            background: transparent;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .search-wrapper {
            position: relative;
        }

        .chat-search-input {
            width: 100%;
            padding: 12px 16px 12px 42px;
            border: 1px solid rgba(255,255,255,0.14);
            border-radius: 12px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s;
            background: rgba(15,23,42,0.62);
            color: #e2e8f0;
        }

        .chat-search-input::placeholder {
            color: #94a3b8;
        }

        .chat-search-input:focus {
            border-color: #22c55e;
            background: rgba(15,23,42,0.9);
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .user-list {
            flex: 1;
            overflow-y: auto;
            padding: 12px 10px 18px;
            background: transparent;
        }

        .user-list::-webkit-scrollbar {
            width: 6px;
        }

        .user-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .user-list::-webkit-scrollbar-thumb {
            background: #1f2937;
            border-radius: 10px;
        }

        .user-item {
            padding: 13px 12px;
            margin-bottom: 10px;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.2s;
            background: rgba(15, 23, 42, 0.78);
            border: 1px solid rgba(148, 163, 184, 0.2);
            box-shadow: 0 8px 20px rgba(2, 6, 23, 0.24);
        }

        .user-item:hover {
            background: rgba(15, 23, 42, 0.96);
            transform: translateY(-1px);
            border-color: rgba(14, 165, 233, 0.42);
            box-shadow: 0 14px 28px rgba(2, 6, 23, 0.34);
        }

        .user-item.active {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            border-color: #38bdf8;
            box-shadow: 0 14px 32px rgba(14, 165, 233, 0.34);
            color: #ffffff;
        }

        .user-item-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #22c55e, #0ea5e9);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 700;
            font-size: 18px;
            flex-shrink: 0;
            box-shadow: 0 10px 24px rgba(14, 165, 233, 0.25);
        }

        .user-details {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-weight: 700;
            margin-bottom: 3px;
            font-size: 15px;
            color: #e2e8f0;
        }

        .user-type {
            font-size: 12px;
            color: #cbd5e1;
        }

        .unread-badge {
            background: #ef4444;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            line-height: 1;
            padding: 5px 7px;
            border-radius: 999px;
            min-width: 22px;
            text-align: center;
            flex-shrink: 0;
            margin-left: 6px;
            box-shadow: 0 8px 18px rgba(239, 68, 68, 0.3);
        }

        .user-item.pinned {
            border-color: rgba(255, 255, 255, 0.65) !important;
        }

        .chat-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #f8fafc;
        }

        .chat-header {
            background: #ffffff;
            color: #0f172a;
            padding: 22px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
        }

        .chat-header-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #0ea5e9;
            display: flex;
            align-items: center;
            justify-contents: center;
            justify-content: center;
            font-size: 18px;
            color: #ffffff;
        }

        .chat-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 2px;
            color: #0f172a;
        }

        .chat-subtitle {
            font-size: 12px;
            color: #71717a;
            font-weight: 500;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 24px 28px;
            background: #eef2f7;
        }

        .message {
            margin-bottom: 16px;
            display: flex;
            align-items: flex-end;
            gap: 10px;
            animation: slideIn 0.2s ease;
        }

        .message.sent {
            justify-content: flex-end;
        }

        .message.received {
            justify-content: flex-start;
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #ffffff;
            font-weight: 700;
            flex-shrink: 0;
        }

        .message.sent .message-avatar {
            background: #0ea5e9;
            order: 2;
        }

        .message.received .message-avatar {
            background: #64748b;
        }

        .message-bubble {
            max-width: 60%;
        }

        .message-content {
            padding: 12px 16px;
            border-radius: 14px;
            word-wrap: break-word;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }

        .message.sent .message-content {
            background: #0ea5e9;
            color: #ffffff;
            border-bottom-right-radius: 6px;
        }

        .message.received .message-content {
            background: #ffffff;
            color: #0f172a;
            border-bottom-left-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .message-text {
            font-size: 14px;
            line-height: 1.5;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .message-info {
            font-size: 11px;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .message.sent .message-info {
            justify-content: flex-end;
            color: rgba(255, 255, 255, 0.85);
        }

        .message.received .message-info {
            color: #94a3b8;
        }

        .message-status-wrap {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-weight: 600;
        }

        .message.sent .message-status,
        .message.sent .message-status-text {
            color: rgba(255, 255, 255, 0.9);
        }

        .message.sent .message-status.read,
        .message.sent .message-status-text.read {
            color: #ffffff;
        }

        .message.sent .message-status-text.pending {
            color: rgba(255, 255, 255, 0.85);
        }

        .message-edited {
            opacity: 0.8;
            font-style: italic;
        }

        .message-text-deleted {
            font-style: italic;
            opacity: 0.86;
        }

        .message-actions {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-left: 4px;
        }

        .message-action-btn {
            border: none;
            background: rgba(255, 255, 255, 0.16);
            color: inherit;
            border-radius: 999px;
            width: 22px;
            height: 22px;
            font-size: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .message-action-btn:hover {
            background: rgba(255, 255, 255, 0.28);
        }

        .message-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(2, 6, 23, 0.45);
            backdrop-filter: blur(7px);
            -webkit-backdrop-filter: blur(7px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 3000;
        }

        .message-modal-backdrop.show {
            display: flex;
        }

        .message-modal {
            width: min(520px, 100%);
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 26px 60px rgba(2, 6, 23, 0.22);
            overflow: hidden;
        }

        .message-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 18px 12px 18px;
            border-bottom: 1px solid #e2e8f0;
        }

        .message-modal-title {
            margin: 0;
            color: #0f172a;
            font-size: 18px;
            font-weight: 800;
        }

        .message-modal-close {
            border: none;
            background: #f1f5f9;
            color: #334155;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .message-modal-close:hover {
            background: #e2e8f0;
        }

        .message-modal-body {
            padding: 16px 18px;
        }

        .message-modal-textarea {
            width: 100%;
            min-height: 120px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            color: #0f172a;
            border-radius: 12px;
            padding: 12px 14px;
            outline: none;
            resize: vertical;
            font-size: 14px;
            line-height: 1.5;
        }

        .message-modal-textarea:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        }

        .message-modal-hint {
            margin-top: 8px;
            color: #64748b;
            font-size: 12px;
        }

        .message-modal-alert {
            margin-top: 8px;
            color: #dc2626;
            font-size: 12px;
            font-weight: 600;
            display: none;
        }

        .message-modal-footer {
            padding: 12px 18px 18px 18px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .message-modal-btn {
            border: 1px solid transparent;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .message-modal-btn.secondary {
            background: #f1f5f9;
            color: #334155;
            border-color: #cbd5e1;
        }

        .message-modal-btn.secondary:hover {
            background: #e2e8f0;
        }

        .message-modal-btn.primary {
            background: #0ea5e9;
            color: #ffffff;
        }

        .message-modal-btn.primary:hover {
            background: #0284c7;
        }

        .message-modal-btn.danger {
            background: #ef4444;
            color: #ffffff;
        }

        .message-modal-btn.danger:hover {
            background: #dc2626;
        }

        .message-modal-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .chat-input-container {
            padding: 16px 20px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            margin-bottom: 0;
        }

        .quick-replies {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }

        .quick-reply-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 700;
            margin-right: 2px;
        }

        .quick-reply-chip {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            color: #0f172a;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .quick-reply-chip:hover {
            background: #e2e8f0;
            border-color: #94a3b8;
        }

        .chat-input-form {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }

        .input-wrapper {
            flex: 1;
            position: relative;
        }

        .chat-input {
            width: 100%;
            padding: 12px 50px 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
            background: #f8fafc;
            color: #0f172a;
            resize: none;
            max-height: 100px;
        }

        .chat-input::placeholder {
            color: #94a3b8;
        }

        .chat-input:focus {
            border-color: #0ea5e9;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.18);
        }

        .attach-button,
        .send-button {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .attach-button {
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        .attach-button:hover {
            background: #e2e8f0;
            border-color: #0ea5e9;
            color: #0ea5e9;
        }

        .send-button {
            background: #0ea5e9;
            color: #ffffff;
            border: none;
        }

        .send-button:hover {
            background: #0284c7;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(2, 132, 199, 0.3);
        }

        .no-chat-selected {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            gap: 16px;
        }

        .no-chat-icon {
            font-size: 64px;
            color: #cbd5e1;
        }

        .no-chat-text {
            color: #475569;
            font-size: 18px;
            font-weight: 700;
        }

        .no-chat-subtext {
            color: #94a3b8;
            font-size: 14px;
        }

        .date-divider {
            text-align: center;
            margin: 20px 0;
        }

        .date-text {
            display: inline-block;
            padding: 6px 14px;
            background: #e2e8f0;
            color: #64748b;
            font-size: 12.5px;
            border-radius: 8px;
        }

        /* Monochrome override: replace blue/green accents with black-white */
        .admin-chat-container {
            background: #090909 !important;
            border: 1px solid #202020 !important;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.5) !important;
        }

        .users-sidebar {
            background: linear-gradient(180deg, #020202 0%, #0a0a0a 52%, #141414 100%) !important;
            border-right: 1px solid #202020 !important;
        }

        .tab-button {
            border-color: rgba(255, 255, 255, 0.14) !important;
            background: rgba(255, 255, 255, 0.05) !important;
            color: #f3f4f6 !important;
        }

        .tab-button.active {
            background: #ffffff !important;
            color: #000000 !important;
            border-color: #ffffff !important;
            box-shadow: 0 10px 24px rgba(255, 255, 255, 0.18) !important;
        }

        .card-glass {
            background: rgba(8, 8, 8, 0.9) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        .card-glass::before {
            background: linear-gradient(125deg, rgba(255, 255, 255, 0.18), rgba(255, 255, 255, 0.06), transparent 45%) !important;
        }

        .broadcast-title i {
            color: #ffffff !important;
        }

        .input-compact {
            background: #0a0a0a !important;
            border-color: rgba(255, 255, 255, 0.16) !important;
            color: #f3f4f6 !important;
        }

        .input-compact:focus {
            border-color: #737373 !important;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.14) !important;
        }

        .btn-inline {
            background: linear-gradient(135deg, #171717, #000000) !important;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.55) !important;
        }

        .btn-inline:hover {
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.62) !important;
        }

        .inline-progress-bar {
            background: linear-gradient(90deg, #ffffff, #9ca3af) !important;
        }

        .admin-avatar {
            background: linear-gradient(135deg, #1a1a1a, #000000) !important;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.45) !important;
        }

        .status-dot {
            background: #ffffff !important;
            box-shadow: 0 0 0 6px rgba(255, 255, 255, 0.14) !important;
        }

        .chat-search-input {
            border-color: rgba(255, 255, 255, 0.16) !important;
            background: rgba(8, 8, 8, 0.72) !important;
            color: #f3f4f6 !important;
        }

        .chat-search-input:focus {
            border-color: #737373 !important;
            background: rgba(8, 8, 8, 0.92) !important;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.12) !important;
        }

        .user-item {
            background: rgba(13, 13, 13, 0.9) !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.38) !important;
        }

        .user-item:hover {
            background: rgba(18, 18, 18, 0.95) !important;
            border-color: rgba(255, 255, 255, 0.34) !important;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.5) !important;
        }

        .user-item.active {
            background: linear-gradient(135deg, #000000, #161616) !important;
            border-color: #ffffff !important;
            box-shadow: 0 14px 32px rgba(0, 0, 0, 0.55) !important;
            color: #ffffff !important;
        }

        .user-avatar {
            background: linear-gradient(135deg, #202020, #000000) !important;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.45) !important;
        }

        .unread-badge {
            background: #ffffff !important;
            color: #000000 !important;
            box-shadow: 0 8px 18px rgba(255, 255, 255, 0.2) !important;
        }

        .chat-section {
            background: #0b0b0b !important;
        }

        .chat-header {
            background: #111111 !important;
            color: #f8fafc !important;
            border-bottom: 1px solid #262626 !important;
        }

        .chat-avatar {
            background: #000000 !important;
            border: 1px solid #3f3f46;
            color: #ffffff !important;
        }

        .chat-title {
            color: #f8fafc !important;
        }

        .chat-subtitle {
            color: #a1a1aa !important;
        }

        .chat-messages {
            background: #151515 !important;
        }

        .message.sent .message-avatar {
            background: #000000 !important;
        }

        .message.sent .message-content {
            background: #000000 !important;
            color: #ffffff !important;
        }

        .message.received .message-content {
            background: #1a1a1a !important;
            color: #f3f4f6 !important;
            border-color: #2e2e2e !important;
        }

        .message.received .message-info {
            color: #9ca3af !important;
        }

        .message-modal {
            background: #111111 !important;
            border-color: #27272a !important;
        }

        .message-modal-header {
            border-bottom: 1px solid #27272a !important;
        }

        .message-modal-title {
            color: #f8fafc !important;
        }

        .message-modal-close {
            background: #1f2937 !important;
            color: #e5e7eb !important;
        }

        .message-modal-close:hover {
            background: #374151 !important;
        }

        .message-modal-textarea {
            background: #0f0f0f !important;
            color: #f8fafc !important;
            border-color: #3f3f46 !important;
        }

        .message-modal-textarea:focus {
            border-color: #737373 !important;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.12) !important;
        }

        .message-modal-hint {
            color: #a1a1aa !important;
        }

        .message-modal-btn.secondary {
            background: #1f2937 !important;
            color: #e5e7eb !important;
            border-color: #3f3f46 !important;
        }

        .message-modal-btn.secondary:hover {
            background: #374151 !important;
        }

        .message-modal-btn.primary {
            background: #000000 !important;
            color: #ffffff !important;
        }

        .message-modal-btn.primary:hover {
            background: #1a1a1a !important;
        }

        .chat-input-container {
            background: #111111 !important;
            border-top: 1px solid #262626 !important;
        }

        .quick-reply-label {
            color: #9ca3af !important;
        }

        .quick-reply-chip {
            background: #1a1a1a !important;
            color: #e5e7eb !important;
            border-color: #3f3f46 !important;
        }

        .quick-reply-chip:hover {
            background: #27272a !important;
            border-color: #737373 !important;
        }

        .chat-input {
            background: #0f0f0f !important;
            border-color: #2d2d2d !important;
            color: #f8fafc !important;
        }

        .chat-input:focus {
            background: #111111 !important;
            border-color: #737373 !important;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.12) !important;
        }

        .attach-button {
            background: #1a1a1a !important;
            color: #e5e7eb !important;
            border-color: #3f3f46 !important;
        }

        .attach-button:hover {
            background: #27272a !important;
            border-color: #737373 !important;
            color: #ffffff !important;
        }

        .send-button {
            background: #000000 !important;
            color: #ffffff !important;
        }

        .send-button:hover {
            background: #1a1a1a !important;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.42) !important;
        }

        .no-chat-text {
            color: #f3f4f6 !important;
        }

        .no-chat-subtext {
            color: #a1a1aa !important;
        }

        .date-text {
            background: #1f2937 !important;
            color: #d1d5db !important;
        }

        /* Requested layout: chat area white + black text, sidebar stays black */
        .chat-section {
            background: #ffffff !important;
        }

        .chat-header {
            background: #ffffff !important;
            color: #111111 !important;
            border-bottom: 1px solid #e5e7eb !important;
        }

        .chat-title {
            color: #111111 !important;
        }

        .chat-subtitle {
            color: #4b5563 !important;
        }

        .chat-messages {
            background: #ffffff !important;
        }

        .message.sent .message-content,
        .message.received .message-content {
            background: #ffffff !important;
            color: #111111 !important;
            border: 1px solid #e5e7eb !important;
            box-shadow: 0 4px 14px rgba(17, 24, 39, 0.08) !important;
        }

        .message.sent .message-info,
        .message.received .message-info {
            color: #6b7280 !important;
        }

        .message.sent .message-status,
        .message.sent .message-status-text,
        .message.sent .message-status.read,
        .message.sent .message-status-text.read,
        .message.sent .message-status-text.pending {
            color: #4b5563 !important;
        }

        .message-action-btn {
            background: #f3f4f6 !important;
            color: #111111 !important;
        }

        .message-action-btn:hover {
            background: #e5e7eb !important;
        }

        .chat-input-container {
            background: #ffffff !important;
            border-top: 1px solid #e5e7eb !important;
        }

        .chat-input {
            background: #ffffff !important;
            color: #111111 !important;
            border-color: #d1d5db !important;
        }

        .chat-input::placeholder {
            color: #9ca3af !important;
        }

        .chat-input:focus {
            background: #ffffff !important;
            border-color: #6b7280 !important;
            box-shadow: 0 0 0 3px rgba(107, 114, 128, 0.16) !important;
        }

        .attach-button {
            background: #ffffff !important;
            color: #111111 !important;
            border-color: #d1d5db !important;
        }

        .attach-button:hover {
            background: #f3f4f6 !important;
            border-color: #9ca3af !important;
            color: #111111 !important;
        }

        .no-chat-text {
            color: #111111 !important;
        }

        .no-chat-subtext {
            color: #6b7280 !important;
        }

        .chat-header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sidebar-toggle-btn {
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #111111;
            border-radius: 10px;
            height: 38px;
            padding: 0 12px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .sidebar-toggle-btn:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
        }

        .admin-chat-container.sidebar-minimized .users-sidebar {
            width: 96px !important;
            min-width: 96px !important;
        }

        .admin-chat-container.sidebar-minimized .sidebar-header {
            padding: 14px 10px !important;
        }

        .admin-chat-container.sidebar-minimized .sidebar-header h2,
        .admin-chat-container.sidebar-minimized .admin-status,
        .admin-chat-container.sidebar-minimized #toggleBroadcast,
        .admin-chat-container.sidebar-minimized #broadcastPanel,
        .admin-chat-container.sidebar-minimized .tab-switcher,
        .admin-chat-container.sidebar-minimized .search-box,
        .admin-chat-container.sidebar-minimized .user-details,
        .admin-chat-container.sidebar-minimized .unread-badge,
        .admin-chat-container.sidebar-minimized .chat-actions {
            display: none !important;
        }

        .admin-chat-container.sidebar-minimized .admin-avatar {
            margin: 0 auto !important;
            width: 46px !important;
            height: 46px !important;
            border-radius: 12px !important;
            font-size: 20px !important;
        }

        .admin-chat-container.sidebar-minimized .user-list {
            padding: 8px 8px 12px !important;
        }

        .admin-chat-container.sidebar-minimized .user-item {
            padding: 8px !important;
            margin-bottom: 8px !important;
        }

        .admin-chat-container.sidebar-minimized .user-item-content {
            justify-content: center;
        }

        .admin-chat-container.sidebar-minimized .user-avatar {
            width: 40px !important;
            height: 40px !important;
            margin: 0 !important;
            font-size: 16px !important;
        }

        .admin-chat-container.sidebar-minimized .chat-section {
            flex: 1;
        }

        /* Performance tune: reduce expensive visual effects */
        .admin-chat-container,
        .user-item,
        .user-item:hover,
        .user-item.active,
        .message-content,
        .send-button:hover,
        .btn-inline,
        .btn-inline:hover,
        .admin-avatar,
        .user-avatar {
            box-shadow: none !important;
        }

        .user-item,
        .user-item:hover,
        .user-item.active,
        .send-button:hover,
        .btn-inline:hover {
            transform: none !important;
        }

        .user-item,
        .user-avatar,
        .tab-button,
        .send-button,
        .attach-button,
        .quick-reply-chip {
            transition: none !important;
        }

        .users-sidebar {
            background-image: none !important;
            background-color: #0a0a0a !important;
        }

        .status-dot {
            animation: none !important;
        }

        /* Broadcast form layout fix (prevent clipping/overlap) */
        .users-sidebar {
            min-height: 0;
        }

        .sidebar-header,
        #broadcastPanel,
        .tab-switcher,
        .search-box {
            flex-shrink: 0;
        }

        #broadcastPanel {
            margin: 0 14px 12px 14px !important;
            padding: 14px !important;
            overflow: visible !important;
        }

        #bcFormSidebar {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        #bcFormSidebar .broadcast-field {
            margin-top: 0;
        }

        #bcMessageSidebar {
            min-height: 92px;
            height: 92px !important;
            max-height: 150px;
            resize: vertical;
        }

        #bcSendSidebar {
            margin-top: 2px !important;
            min-height: 44px;
            line-height: 1.2;
            white-space: normal;
        }

        .user-list {
            min-height: 0 !important;
            overflow-y: auto;
        }
    </style>
@endsection

@section('content')
    <div class="admin-chat-container">
        <div class="users-sidebar">
            <div class="sidebar-header">
                <div class="admin-info">
                    <div class="admin-avatar">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <h2 style="margin:0;">Chat Pembayaran</h2>
                        <button id="toggleBroadcast" class="pill-toggle" aria-label="Sembunyikan/lihat broadcast">Toggle</button>
                    </div>
                    <div class="admin-status">
                        <span class="status-dot"></span>
                        <span>Admin Billing</span>
                    </div>
                </div>
            </div>

            <div id="broadcastPanel" class="card-glass" style="margin:0 16px 14px 16px;">
                <form id="bcFormSidebar" class="broadcast-form">
                    @csrf
                    <div class="broadcast-head">
                        <div class="broadcast-title">
                            <i class="fas fa-bullhorn"></i>
                            <span>Broadcast</span>
                        </div>
                        <span id="bcStatusSidebar" class="pill" style="display:none;"></span>
                    </div>

                    <div class="broadcast-field">
                        <label class="broadcast-label" for="bcTypeSidebar">Jenis Pesan</label>
                        <div class="input-wrap">
                            <i class="fas fa-layer-group"></i>
                            <select id="bcTypeSidebar" class="input-compact">
                                <option value="greeting">Salam (pagi/siang/sore/malam)</option>
                                <option value="quote">Kata Bijak</option>
                                <option value="billing">Pengingat Tagihan</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                    </div>

                    <div class="broadcast-field">
                        <label class="broadcast-label" for="bcVariantSidebar">Waktu Salam</label>
                        <div class="input-wrap">
                            <i class="fas fa-sun"></i>
                            <select id="bcVariantSidebar" class="input-compact">
                                <option value="pagi">Pagi</option>
                                <option value="siang">Siang</option>
                                <option value="sore">Sore</option>
                                <option value="malam">Malam</option>
                            </select>
                        </div>
                    </div>

                    <div class="broadcast-field">
                        <label class="broadcast-label" for="bcMessageSidebar">Isi Pesan</label>
                        <textarea id="bcMessageSidebar" class="input-compact" placeholder="Isi pesan (opsional kecuali custom)"></textarea>
                    </div>

                    <button type="submit" class="btn-inline" id="bcSendSidebar">
                        <i class="fas fa-paper-plane"></i> Kirim ke semua pelanggan
                    </button>
                    <div class="inline-progress" id="bcProgressSidebar" style="display:none;">
                        <div class="inline-progress-head">
                            <span id="bcProgressTextSidebar">Menunggu...</span>
                            <span id="bcProgressPctSidebar">0%</span>
                        </div>
                        <div class="inline-progress-track">
                            <div class="inline-progress-bar" id="bcProgressBarSidebar"></div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="tab-switcher">
                <button class="tab-button active" data-filter="all" id="tabAll">All Chat</button>
                <button class="tab-button" data-filter="unread" id="tabUnread">Unread</button>
            </div>

            <div class="search-box">
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="chat-search-input" id="chatSearchInput" placeholder="Cari pelanggan..."
                        autocomplete="off">
                </div>
            </div>

            <div class="user-list" id="userList">
                @foreach($users as $user)
                    <div class="user-item" data-user-id="{{ $user['id'] }}" data-user-name="{{ $user['name'] }}" title="{{ $user['name'] }}">
                        <div class="user-item-content">
                            <div class="user-avatar">
                                {{ strtoupper(substr($user['name'], 0, 1)) }}
                            </div>
                            <div class="user-details">
                                <div class="user-name">{{ $user['name'] }}</div>
                                <div class="user-type">{{ $user['nomer_id'] }}</div>
                            </div>
                            <span class="unread-badge" id="unread-{{ $user['id'] }}" style="display: none;">0</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="chat-section">
            <div class="chat-header">
                <div class="chat-header-info">
                    <div class="chat-avatar" id="chatAvatar" style="display: none;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h1 class="chat-title" id="chatTitle">Pilih pelanggan untuk memulai chat</h1>
                        <div class="chat-subtitle" id="chatSubtitle" style="display: none;">Pertanyaan Pembayaran</div>
                    </div>
                </div>
                <div class="chat-header-actions">
                    <button type="button" id="sidebarToggleBtn" class="sidebar-toggle-btn" aria-label="Lebarkan chat">
                        <i class="fas fa-expand-alt"></i>
                        <span id="sidebarToggleText">Lebarkan Chat</span>
                    </button>
                </div>
            </div>

            <div class="chat-messages" id="chatMessages">
                <div class="no-chat-selected">
                    <i class="fas fa-comments-dollar no-chat-icon"></i>
                    <div class="no-chat-text">Chat Pembayaran</div>
                    <div class="no-chat-subtext">Pilih pelanggan dari sidebar untuk memulai percakapan tentang pembayaran
                    </div>
                </div>
            </div>

            <div class="chat-input-container" id="chatInputContainer" style="display: none;">
                <div class="quick-replies" id="quickReplies" style="display: none;">
                    <span class="quick-reply-label">Balasan cepat:</span>
                    <button type="button" class="quick-reply-chip" data-reply-index="0" title="Alt+1">Tagihan terbit (Alt+1)</button>
                    <button type="button" class="quick-reply-chip" data-reply-index="1" title="Alt+2">Kirim bukti bayar (Alt+2)</button>
                    <button type="button" class="quick-reply-chip" data-reply-index="2" title="Alt+3">Konfirmasi diproses (Alt+3)</button>
                    <button type="button" class="quick-reply-chip" data-reply-index="3" title="Alt+4">Terima kasih (Alt+4)</button>
                    <button type="button" class="quick-reply-chip" data-reply-index="4" title="Alt+5">Hubungi CS (Alt+5)</button>
                </div>
                <div id="mediaPreview"
                    style="display: none; padding: 8px 12px; background: #f1f5f9; border-radius: 8px; margin-bottom: 8px;">
                </div>
                <form class="chat-input-form" id="chatForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="receiverId" name="receiver_id">
                    <input type="file" id="mediaInput" accept="image/*,video/*" style="display: none;">
                    <button type="button" class="attach-button" id="attachButton" title="Kirim foto/video">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <div class="input-wrapper">
                        <textarea class="chat-input" id="messageInput" rows="2"
                            placeholder="Tulis pesan tentang pembayaran... (Enter untuk baris baru)"></textarea>
                    </div>
                    <button type="submit" class="send-button" id="sendButton">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="editMessageModal" class="message-modal-backdrop" aria-hidden="true">
        <div class="message-modal" role="dialog" aria-modal="true" aria-labelledby="editMessageModalTitle">
            <div class="message-modal-header">
                <h3 class="message-modal-title" id="editMessageModalTitle">Edit pesan</h3>
                <button type="button" class="message-modal-close" id="editMessageModalClose" aria-label="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editMessageForm">
                <div class="message-modal-body">
                    <textarea id="editMessageInput" class="message-modal-textarea" maxlength="5000"
                        placeholder="Tulis perubahan pesan..."></textarea>
                    <div class="message-modal-hint">Perubahan akan langsung terlihat tanpa reload halaman.</div>
                    <div class="message-modal-alert" id="editMessageError"></div>
                </div>
                <div class="message-modal-footer">
                    <button type="button" class="message-modal-btn secondary" id="editMessageCancel">Batal</button>
                    <button type="submit" class="message-modal-btn primary" id="editMessageSave">Simpan perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteMessageModal" class="message-modal-backdrop" aria-hidden="true">
        <div class="message-modal" role="dialog" aria-modal="true" aria-labelledby="deleteMessageModalTitle">
            <div class="message-modal-header">
                <h3 class="message-modal-title" id="deleteMessageModalTitle">Hapus pesan</h3>
                <button type="button" class="message-modal-close" id="deleteMessageModalClose" aria-label="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="message-modal-body">
                Pesan yang dihapus akan langsung berubah realtime di semua chat terkait.
            </div>
            <div class="message-modal-footer">
                <button type="button" class="message-modal-btn secondary" id="deleteMessageCancel">Batal</button>
                <button type="button" class="message-modal-btn danger" id="deleteMessageConfirm">Hapus pesan</button>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        window.userId = "{{ Auth::id() }}";
        window.userName = "{{ Auth::user()->name }}";
        window.isAdmin = true;
        window.selectedUserId = null;
        window.chatType = 'admin'; // KEY: This tells JS to use admin-chat endpoints
    </script>
    @vite(['resources/js/admin-chat.js'])
@endsection
