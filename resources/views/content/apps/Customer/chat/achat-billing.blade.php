@use('Illuminate\Support\Facades\Auth')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover, interactive-widget=resizes-visual">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat Admin (Pembayaran)</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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

        html {
            height: 100%;
            overflow: auto;
            scroll-behavior: smooth;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: env(safe-area-inset-top, 20px) env(safe-area-inset-right, 10px) env(safe-area-inset-bottom, 20px) env(safe-area-inset-left, 10px);
        }

        .chat-container {
            width: 100%;
            max-width: 600px;
            height: 60vh;
            max-height: 600px;
            min-height: 400px;
            display: flex;
            flex-direction: column;
            background: #064e3b;
            overflow: hidden;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        .chat-header {
            background: #047857;
            color: #ecfdf5;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #059669;
            flex-shrink: 0;
            min-height: 70px;
            z-index: 10;
            border-radius: 16px 16px 0 0;
        }

        .header-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #ffffff;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .header-info {
            flex: 1;
        }

        .header-info h1 {
            font-size: 17px;
            margin-bottom: 4px;
            font-weight: 600;
            color: #ecfdf5;
        }

        .user-status {
            font-size: 13px;
            color: #a7f3d0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            background: #34d399;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 16px 12px;
            padding-bottom: 80px;
            background-color: #064e3b;
            position: relative;
            -webkit-overflow-scrolling: touch;
        }

        .message {
            margin-bottom: 10px;
            display: flex;
            align-items: flex-end;
            animation: slideIn 0.3s ease-out;
        }

        .message.sent {
            justify-content: flex-end;
        }

        .message.received {
            justify-content: flex-start;
        }

        .message-bubble {
            max-width: 75%;
            position: relative;
        }

        .message-content {
            padding: 8px 10px 10px 12px;
            border-radius: 10px;
            word-wrap: break-word;
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .message.sent .message-content {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ecfdf5;
            border-radius: 10px;
            border-top-right-radius: 2px;
        }

        .message.received .message-content {
            background: #065f46;
            color: #ecfdf5;
            border-radius: 10px;
            border-top-left-radius: 2px;
        }

        .message-text {
            font-size: 14.5px;
            line-height: 20px;
            padding-right: 50px;
        }

        .message-info {
            font-size: 11px;
            color: #a7f3d0;
            position: absolute;
            right: 10px;
            bottom: 5px;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .message.sent .message-info {
            color: rgba(236, 253, 245, 0.7);
        }

        .chat-input-container {
            padding: 10px 12px;
            padding-bottom: calc(10px + env(safe-area-inset-bottom, 0px));
            background: #047857;
            border-top: 1px solid #059669;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            border-radius: 0 0 16px 16px;
        }

        .chat-input-form {
            display: flex;
            gap: 8px;
            align-items: flex-end;
        }

        .input-wrapper {
            flex: 1;
            max-width: calc(100% - 54px);
            position: relative;
            background: #065f46;
            border-radius: 24px;
            display: flex;
            align-items: center;
            padding: 0 8px;
            min-height: 42px;
        }

        .emoji-button {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 6px;
            color: #a7f3d0;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .chat-input {
            flex: 1;
            padding: 10px 8px;
            border: none;
            background: transparent;
            font-size: 14.5px;
            outline: none;
            color: #ecfdf5;
            font-family: inherit;
            resize: none;
            max-height: 100px;
        }

        .chat-input::placeholder {
            color: #a7f3d0;
        }

        .attach-button {
            background: transparent;
            color: #a7f3d0;
            border: none;
            cursor: pointer;
            font-size: 18px;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
            border-radius: 50%;
        }

        .send-button {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            color: #ffffff;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .send-button:hover {
            background: linear-gradient(135deg, #34d399 0%, #6ee7b7 100%);
            transform: scale(1.05);
        }

        .back-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: transparent;
            color: #a7f3d0;
            text-decoration: none;
            transition: all 0.3s ease;
            flex-shrink: 0;
            font-size: 18px;
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #34d399;
        }

        @media (max-width: 767px) {
            body {
                padding: 10px;
            }

            .chat-container {
                height: 65vh;
                max-height: none;
                border-radius: 12px;
            }

            .chat-header {
                border-radius: 12px 12px 0 0;
            }

            .chat-input-container {
                border-radius: 0 0 12px 12px;
            }
        }

        @media (min-width: 768px) {
            body {
                padding: 30px;
            }

            .chat-container {
                max-width: 900px;
                height: 60vh;
                border: 2px solid #059669;
            }
        }
    </style>
</head>

<body>
    <div class="chat-container">
        <div class="chat-header">
            <a href="/dashboard/customer/tagihan/home" class="back-button">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="header-avatar">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="header-info">
                <h1>Chat Admin (Pembayaran)</h1>
                <div class="user-status">
                    <span class="status-dot"></span>
                    @php
                        $auth = Auth::user() ?? Auth::guard('customer')->user();
                    @endphp
                    <span>{{ $auth->name ?? $auth->nama_lengkap ?? 'Customer' }}</span>
                </div>
            </div>
        </div>

        <div class="chat-messages" id="chatMessages">
            <!-- Messages will be loaded here -->
        </div>

        <div class="typing-indicator" id="typingIndicator" style="display: none;">
            <div class="typing-dots">
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
            </div>
            <span>Admin sedang mengetik...</span>
        </div>

        <div class="chat-input-container" id="chatInputContainer">
            <div id="mediaPreview"
                style="display: none; padding: 8px 12px; background: #065f46; border-radius: 8px; margin-bottom: 8px;">
            </div>
            <form class="chat-input-form" id="chatForm" enctype="multipart/form-data">
                @csrf
                <input type="file" id="mediaInput" accept="image/*,video/*" style="display: none;">
                <div class="input-wrapper">
                    <button type="button" class="emoji-button">😊</button>
                    <input type="text" class="chat-input" id="messageInput" placeholder="Tanya tentang pembayaran..."
                        autocomplete="off">
                    <button type="button" class="attach-button" id="attachButton" title="Kirim foto/video">
                        <i class="fas fa-paperclip"></i>
                    </button>
                </div>
                <button type="submit" class="send-button" id="sendButton">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <script>
        window.userId = "{{ auth('customer')->id() }}";
        window.userName = "{{ $auth->name ?? $auth->nama_lengkap ?? 'Customer' }}";
        window.isAdmin = false;
        window.chatType = 'admin'; // KEY: Use admin-chat endpoints

        console.log('Customer Billing Chat Initialized');
        console.log('User ID:', window.userId);

        const chatMessages = document.getElementById('chatMessages');

        function scrollToBottom(smooth = true) {
            if (chatMessages) {
                chatMessages.scrollTo({
                    top: chatMessages.scrollHeight,
                    behavior: smooth ? 'smooth' : 'auto'
                });
            }
        }

        window.scrollToBottom = scrollToBottom;

        window.addEventListener('load', function () {
            setTimeout(() => scrollToBottom(false), 100);
        });
    </script>

    @vite(['resources/js/bootstrap.js', 'resources/js/echo.js', 'resources/js/admin-chat.js'])
</body>

</html>