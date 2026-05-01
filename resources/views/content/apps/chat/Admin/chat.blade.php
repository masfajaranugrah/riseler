@extends('layouts/layoutMaster')

@section('title', 'Chat Admin')

@use('Illuminate\Support\Facades\Auth')

@section('vendor-style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('vendor-script')
    @vite(['resources/js/bootstrap.js', 'resources/js/echo.js'])
@endsection

@section('page-style')
    <style>
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
            height: calc(100vh - 120px);
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin: 20px auto;
            max-width: 1400px;
            border: 1px solid #e5e7eb;
        }

        .users-sidebar {
            width: 392px;
            border-right: 1px solid #dbe4f0;
            display: flex;
            flex-direction: column;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 58%, #eef2ff 100%);
        }

        .sidebar-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: #ffffff;
            padding: 28px 24px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: inset 0 -1px 0 rgba(255, 255, 255, 0.08);
        }

        .admin-avatar {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 12px;
            color: #ffffff;
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.35);
        }

        .admin-info h2 {
            font-size: 20px;
            margin-bottom: 6px;
            font-weight: 600;
            color: #ffffff;
        }

        .admin-status {
            font-size: 13px;
            color: #cbd5e1;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        .search-box {
            padding: 14px 14px 10px;
            background: transparent;
            border-bottom: 1px solid #e2e8f0;
        }

        .tab-switcher {
            display: flex;
            gap: 8px;
            padding: 12px 14px;
            background: transparent;
            border-bottom: 1px solid #e2e8f0;
        }

        .tab-button {
            flex: 1;
            border: 1px solid #dbe4f0;
            background: #ffffff;
            color: #334155;
            font-size: 12px;
            font-weight: 700;
            border-radius: 10px;
            padding: 9px 10px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .tab-button.active {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border-color: #1d4ed8;
            color: #ffffff;
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.24);
        }

        .search-wrapper {
            position: relative;
        }

        .chat-search-input {
            width: 100%;
            padding: 12px 16px 12px 42px;
            border: 1px solid #dbe4f0;
            border-radius: 12px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s;
            background: #ffffff;
            color: #1e293b;
        }

        .chat-search-input::placeholder {
            color: #94a3b8;
        }

        .chat-search-input:focus {
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
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
            padding: 12px 10px 14px;
            background: transparent;
        }

        .user-list::-webkit-scrollbar {
            width: 6px;
        }

        .user-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .user-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .user-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .user-item {
            padding: 13px 12px;
            margin-bottom: 8px;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.2s;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        .user-item:hover {
            background: #f8fbff;
            border-color: #cbd5e1;
            transform: translateX(3px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        }

        .user-item.active {
            background: #eff6ff;
            border-color: #93c5fd;
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.16);
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
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 700;
            font-size: 18px;
            flex-shrink: 0;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.2);
        }

        .user-details {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-weight: 700;
            margin-bottom: 3px;
            font-size: 15px;
            color: #1e293b;
        }

        .user-type {
            font-size: 12px;
            color: #64748b;
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
            box-shadow: none;
            transition: none;
        }

        .chat-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #ffffff;
        }

        .chat-header {
            background: #ffffff;
            color: #1e293b;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e5e7eb;
        }

        .chat-header-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #3b82f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #ffffff;
        }

        .chat-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 2px;
            color: #1e293b;
        }

        .chat-subtitle {
            font-size: 12px;
            color: #10b981;
            font-weight: 500;
        }

        .chat-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background: #eff6ff;
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            background: #f8fafc;
        }

        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
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
            font-weight: 600;
            flex-shrink: 0;
        }

        .message.sent .message-avatar {
            background: #3b82f6;
            order: 2;
        }

        .message.received .message-avatar {
            background: #10b981;
        }

        .message-bubble {
            max-width: 60%;
        }

        .message-content {
            padding: 12px 16px;
            border-radius: 12px;
            word-wrap: break-word;
        }

        .message.sent .message-content {
            background: #3b82f6;
            color: #ffffff;
            border-bottom-right-radius: 4px;
        }

        .message.received .message-content {
            background: #ffffff;
            color: #1e293b;
            border-bottom-left-radius: 4px;
            border: 1px solid #e5e7eb;
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
            gap: 4px;
        }

        .message.sent .message-info {
            justify-content: flex-end;
            color: rgba(255, 255, 255, 0.8);
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

        .message-text-deleted {
            font-style: italic;
            opacity: 0.88;
        }

        .message-edited {
            font-size: 10px;
            opacity: 0.9;
            margin-left: 4px;
        }

        .message-actions {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-left: 8px;
        }

        .message-action-btn {
            width: 22px;
            height: 22px;
            border: none;
            border-radius: 6px;
            background: rgba(148, 163, 184, 0.2);
            color: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .message-action-btn:hover {
            background: rgba(15, 23, 42, 0.2);
            transform: translateY(-1px);
        }

        /* Confirm modal for delete */
        .chat-confirm-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.35);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: fadeIn 0.12s ease;
        }

        .chat-confirm-overlay.hide {
            opacity: 0;
            transition: opacity 0.12s ease;
        }

        .chat-confirm-dialog {
            background: #ffffff;
            border-radius: 14px;
            padding: 20px 22px 18px;
            width: min(360px, 90vw);
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.16);
        }

        .chat-prompt-dialog {
            width: min(440px, 92vw);
        }

        .chat-confirm-dialog h4 {
            margin: 0 0 8px;
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }

        .chat-confirm-dialog p {
            margin: 0 0 16px;
            color: #475569;
            font-size: 13px;
            line-height: 1.5;
        }

        .chat-prompt-input {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            resize: vertical;
            min-height: 82px;
            color: #0f172a;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .chat-prompt-input:focus {
            border-color: #0f172a;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.12);
        }

        .chat-confirm-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .chat-confirm-actions .btn-cancel,
        .chat-confirm-actions .btn-confirm {
            border: none;
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .chat-confirm-actions .btn-cancel {
            background: #e2e8f0;
            color: #0f172a;
        }

        .chat-confirm-actions .btn-confirm {
            background: #0f172a;
            color: #ffffff;
        }

        .chat-confirm-actions .btn-cancel:hover { background: #cbd5e1; }
        .chat-confirm-actions .btn-confirm:hover { background: #111827; }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .chat-input-container {
            padding: 16px 20px;
            background: #ffffff;
            border-top: 1px solid #e5e7eb;
            margin-bottom: 60px;
        }

        .chat-input-form {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }

        .input-wrapper {
            flex: 1;
            position: relative;
            display: flex;
            align-items: flex-end;
        }

        .chat-input {
            width: 100%;
            min-height: 44px;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            line-height: 1.5;
            outline: none;
            transition: all 0.2s;
            background: #f8fafc;
            color: #1e293b;
            resize: none;
            max-height: 120px;
            overflow-y: auto;
        }

        .chat-input::placeholder {
            color: #94a3b8;
        }

        .chat-input:focus {
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .attach-button,
        .location-button {
            width: 44px;
            height: 44px;
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .attach-button:hover,
        .location-button:hover {
            background: #eff6ff;
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .media-preview-container {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .remove-media-btn {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }


        .media-filename {
            font-size: 12px;
            color: #64748b;
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .location-card {
            background: #0f172a;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #1f2937;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.18);
        }

        .location-card .location-img {
            display: block;
            width: 100%;
            max-width: 280px;
            height: auto;
        }

        .location-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            background: #111827;
            color: #e2e8f0;
            font-size: 12px;
        }

        .location-coord {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .location-btn {
            background: #22d3ee;
            color: #0f172a;
            padding: 6px 10px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .location-btn:hover {
            background: #0ea5e9;
            color: #0f172a;
        }

        .send-button {
            width: 44px;
            height: 44px;
            background: #3b82f6;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .send-button:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .send-button:active {
            transform: translateY(0);
        }

        .send-button:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
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
            font-weight: 600;
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
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        /* Monochrome theme override (selaras chat billing) */
        .admin-chat-container {
            background: #ffffff;
            border: 1px solid #d1d5db;
        }

        .users-sidebar {
            background: #020617;
            border-right-color: #1f2937;
        }

        .sidebar-header {
            background: #020617;
            border-bottom-color: #1f2937;
            box-shadow: none;
        }

        .admin-avatar {
            background: linear-gradient(135deg, #111827 0%, #000000 100%);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.35);
        }

        .admin-status {
            color: #9ca3af;
        }

        .search-box,
        .tab-switcher {
            border-bottom-color: #111827;
        }

        .chat-search-input {
            background: #020617;
            color: #f9fafb;
            border-color: #1f2937;
        }

        .chat-search-input::placeholder {
            color: #94a3b8;
        }

        .chat-search-input:focus {
            background: #020617;
            border-color: #334155;
            box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.15);
        }

        .tab-button {
            background: #020617;
            color: #cbd5e1;
            border-color: #1f2937;
        }

        .tab-button.active {
            background: #f8fafc;
            color: #0f172a;
            border-color: #e2e8f0;
            box-shadow: 0 8px 20px rgba(148, 163, 184, 0.18);
        }

        .user-item {
            background: #020617;
            border-color: #1f2937;
            box-shadow: none;
        }

        .user-item:hover {
            background: #0b1220;
            border-color: #334155;
            box-shadow: none;
        }

        .user-item.active {
            background: #111827;
            border-color: #475569;
            box-shadow: none;
        }

        .user-avatar {
            background: linear-gradient(135deg, #111827 0%, #000000 100%);
            box-shadow: none;
        }

        .user-name {
            color: #f8fafc;
        }

        .user-type {
            color: #94a3b8;
        }

        .chat-header {
            background: #ffffff;
        }

        .chat-avatar,
        .message.sent .message-avatar {
            background: #111827;
        }

        .message.sent .message-content,
        .send-button {
            background: #111827;
            color: #ffffff;
        }

        .send-button:hover {
            background: #000000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.28);
        }

        .chat-messages {
            background: #ffffff;
        }

        .chat-input {
            background: #ffffff;
            border-color: #d1d5db;
            color: #111827;
        }

        .chat-input:focus {
            border-color: #111827;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
        }

        .attach-button,
        .location-button {
            background: #ffffff;
            border-color: #d1d5db;
            color: #374151;
        }

        .attach-button:hover,
        .location-button:hover {
            background: #f3f4f6;
            border-color: #111827;
            color: #111827;
        }

        .chat-header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sidebar-toggle-btn {
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #111827;
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
        .admin-chat-container.sidebar-minimized .tab-switcher,
        .admin-chat-container.sidebar-minimized .search-box,
        .admin-chat-container.sidebar-minimized .user-details,
        .admin-chat-container.sidebar-minimized .unread-badge {
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
    </style>
@endsection

@section('content')
    <div class="admin-chat-container">
        <div class="users-sidebar">
            <div class="sidebar-header">
                <div class="admin-info">
                    <div class="admin-avatar">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h2>Admin Panel</h2>
                    <div class="admin-status">
                        <span class="status-dot"></span>
                        <span>Customer Service</span>
                    </div>
                </div>
            </div>

            <div class="search-box">
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="chat-search-input" id="chatSearchInput" placeholder="Cari user..."
                        autocomplete="off">
                </div>
            </div>

            <div class="tab-switcher">
                <button class="tab-button active" data-filter="all" id="tabAll">All Chat</button>
                <button class="tab-button" data-filter="unread" id="tabUnread">Unread</button>
            </div>

            <div class="user-list" id="userList">
                @foreach($users as $user)
                    <div class="user-item" data-user-id="{{ $user['id'] }}" data-user-name="{{ $user['name'] }}">
                        <div class="user-item-content">
                            <div class="user-avatar">
                                {{ strtoupper(substr($user['name'], 0, 1)) }}
                            </div>
                            <div class="user-details">
                                <div class="user-name">{{ $user['name'] }}</div>
                                <div class="user-type">{{ ($user['nomer_id']) }}</div>
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
                        <h1 class="chat-title" id="chatTitle">Pilih user untuk memulai chat</h1>
                        <div class="chat-subtitle" id="chatSubtitle" style="display: none;">? Online</div>
                    </div>
                </div>
                <div class="chat-header-actions">
                    <button type="button" id="sidebarToggleBtn" class="sidebar-toggle-btn" aria-label="Lebarkan chat">
                        <i class="fas fa-expand-alt"></i>
                        <span id="sidebarToggleText">Lebarkan Chat</span>
                    </button>
                    <div class="chat-actions" id="chatActions" style="display: none;">
                        <button class="action-btn" title="Info">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <button class="action-btn" title="More">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="chat-messages" id="chatMessages">
                <div class="no-chat-selected">
                    <i class="fas fa-comments no-chat-icon"></i>
                    <div class="no-chat-text">Selamat Datang, Admin!</div>
                    <div class="no-chat-subtext">Pilih user dari sidebar untuk memulai percakapan</div>
                </div>
            </div>

            <div class="chat-input-container" id="chatInputContainer" style="display: none;">
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
                    <button type="button" class="location-button" id="locationButton" title="Kirim lokasi">
                        <i class="fas fa-location-arrow"></i>
                    </button>
                    <div class="input-wrapper">
                        <textarea class="chat-input" id="messageInput" placeholder="Tulis pesan Anda..."
                            autocomplete="off" rows="1"></textarea>
                    </div>
                    <button type="submit" class="send-button" id="sendButton">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
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

        (function () {
            const container = document.querySelector('.admin-chat-container');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const toggleText = document.getElementById('sidebarToggleText');
            const storageKey = 'adminCSSidebarMinimized';

            if (!container || !toggleBtn) return;

            const applyState = (isMinimized) => {
                container.classList.toggle('sidebar-minimized', isMinimized);
                if (toggleText) {
                    toggleText.textContent = isMinimized ? 'Tampilkan Sidebar' : 'Lebarkan Chat';
                }
                const icon = toggleBtn.querySelector('i');
                if (icon) {
                    icon.className = isMinimized ? 'fas fa-compress-alt' : 'fas fa-expand-alt';
                }
            };

            let isMinimized = false;
            try {
                isMinimized = localStorage.getItem(storageKey) === '1';
            } catch (_) {}

            applyState(isMinimized);

            toggleBtn.addEventListener('click', function () {
                isMinimized = !container.classList.contains('sidebar-minimized');
                applyState(isMinimized);
                try {
                    localStorage.setItem(storageKey, isMinimized ? '1' : '0');
                } catch (_) {}
            });
        })();

    </script>
    @vite(['resources/js/chat.js'])
@endsection
