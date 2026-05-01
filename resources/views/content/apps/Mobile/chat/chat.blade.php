@use('Illuminate\Support\Facades\Auth')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover, interactive-widget=resizes-visual">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat - User</title>
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
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        html {
            height: 100%;
            overflow: auto;
            scroll-behavior: smooth;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #0c1317 0%, #1a2530 100%);
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
            background: #0c1317;
            overflow: hidden;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        .chat-header {
            background: #1f2c33;
            color: #e9edef;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid #2a3942;
            flex-shrink: 0;
            min-height: 70px;
            z-index: 10;
            border-radius: 16px 16px 0 0;
        }

        .header-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00a884 0%, #00d9a8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #ffffff;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0, 168, 132, 0.3);
        }

        .header-info {
            flex: 1;
        }

        .header-info h1 {
            font-size: 17px;
            margin-bottom: 4px;
            font-weight: 600;
            color: #e9edef;
        }

        .user-status {
            font-size: 13px;
            color: #8696a0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            background: #00a884;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 16px 12px;
            padding-bottom: 80px;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            background-color: #0c1317;
            position: relative;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
            scroll-behavior: smooth;
        }

        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #3b4a54;
            border-radius: 10px;
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
            background: linear-gradient(135deg, #005c4b 0%, #00755e 100%);
            color: #e9edef;
            border-radius: 10px;
            border-top-right-radius: 2px;
        }

        .message.received .message-content {
            background: #1f2c33;
            color: #e9edef;
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
            color: #8696a0;
            position: absolute;
            right: 10px;
            bottom: 5px;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .message.sent .message-info {
            color: #a0c7bf;
        }

        .typing-indicator {
            padding: 12px 16px;
            font-size: 13px;
            color: #8696a0;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #0c1317;
            flex-shrink: 0;
            min-height: 45px;
        }

        .typing-dots {
            display: flex;
            gap: 4px;
        }

        .typing-dot {
            width: 6px;
            height: 6px;
            background: #8696a0;
            border-radius: 50%;
            animation: pulse 1.4s ease-in-out infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        .chat-input-container {
            padding: 10px 12px;
            padding-bottom: calc(10px + env(safe-area-inset-bottom, 0px));
            background: #1f2c33;
            border-top: 1px solid #2a3942;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: bottom 0.2s ease-out;
            border-radius: 0 0 16px 16px;
        }

        .chat-input-form {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }

        .input-wrapper {
            flex: 1;
            position: relative;
            background: #2a3942;
            border-radius: 24px;
            display: flex;
            align-items: center;
            padding: 0 10px;
            min-height: 46px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .emoji-button {
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            padding: 8px;
            color: #8696a0;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .emoji-button:hover {
            color: #e9edef;
            transform: scale(1.1);
        }

        .chat-input {
            flex: 1;
            padding: 12px 10px;
            border: none;
            background: transparent;
            font-size: 15px;
            outline: none;
            color: #e9edef;
            font-family: inherit;
            resize: none;
            max-height: 100px;
            overflow-y: auto;
        }

        .chat-input::placeholder {
            color: #8696a0;
        }

        .send-button {
            width: 46px;
            height: 46px;
            background: linear-gradient(135deg, #00a884 0%, #00d9a8 100%);
            color: #ffffff;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0, 168, 132, 0.3);
        }

        .send-button:hover {
            background: linear-gradient(135deg, #06cf9c 0%, #00ffc8 100%);
            transform: scale(1.05);
        }

        .send-button:active {
            transform: scale(0.95);
        }

        .send-button:disabled {
            background: #3b4a54;
            cursor: not-allowed;
            box-shadow: none;
        }

        .date-divider {
            text-align: center;
            margin: 20px 0;
        }

        .date-text {
            display: inline-block;
            padding: 6px 14px;
            background: #1f2c33;
            color: #8696a0;
            font-size: 12.5px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        /* Bottom Navbar Styles */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #1f2c33;
            border-top: 1px solid #2a3942;
            transition: transform 0.3s ease;
            z-index: 999;
        }

        .bottom-nav.hidden {
            transform: translateY(100%);
        }

        /* Mobile specific */
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

.back-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: transparent;
    color: #8696a0;
    text-decoration: none;
    transition: all 0.3s ease;
    flex-shrink: 0;
    font-size: 18px;
}

.back-button:hover {
    background: rgba(255, 255, 255, 0.05);
    color: #00a884;
    transform: scale(1.1);
}

.back-button:active {
    transform: scale(0.95);
}



        /* Desktop styles */
        @media (min-width: 768px) {
            body {
                padding: 30px;
            }

            .chat-container {
                max-width: 900px;
                height: 60vh;
                border: 2px solid #2a3942;
            }

            .chat-input-container {
                position: absolute;
                border-radius: 0 0 14px 14px;
            }

            .bottom-nav {
                position: relative;
            }
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
<a href="{{ url('https://layanan.jernih.net.id/dashboard/customer/tagihan/home') }}" class="back-button">
        <i class="fas fa-arrow-left"></i>
    </a>
            <div class="header-avatar">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="header-info">
                <h1>Chat dengan Admin</h1>
                <div class="user-status">
                    <span class="status-dot"></span>
                    <span>{{ Auth::user()->name }}</span>
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
            <form class="chat-input-form" id="chatForm">
                @csrf
                <div class="input-wrapper">
                    <button type="button" class="emoji-button"></button>
                    <input
                        type="text"
                        class="chat-input"
                        id="messageInput"
                        placeholder="Tulis pesan"
                        autocomplete="off"
                        required
                    >
                </div>
                <button type="submit" class="send-button" id="sendButton">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <script>
        window.userId = "{{ auth('customer')->id() }}";
        window.userName = "{{ auth('customer')->user()->nama_lengkap}}";
        window.isAdmin = false;

        console.log('Chat User Initialized');
        console.log('User ID:', window.userId);
        console.log('User Name:', window.userName);

        // Elements
        const messageInput = document.getElementById('messageInput');
        const bottomNav = document.querySelector('.bottom-nav');
        const chatMessages = document.getElementById('chatMessages');
        const chatInputContainer = document.getElementById('chatInputContainer');

        // Smooth scroll to bottom function
        function scrollToBottom(smooth = true) {
            if (chatMessages) {
                chatMessages.scrollTo({
                    top: chatMessages.scrollHeight,
                    behavior: smooth ? 'smooth' : 'auto'
                });
            }
        }

        // Make scrollToBottom available globally
        window.scrollToBottom = scrollToBottom;

        // Detect if user is at bottom (for auto-scroll on new messages)
        let isAtBottom = true;

        if (chatMessages) {
            chatMessages.addEventListener('scroll', function() {
                const threshold = 50;
                const position = chatMessages.scrollHeight - chatMessages.scrollTop - chatMessages.clientHeight;
                isAtBottom = position < threshold;
            }, { passive: true });
        }

        // Auto-scroll only if user is already at bottom
        function autoScrollIfNeeded() {
            if (isAtBottom) {
                scrollToBottom(true);
            }
        }

        window.autoScrollIfNeeded = autoScrollIfNeeded;

        // Visual Viewport API untuk handle keyboard (khusus mobile)
        if (window.visualViewport && window.innerWidth < 768) {
            const viewport = window.visualViewport;

            function updateInputPosition() {
                const offsetTop = viewport.offsetTop;
                const viewportHeight = viewport.height;
                const windowHeight = window.innerHeight;

                // Hitung tinggi keyboard
                const keyboardHeight = windowHeight - (viewportHeight + offsetTop);

                if (keyboardHeight > 100) {
                    // Keyboard muncul - geser input ke atas
                    chatInputContainer.style.bottom = `${keyboardHeight}px`;
                    chatMessages.style.paddingBottom = `${keyboardHeight + 80}px`;

                    // Hide bottom nav if exists
                    if (bottomNav) {
                        bottomNav.classList.add('hidden');
                    }

                    // Auto scroll ke bawah
                    setTimeout(() => scrollToBottom(true), 150);
                } else {
                    // Keyboard tertutup - reset posisi
                    chatInputContainer.style.bottom = '0px';
                    chatMessages.style.paddingBottom = '80px';

                    // Show bottom nav if exists
                    if (bottomNav) {
                        bottomNav.classList.remove('hidden');
                    }
                }
            }

            // Listen resize dan scroll viewport
            viewport.addEventListener('resize', updateInputPosition);
            viewport.addEventListener('scroll', updateInputPosition);

            // Update saat input focus
            messageInput.addEventListener('focus', () => {
                setTimeout(updateInputPosition, 150);
            });

            // Update saat input blur
            messageInput.addEventListener('blur', () => {
                setTimeout(updateInputPosition, 150);
            });
        }
        // Fallback untuk browser tanpa Visual Viewport API
        else if (window.innerWidth < 768) {
            let originalHeight = window.innerHeight;

            window.addEventListener('resize', function() {
                const currentHeight = window.innerHeight;
                const diff = originalHeight - currentHeight;

                if (diff > 150) {
                    // Keyboard muncul
                    chatInputContainer.style.bottom = `${diff}px`;
                    chatMessages.style.paddingBottom = `${diff + 80}px`;

                    if (bottomNav) {
                        bottomNav.classList.add('hidden');
                    }

                    setTimeout(() => scrollToBottom(true), 200);
                } else if (diff < -150) {
                    // Keyboard tertutup
                    chatInputContainer.style.bottom = '0px';
                    chatMessages.style.paddingBottom = '80px';
                    originalHeight = currentHeight;

                    if (bottomNav) {
                        bottomNav.classList.remove('hidden');
                    }
                }
            });

            // Handle focus/blur for fallback
            messageInput.addEventListener('focus', function() {
                if (bottomNav) {
                    bottomNav.classList.add('hidden');
                }

                setTimeout(() => scrollToBottom(true), 300);
            });

            messageInput.addEventListener('blur', function() {
                setTimeout(() => {
                    if (bottomNav) {
                        bottomNav.classList.remove('hidden');
                    }
                }, 100);
            });
        }

        // Prevent iOS bounce scroll on body
        document.body.addEventListener('touchmove', function(e) {
            if (e.target === document.body) {
                e.preventDefault();
            }
        }, { passive: false });

        // Initial scroll to bottom on page load
        window.addEventListener('load', function() {
            setTimeout(() => {
                scrollToBottom(false);
            }, 100);
        });
    </script>

    @vite(['resources/js/bootstrap.js', 'resources/js/echo.js', 'resources/js/chat.js'])

</body>
</html>
