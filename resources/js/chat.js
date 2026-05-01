// Chat functionality
document.addEventListener('DOMContentLoaded', function () {
    if (window.__csChatInitialized) return;

    const waitForAxiosThenInit = (startedAt = Date.now()) => {
        if (typeof window.axios === 'undefined') {
            if (Date.now() - startedAt <= 5000) {
                setTimeout(() => waitForAxiosThenInit(startedAt), 120);
            } else {
                console.error('[CSChat] axios is unavailable after waiting 5s, skip chat initialization.');
            }
            return;
        }

        if (window.__csChatInitialized) return;
        window.__csChatInitialized = true;
        initChat();
    };

    const initChat = () => {

    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    const mediaInput = document.getElementById('mediaInput');
    const attachButton = document.getElementById('attachButton');
    const locationButton = document.getElementById('locationButton');
    const mediaPreview = document.getElementById('mediaPreview');

    let selectedMediaFile = null;
    
    // Sound system for CS chat
    let audioUnlocked = false;
    let preloadedAudio = null;
    
    function initSound() {
        preloadedAudio = new Audio('/sounds/42289.mp3');
        preloadedAudio.volume = 0.5;
        preloadedAudio.load();
        
        const unlockAudioContext = () => {
            if (audioUnlocked) return;
            
            preloadedAudio.play()
                .then(() => {
                    preloadedAudio.pause();
                    preloadedAudio.currentTime = 0;
                    audioUnlocked = true;
                })
                .catch(() => {});
        };
        
        const events = ['click', 'touchstart', 'keydown', 'scroll', 'mousemove'];
        events.forEach(eventType => {
            document.addEventListener(eventType, unlockAudioContext, { once: true, passive: true });
        });
    }
    
    // Initialize sound system
    initSound();

    if (!chatMessages || !chatForm) {
        return;
    }

    function autoResizeMessageInput() {
        if (!messageInput || messageInput.tagName !== 'TEXTAREA') return;
        messageInput.style.height = 'auto';
        messageInput.style.height = `${Math.min(messageInput.scrollHeight, 120)}px`;
    }

    if (messageInput && messageInput.tagName === 'TEXTAREA') {
        autoResizeMessageInput();
        messageInput.addEventListener('input', autoResizeMessageInput);
        messageInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && (event.ctrlKey || event.metaKey)) {
                event.preventDefault();
                chatForm.requestSubmit();
            }
        });
    }

    // Setup attach button click
    if (attachButton && mediaInput) {
        attachButton.addEventListener('click', function (e) {
            e.preventDefault();
            mediaInput.click();
        });

        mediaInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                selectedMediaFile = file;
                showMediaPreview(file);
            }
        });
    }

    // Location button
    if (locationButton) {
        locationButton.addEventListener('click', function (e) {
            e.preventDefault();
            shareLocation();
        });
    }

    // Show media preview
    function showMediaPreview(file) {
        if (!mediaPreview) return;

        const isImage = file.type.startsWith('image/');
        const isVideo = file.type.startsWith('video/');

        let previewHTML = '';

        if (isImage) {
            const url = URL.createObjectURL(file);
            previewHTML = `
                <div class="media-preview-container">
                    <img src="${url}" alt="Preview" style="max-height: 100px; border-radius: 8px;">
                    <button type="button" class="remove-media-btn" onclick="window.clearMediaPreview()">
                        <i class="fas fa-times"></i>
                    </button>
                    <span class="media-filename">${file.name}</span>
                </div>
            `;
        } else if (isVideo) {
            previewHTML = `
                <div class="media-preview-container">
                    <i class="fas fa-video" style="font-size: 24px; color: #3b82f6;"></i>
                    <button type="button" class="remove-media-btn" onclick="window.clearMediaPreview()">
                        <i class="fas fa-times"></i>
                    </button>
                    <span class="media-filename">${file.name}</span>
                </div>
            `;
        }

        mediaPreview.innerHTML = previewHTML;
        mediaPreview.style.display = 'block';
    }

    // Clear media preview
    window.clearMediaPreview = function () {
        selectedMediaFile = null;
        if (mediaInput) mediaInput.value = '';
        if (mediaPreview) {
            mediaPreview.innerHTML = '';
            mediaPreview.style.display = 'none';
        }
    };

    // Location sharing helpers
    function setLocationLoading(isLoading) {
        if (!locationButton) return;
        locationButton.disabled = isLoading;
        locationButton.classList.toggle('loading', isLoading);
        locationButton.innerHTML = isLoading ? '<i class="fas fa-spinner fa-spin"></i>' : '<i class="fas fa-location-arrow"></i>';
    }

    function shareLocation() {
        if (!locationButton) return;
        if (!navigator.geolocation) {
            showErrorDialog({
                title: 'Lokasi Tidak Didukung',
                message: 'Browser tidak mendukung akses lokasi.'
            });
            return;
        }

        if (!window.isSecureContext) {
            showErrorDialog({
                title: 'Aktifkan HTTPS',
                message: 'Akses lokasi hanya berfungsi di koneksi aman (https). Buka aplikasi/web melalui alamat https.'
            });
            return;
        }

        // Jika permission sudah ditolak sebelumnya (terutama PWA), beri panduan cepat
        if (navigator.permissions && navigator.permissions.query) {
            navigator.permissions.query({ name: 'geolocation' }).then((status) => {
                if (status.state === 'denied') {
                    showErrorDialog({
                        title: 'Izin Lokasi Diblokir',
                        message: 'Buka pengaturan situs/PWA, ubah Location ke Allow lalu muat ulang aplikasi.'
                    });
                }
            });
        }

        setLocationLoading(true);

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                const link = `https://maps.google.com/?q=${lat},${lng}`;
                const text = `Lokasi saya: ${lat.toFixed(5)}, ${lng.toFixed(5)}\\n${link}`;

                const formData = new FormData();
                formData.append('message', text);

                if (isAdmin) {
                    const receiverIdInput = document.getElementById('receiverId');
                    const receiverId = receiverIdInput ? receiverIdInput.value : '';
                    if (!receiverId) {
                        setLocationLoading(false);
                        showErrorDialog({
                            title: 'Pilih Pelanggan',
                            message: 'Silakan pilih pelanggan sebelum mengirim lokasi.'
                        });
                        return;
                    }
                    formData.append('receiver_id', receiverId);
                }

                axios.post('/chat/send', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                })
                    .then(response => {
                        appendMessage(response.data.message, false);
                        scrollToBottom();

                        if (isAdmin && window.selectedUserId) {
                            moveUserToTop(window.selectedUserId);
                        }
                    })
                    .catch(error => {
                        showErrorDialog({
                            title: 'Gagal Mengirim Lokasi',
                            message: error.response?.data?.error || 'Silakan coba lagi.'
                        });
                    })
                    .finally(() => setLocationLoading(false));
            },
            (err) => {
                let msg = 'Gagal mengakses lokasi.';
                if (err.code === 1) msg = 'Izin lokasi ditolak. Izinkan akses lokasi untuk mengirim lokasi.';
                else if (err.code === 2) msg = 'Lokasi tidak tersedia saat ini.';
                else if (err.code === 3) msg = 'Permintaan lokasi timeout.';
                showErrorDialog({ title: 'Lokasi Gagal', message: msg });
                setLocationLoading(false);
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    }

    const isAdmin = window.isAdmin === true && !!document.getElementById('receiverId');
    const userId = window.userId;
    const API_BASE = '/chat';
    const MESSAGE_EDIT_WINDOW_MS = 15 * 60 * 1000;
    const messageStore = new Map();
    const userFilterState = {
        mode: 'all',
        search: '',
    };
    const FALLBACK_POLL_INTERVAL_MS = 1000;
    const RECONNECT_DELAY_MS = 800;
    let isSocketConnected = false;
    let fallbackPollingTimer = null;
    let reconnectTimer = null;
    let lastRenderedMessagesSignature = '';
    let currentConversationKey = '';
    const unreadCountSnapshot = new Map();
    let lastUnreadSignature = '';
    let lastUnreadFetchAt = 0;
    const UNREAD_REFRESH_THROTTLE_MS = 4000;

    // Load unread counts for admin on page load
    function loadUnreadCounts() {
        if (!isAdmin) return;

        const now = Date.now();
        if (now - lastUnreadFetchAt < UNREAD_REFRESH_THROTTLE_MS) return;
        lastUnreadFetchAt = now;

        axios.get('/chat/unread-count')
            .then(response => {
                const unreadCounts = response.data || {};
                const unreadEntries = Object.entries(unreadCounts)
                    .map(([id, count]) => [String(id), Number(count || 0)])
                    .sort((a, b) => a[0].localeCompare(b[0]));
                const unreadSignature = JSON.stringify(unreadEntries);
                if (unreadSignature === lastUnreadSignature) {
                    return;
                }

                lastUnreadSignature = unreadSignature;
                const allBadges = document.querySelectorAll('.unread-badge');
                let hasVisualChange = false;

                allBadges.forEach((badge) => {
                    const senderId = (badge.id || '').replace('unread-', '');
                    if (!senderId) return;

                    const nextCount = Number(unreadCounts[senderId] || 0);
                    const prevCount = unreadCountSnapshot.has(senderId)
                        ? Number(unreadCountSnapshot.get(senderId) || 0)
                        : null;

                    if (prevCount === nextCount) {
                        return;
                    }

                    unreadCountSnapshot.set(senderId, nextCount);
                    badge.textContent = nextCount > 0 ? String(nextCount) : '0';
                    badge.style.display = nextCount > 0 ? 'inline-block' : 'none';
                    hasVisualChange = true;
                });

                if (hasVisualChange) {
                    applyUserFilter();
                }
            })
            .catch(error => { });
    }

    // Function to get initials from name
    function getInitials(name) {
        return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
    }

    function applyUserFilter() {
        if (!isAdmin) return;

        const userList = document.getElementById('userList');
        if (!userList) return;

        const userItems = userList.querySelectorAll('.user-item');
        let visibleCount = 0;

        userItems.forEach(item => {
            const userName = (item.dataset.userName || '').toLowerCase();
            const userType = (item.querySelector('.user-type')?.textContent || '').toLowerCase();
            const badge = item.querySelector('.unread-badge');
            const unreadCount = parseInt(badge?.textContent || '0', 10) || 0;
            const hasUnread = unreadCount > 0;
            const isActive = item.classList.contains('active');
            const matchesSearch = userFilterState.search === ''
                || userName.includes(userFilterState.search)
                || userType.includes(userFilterState.search);
            const matchesTab = userFilterState.mode === 'all' || hasUnread || isActive;

            if (matchesSearch && matchesTab) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        let noResults = userList.querySelector('.no-results-message');
        if (visibleCount === 0) {
            if (!noResults) {
                noResults = document.createElement('div');
                noResults.className = 'no-results-message';
                noResults.innerHTML = `
                    <div style="text-align: center; padding: 40px 20px; color: #94a3b8;">
                        <i class="fas fa-search" style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
                        <div style="font-size: 14px; margin-bottom: 4px; font-weight: 500;">Tidak ada hasil</div>
                        <div style="font-size: 12px;">Coba kata kunci lain atau ubah filter chat</div>
                    </div>
                `;
                userList.appendChild(noResults);
            }
            noResults.style.display = 'block';
        } else if (noResults) {
            noResults.style.display = 'none';
        }
    }

    // WhatsApp-style date formatting
    let lastDisplayedDate = null;

    function formatWhatsAppDate(dateString) {
        const date = new Date(dateString);
        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);

        // Reset time part for comparison
        const dateOnly = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        const todayOnly = new Date(today.getFullYear(), today.getMonth(), today.getDate());
        const yesterdayOnly = new Date(yesterday.getFullYear(), yesterday.getMonth(), yesterday.getDate());

        // Calculate days difference
        const diffTime = todayOnly - dateOnly;
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays === 0) {
            return 'Hari ini';
        } else if (diffDays === 1) {
            return 'Kemarin';
        } else if (diffDays < 7) {
            // Within this week - show day name
            const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            return dayNames[date.getDay()];
        } else {
            // Older than a week - show full date
            return date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'numeric',
                year: 'numeric'
            });
        }
    }

    function getDateKey(dateString) {
        const date = new Date(dateString);
        return `${date.getFullYear()}-${date.getMonth()}-${date.getDate()}`;
    }

    function shouldShowDateDivider(messageDate) {
        const dateKey = getDateKey(messageDate);
        if (lastDisplayedDate !== dateKey) {
            lastDisplayedDate = dateKey;
            return true;
        }
        return false;
    }

    function createDateDivider(dateString) {
        const divider = document.createElement('div');
        divider.className = 'date-divider';
        divider.innerHTML = `<span class="date-text">${formatWhatsAppDate(dateString)}</span>`;
        return divider;
    }

    function formatWhatsAppTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function toBooleanFlag(value) {
        if (typeof value === 'boolean') return value;
        if (typeof value === 'number') return value === 1;
        if (typeof value === 'string') {
            const normalized = value.trim().toLowerCase();
            return normalized === '1' || normalized === 'true' || normalized === 'yes' || normalized === 'on';
        }
        return false;
    }

    function extractLocationFromMessage(messageText) {
        if (!messageText || typeof messageText !== 'string') return null;
        // Priority: find lat,lng in maps query
        const queryMatch = messageText.match(/maps\.google\.com\/?\?q=(-?\d+(?:\.\d+)?),\s*(-?\d+(?:\.\d+)?)/i);
        if (queryMatch) {
            return { lat: parseFloat(queryMatch[1]), lng: parseFloat(queryMatch[2]) };
        }
        // Fallback: first pair of decimals
        const pairMatch = messageText.match(/(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/);
        if (pairMatch) {
            return { lat: parseFloat(pairMatch[1]), lng: parseFloat(pairMatch[2]) };
        }
        return null;
    }

    // Load messages
    function buildMessagesSignature(messages) {
        if (!Array.isArray(messages) || messages.length === 0) return 'empty';
        const first = messages[0];
        const last = messages[messages.length - 1];
        const firstKey = `${first.id || 'x'}:${first.updated_at || first.edited_at || first.deleted_at || first.created_at || ''}:${first.is_read ? 1 : 0}`;
        const lastKey = `${last.id || 'x'}:${last.updated_at || last.edited_at || last.deleted_at || last.created_at || ''}:${last.is_read ? 1 : 0}`;
        return `${messages.length}|${firstKey}|${lastKey}`;
    }

    function loadMessages(targetUserId = null, options = {}) {
        const {
            autoScroll = true,
            skipIfUnchanged = false,
            resetSignature = false,
        } = options;

        const url = isAdmin && targetUserId
            ? `/chat/messages/${targetUserId}`
            : '/chat/messages';

        axios.get(url)
            .then(response => {
                const conversationKey = isAdmin
                    ? `admin:${String(targetUserId || '')}`
                    : `user:${String(userId || '')}`;
                const responseMessages = Array.isArray(response.data) ? response.data : [];

                if (resetSignature || currentConversationKey !== conversationKey) {
                    currentConversationKey = conversationKey;
                    lastRenderedMessagesSignature = '';
                }

                const incomingSignature = buildMessagesSignature(responseMessages);
                if (skipIfUnchanged && incomingSignature === lastRenderedMessagesSignature) {
                    return;
                }

                displayMessages(response.data);
                lastRenderedMessagesSignature = incomingSignature;

                if (autoScroll) {
                    scrollToBottom();
                }

                // For customers, mark admin messages as read
                if (!isAdmin) {
                    const unreadAdminMessages = responseMessages.filter(m =>
                        String(m.sender_id) !== String(userId) && !m.is_read
                    );

                    if (unreadAdminMessages.length > 0) {
                        // Get admin ID from first admin message
                        const adminId = unreadAdminMessages[0].sender_id;

                        axios.post(`/chat/mark-read/${adminId}`)
                            .catch(err => { });
                    }
                }
            })
            .catch(error => { });
    }

    function refreshMessagesFallback() {
        if (isSocketConnected || document.visibilityState !== 'visible') return;

        if (isAdmin) {
            if (window.selectedUserId) {
                loadMessages(window.selectedUserId, { autoScroll: false, skipIfUnchanged: true });
            }
            loadUnreadCounts();
            return;
        }

        loadMessages(null, { autoScroll: false, skipIfUnchanged: true });
    }

    function ensureFallbackPolling() {
        if (fallbackPollingTimer) return;
        fallbackPollingTimer = setInterval(refreshMessagesFallback, FALLBACK_POLL_INTERVAL_MS);
    }

    function scheduleSocketReconnect() {
        if (reconnectTimer) return;
        reconnectTimer = setTimeout(() => {
            reconnectTimer = null;
            const pusher = window.Echo?.connector?.pusher;
            if (!pusher) return;

            const state = pusher.connection?.state;
            if (state === 'connected' || state === 'connecting') return;
            try {
                pusher.connect();
            } catch (error) { }
        }, RECONNECT_DELAY_MS);
    }

    // Display messages
    function displayMessages(messages) {
        chatMessages.innerHTML = '';
        lastDisplayedDate = null; // Reset date tracker when loading messages
        messageStore.clear();

        messages.forEach(message => {
            appendMessage(message, false, true);
        });
    }

    function normalizeMessagePayload(message, fallbackMessage = null) {
        if (!message) return null;

        const normalized = {
            ...(fallbackMessage || {}),
            ...message,
        };

        normalized.chat_type = normalized.chat_type || 'cs';
        normalized.message = typeof normalized.message === 'string' ? normalized.message : '';
        normalized.is_read = toBooleanFlag(normalized.is_read);
        normalized.is_deleted = toBooleanFlag(normalized.is_deleted);
        normalized.edited_at = normalized.edited_at || null;
        normalized.deleted_at = normalized.deleted_at || null;
        normalized.media_url = normalized.media_url || null;
        normalized.media_type = normalized.media_type || null;
        normalized.created_at = normalized.created_at || fallbackMessage?.created_at || new Date().toISOString();

        return normalized;
    }

    function cacheMessage(message) {
        if (!message || !message.id) return normalizeMessagePayload(message);
        const key = String(message.id);
        const normalized = normalizeMessagePayload(message, messageStore.get(key) || null);
        messageStore.set(key, normalized);
        return normalized;
    }

    function renderMessageStatus(message, isSent, isPending = false) {
        if (!isSent) return '';

        if (isPending) {
            return `
                <span class="message-status-wrap">
                    <i class="fas fa-clock message-status"></i>
                    <span class="message-status-text">Menunggu</span>
                </span>
            `;
        }

        if (toBooleanFlag(message.is_read)) {
            return `
                <span class="message-status-wrap">
                    <i class="fas fa-check-double message-status read"></i>
                    <span class="message-status-text read">Dibaca</span>
                </span>
            `;
        }

        return `
            <span class="message-status-wrap">
                <i class="fas fa-check message-status"></i>
                <span class="message-status-text">Terkirim</span>
            </span>
        `;
    }

    function renderMessageMedia(message) {
        // Detect location in text (pretty render)
        const loc = extractLocationFromMessage(message.message);
        if (loc) {
            const { lat, lng } = loc;
            const mapsLink = `https://maps.google.com/?q=${lat},${lng}`;

            let mapUrl = '';
            const googleKey = typeof window.googleStaticMapKey === 'string' ? window.googleStaticMapKey : '';
            if (googleKey) {
                mapUrl = `https://maps.googleapis.com/maps/api/staticmap?center=${lat},${lng}&zoom=16&size=640x360&scale=2&maptype=roadmap&markers=color:red%7C${lat},${lng}&key=${googleKey}`;
            } else {
                // Inline SVG fallback to guarantee terlihat (tidak tergantung domain luar)
                const svg = `
<svg xmlns="http://www.w3.org/2000/svg" width="640" height="360" viewBox="0 0 640 360">
  <defs>
    <linearGradient id="g" x1="0" x2="1" y1="0" y2="1">
      <stop offset="0" stop-color="#0f172a"/>
      <stop offset="1" stop-color="#1e293b"/>
    </linearGradient>
  </defs>
  <rect width="640" height="360" fill="url(#g)"/>
  <path d="M320 90c-33 0-60 27-60 60 0 35 53 106 56 110a5 5 0 0 0 8 0c3-4 56-75 56-110 0-33-27-60-60-60zm0 84a24 24 0 1 1 0-48 24 24 0 0 1 0 48z" fill="#22d3ee"/>
  <circle cx="320" cy="150" r="18" fill="#0f172a"/>
  <text x="320" y="315" fill="#e2e8f0" font-size="32" font-family="Inter,Arial,sans-serif" text-anchor="middle" font-weight="700">
    ${lat.toFixed(5)}, ${lng.toFixed(5)}
  </text>
</svg>`;
                mapUrl = 'data:image/svg+xml;utf8,' + encodeURIComponent(svg.trim());
            }
            return `
                <div class="location-card">
                    <a href="${mapsLink}" target="_blank" rel="noopener">
                        <img src="${mapUrl}" alt="Lokasi" class="location-img">
                    </a>
                    <div class="location-footer">
                        <div class="location-coord"><i class="fas fa-map-marker-alt"></i>${lat.toFixed(5)}, ${lng.toFixed(5)}</div>
                        <a class="location-btn" href="${mapsLink}" target="_blank" rel="noopener">Buka Maps</a>
                    </div>
                </div>
            `;
        }

        if (message.is_deleted || !message.media_url || !message.media_type) {
            return '';
        }

        if (message.media_type === 'image') {
            return `
                <div class="message-media">
                    <img src="${message.media_url}" alt="Image" onclick="window.open('${message.media_url}', '_blank')" style="max-width: 250px; max-height: 200px; border-radius: 8px; cursor: pointer; margin-bottom: 6px;">
                </div>
            `;
        }

        if (message.media_type === 'video') {
            return `
                <div class="message-media">
                    <video controls style="max-width: 250px; max-height: 200px; border-radius: 8px; margin-bottom: 6px;">
                        <source src="${message.media_url}" type="video/mp4">
                        Browser tidak mendukung video.
                    </video>
                </div>
            `;
        }

        return '';
    }

    function renderMessageText(message) {
        // If we render location card, suppress raw text to avoid duplicating
        if (extractLocationFromMessage(message.message)) {
            return '';
        }
        if (!message.message || message.message.trim() === '') {
            return '';
        }

        const formattedText = escapeHtml(message.message).replace(/\r\n|\r|\n/g, '<br>');

        if (message.is_deleted) {
            return `<div class="message-text message-text-deleted">${formattedText}</div>`;
        }

        const editedLabel = message.edited_at ? ' <span class="message-edited">(diedit)</span>' : '';
        return `<div class="message-text">${formattedText}${editedLabel}</div>`;
    }

    function canManageMessage(message) {
        if (!message || message.is_deleted) return false;
        if (isAdmin) return true; // Admin/CS boleh edit/hapus kapan saja

        if (!message.created_at) return false;
        const createdAt = new Date(message.created_at).getTime();
        if (Number.isNaN(createdAt)) return false;
        return (Date.now() - createdAt) <= MESSAGE_EDIT_WINDOW_MS;
    }

    function renderMessageActions(message, isSent) {
        if (!isAdmin) return '';

        const canManage = isSent && canManageMessage(message) && message.id;
        if (!canManage) return '';

        return `
            <span class="message-actions">
                <button class="message-action-btn js-edit-message" data-message-id="${message.id}" title="Edit"><i class="fas fa-pen"></i></button>
                <button class="message-action-btn js-delete-message" data-message-id="${message.id}" title="Hapus"><i class="fas fa-trash"></i></button>
            </span>
        `;
    }

    function renderMessageContent(message, isSent, isPending = false) {
        const statusInfo = renderMessageStatus(message, isSent, isPending);
        const mediaContent = renderMessageMedia(message);
        const textContent = renderMessageText(message);
        const time = formatWhatsAppTime(message.created_at);
        const actionsHtml = renderMessageActions(message, isSent);

        return `
            <div class="message-content">
                ${mediaContent}
                ${textContent}
                <div class="message-info">
                    ${statusInfo}
                    ${time}
                    ${actionsHtml}
                </div>
            </div>
        `;
    }

    function patchMessageElement(messageDiv, message, isPending = false) {
        const isSent = messageDiv.classList.contains('sent');
        const bubble = messageDiv.querySelector('.message-bubble');
        if (!bubble) return;

        if (message.id) {
            messageDiv.dataset.messageId = String(message.id);
        }
        messageDiv.dataset.createdAt = message.created_at || '';
        bubble.innerHTML = renderMessageContent(message, isSent, isPending);
    }

    function replacePendingMessage(tempId, serverMessage) {
        if (!tempId) return false;
        const pendingNode = chatMessages.querySelector(`[data-temp-id="${tempId}"]`);
        if (!pendingNode) return false;

        const normalized = cacheMessage(serverMessage);
        if (!normalized) return false;

        pendingNode.classList.remove('message-pending');
        pendingNode.removeAttribute('data-temp-id');
        pendingNode.dataset.messageId = String(normalized.id || '');
        pendingNode.dataset.createdAt = normalized.created_at || '';
        patchMessageElement(pendingNode, normalized, false);
        return true;
    }

    function updateMessageRealtime(message, options = {}) {
        const normalized = cacheMessage(message);
        if (!normalized || !normalized.id) return;

        const { appendIfMissing = true, autoScroll = false } = options;
        const messageDiv = chatMessages.querySelector(`[data-message-id="${normalized.id}"]`);
        if (messageDiv) {
            patchMessageElement(messageDiv, normalized, false);
        } else if (appendIfMissing) {
            appendMessage(normalized, false, false);
        }

        if (autoScroll) {
            scrollToBottom();
        }
    }

    function getMessageFromDom(messageId) {
        const messageDiv = chatMessages.querySelector(`[data-message-id="${messageId}"]`);
        if (!messageDiv) return null;

        const messageTextEl = messageDiv.querySelector('.message-text');
        const rawText = messageTextEl ? messageTextEl.textContent : '';
        const cleanText = (rawText || '').replace('(diedit)', '').trim();

        return {
            id: messageId,
            sender_id: messageDiv.classList.contains('sent') ? String(userId) : null,
            message: cleanText,
            created_at: messageDiv.dataset.createdAt || new Date().toISOString(),
            is_deleted: messageTextEl ? messageTextEl.classList.contains('message-text-deleted') : false,
        };
    }

    // Append single message
    function appendMessage(message, isPending = false, isLoading = false) {
        const normalized = cacheMessage(message);
        if (!normalized) return;

        // Cek duplikat berdasarkan message ID
        if (normalized.id && chatMessages.querySelector(`[data-message-id="${normalized.id}"]`)) {
            const existingMessage = chatMessages.querySelector(`[data-message-id="${normalized.id}"]`);
            if (existingMessage) {
                patchMessageElement(existingMessage, normalized, isPending);
            }
            return;
        }

        // Show date divider if needed (only when loading messages or for new day)
        if (normalized.created_at && shouldShowDateDivider(normalized.created_at)) {
            chatMessages.appendChild(createDateDivider(normalized.created_at));
        }

        const messageDiv = document.createElement('div');

        // Convert to string for UUID comparison
        const currentUserId = String(userId);
        const messageSenderId = String(normalized.sender_id);
        const isSent = messageSenderId === currentUserId;

        messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
        if (normalized.id) {
            messageDiv.dataset.messageId = normalized.id;
        }
        if (normalized.tempId) {
            messageDiv.dataset.tempId = normalized.tempId;
        }
        messageDiv.dataset.createdAt = normalized.created_at || '';

        const senderName = normalized.sender ? normalized.sender.name : 'Unknown';
        const initials = getInitials(senderName);

        messageDiv.innerHTML = `
            <div class="message-avatar">${initials}</div>
            <div class="message-bubble">
                ${renderMessageContent(normalized, isSent, isPending)}
            </div>
        `;

        chatMessages.appendChild(messageDiv);

        // Hilangkan notifikasi suara - tidak perlu lagi
        // if (!isSent && !isPending) {
        //     playNotificationSound();
        //     showNotification(senderName, message.message);
        // }
    }
    // Notification sound - improved with preloaded audio
    function playNotificationSound() {
        if (!audioUnlocked) return;
        
        try {
            if (preloadedAudio) {
                preloadedAudio.currentTime = 0;
                preloadedAudio.play().catch(() => {});
            } else {
                const audio = new Audio('/sounds/42289.mp3');
                audio.volume = 0.5;
                audio.play().catch(() => {});
            }
        } catch (error) {}
    }

    // Browser notification removed for performance - using sound only

    // Update unread badge untuk admin (reload from server for accuracy)
    function updateUnreadBadge(senderId) {
        const badge = document.getElementById(`unread-${senderId}`);
        if (!badge) return;

        // Realtime: increment locally immediately (tanpa menunggu refresh API periodik).
        const currentCount = parseInt(badge.textContent, 10) || 0;
        const nextCount = currentCount + 1;
        badge.textContent = String(nextCount);
        badge.style.display = 'inline-block';
        unreadCountSnapshot.set(String(senderId), nextCount);
        applyUserFilter();
    }

    // Move user to top of list when new message arrives
    function moveUserToTop(userId) {
        const userList = document.getElementById('userList');
        if (!userList) return;

        // Find user item
        const userItem = userList.querySelector(`[data-user-id="${userId}"]`);
        if (!userItem) return;

        // If already at top, no need to move
        if (userList.firstElementChild === userItem) return;

        // Remove from current position and add to top with smooth animation
        userItem.style.transition = 'all 0.3s ease';
        userList.insertBefore(userItem, userList.firstElementChild);

        // Add highlight animation
        userItem.style.backgroundColor = '#f0f9ff';
        setTimeout(() => {
            userItem.style.backgroundColor = '';
        }, 1000);
    }

    // Clear unread badge
    function clearUnreadBadge(userId) {
        const badge = document.getElementById(`unread-${userId}`);
        if (badge) {
            badge.textContent = '0';
            badge.style.display = 'none';
        }
        unreadCountSnapshot.set(String(userId), 0);
        applyUserFilter();
    }

    // Update message read status (centang 1 -> centang 2)
    function updateMessageReadStatus(messageIds) {
        if (!messageIds || messageIds.length === 0) return;

        messageIds.forEach(messageId => {
            const messageDiv = chatMessages.querySelector(`[data-message-id="${messageId}"]`);

            if (messageDiv) {
                const statusIcon = messageDiv.querySelector('.message-status');
                const statusText = messageDiv.querySelector('.message-status-text');

                if (statusIcon) {
                    statusIcon.className = 'fas fa-check-double message-status read';
                }
                if (statusText) {
                    statusText.textContent = 'Dibaca';
                    statusText.classList.add('read');
                }
            }
        });
    }

    function showPromptDialog({ title = 'Edit Pesan', message = '', placeholder = '', confirmText = 'Simpan', cancelText = 'Batal', defaultValue = '', onConfirm }) {
        const overlay = document.createElement('div');
        overlay.className = 'chat-confirm-overlay';

        overlay.innerHTML = `
            <div class="chat-confirm-dialog chat-prompt-dialog">
                <h4>${escapeHtml(title)}</h4>
                ${message ? `<p>${escapeHtml(message)}</p>` : ''}
                <textarea class="chat-prompt-input" rows="3" placeholder="${escapeHtml(placeholder)}">${escapeHtml(defaultValue)}</textarea>
                <div class="chat-confirm-actions">
                    <button type="button" class="btn-cancel">${escapeHtml(cancelText)}</button>
                    <button type="button" class="btn-confirm">${escapeHtml(confirmText)}</button>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);

        const textarea = overlay.querySelector('.chat-prompt-input');
        textarea.focus();
        textarea.setSelectionRange(textarea.value.length, textarea.value.length);

        const cleanup = () => {
            overlay.classList.add('hide');
            setTimeout(() => overlay.remove(), 120);
        };

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) cleanup();
        });
        overlay.querySelector('.btn-cancel').addEventListener('click', cleanup);
        overlay.querySelector('.btn-confirm').addEventListener('click', () => {
            const value = textarea.value.trim();
            if (!value) {
                textarea.focus();
                return;
            }
            cleanup();
            if (typeof onConfirm === 'function') onConfirm(value);
        });
    }

    function editMessage(messageId) {
        const messageData = messageStore.get(String(messageId)) || getMessageFromDom(messageId);
        if (!messageData || messageData.is_deleted) return;

        const currentText = (messageData.message || '').trim();

        showPromptDialog({
            title: 'Edit Pesan',
            message: '',
            placeholder: 'Tulis pesan...',
            defaultValue: currentText,
            confirmText: 'Simpan',
            cancelText: 'Batal',
            onConfirm: (value) => {
                axios.put(`${API_BASE}/messages/${messageId}`, { message: value })
                    .then(response => {
                        const updatedMessage = response?.data?.message;
                        if (updatedMessage) {
                            updateMessageRealtime(updatedMessage, { appendIfMissing: true });
                        }
                    })
                    .catch(error => {
                        const errMsg = error.response?.data?.error || '';
                        const expired = error.response?.status === 403 || /expired|window/i.test(errMsg);
                        showErrorDialog({
                            title: expired ? 'Tidak Bisa Diedit' : 'Gagal Edit Pesan',
                            message: expired ? 'Pesan sudah tidak dapat dihapus maupun diedit.' : (errMsg || 'Silakan coba lagi.')
                        });
                    });
            }
        });
    }

    function showConfirmDialog({ title = 'Konfirmasi', message = 'Lanjutkan?', confirmText = 'Hapus', cancelText = 'Batal', onConfirm }) {
        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'chat-confirm-overlay';

        // Modal content
        overlay.innerHTML = `
            <div class="chat-confirm-dialog">
                <h4>${escapeHtml(title)}</h4>
                <p>${escapeHtml(message)}</p>
                <div class="chat-confirm-actions">
                    <button type="button" class="btn-cancel">${escapeHtml(cancelText)}</button>
                    <button type="button" class="btn-confirm">${escapeHtml(confirmText)}</button>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);

        const cleanup = () => {
            overlay.classList.add('hide');
            setTimeout(() => overlay.remove(), 120);
        };

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                cleanup();
            }
        });

        overlay.querySelector('.btn-cancel').addEventListener('click', () => {
            cleanup();
        });

        overlay.querySelector('.btn-confirm').addEventListener('click', () => {
            cleanup();
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
        });
    }

    function showErrorDialog({ title = 'Terjadi Kesalahan', message = 'Silakan coba lagi.' }) {
        const overlay = document.createElement('div');
        overlay.className = 'chat-confirm-overlay';
        overlay.innerHTML = `
            <div class="chat-confirm-dialog chat-error-dialog">
                <h4>${escapeHtml(title)}</h4>
                <p>${escapeHtml(message)}</p>
                <div class="chat-confirm-actions">
                    <button type="button" class="btn-confirm">Tutup</button>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);

        const cleanup = () => {
            overlay.classList.add('hide');
            setTimeout(() => overlay.remove(), 120);
        };

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) cleanup();
        });
        overlay.querySelector('.btn-confirm').addEventListener('click', cleanup);
    }

    function deleteMessage(messageId) {
        showConfirmDialog({
            title: 'Hapus Pesan',
            message: 'Yakin ingin menghapus pesan ini?',
            confirmText: 'Hapus',
            cancelText: 'Batal',
            onConfirm: () => {
                axios.delete(`${API_BASE}/messages/${messageId}`)
                    .then(response => {
                        const updatedMessage = response?.data?.message;
                        if (updatedMessage) {
                            updateMessageRealtime(updatedMessage, { appendIfMissing: true });
                        }
                    })
                    .catch(error => {
                        const errMsg = error.response?.data?.error || '';
                        const expired = error.response?.status === 403 || /expired|window/i.test(errMsg);
                        showErrorDialog({
                            title: expired ? 'Tidak Bisa Dihapus' : 'Gagal Hapus Pesan',
                            message: expired ? 'Pesan sudah tidak dapat dihapus maupun diedit.' : (errMsg || 'Silakan coba lagi.')
                        });
                    });
            }
        });
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Scroll to bottom
    function scrollToBottom() {
        setTimeout(() => {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 100);
    }

    // Send message with optional media
    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const rawMessage = (messageInput.value || '').replace(/\r\n/g, '\n');
        const hasMessage = rawMessage.trim().length > 0;

        // Check if we have message or media
        if (!hasMessage && !selectedMediaFile) return;

        // Use FormData for file upload
        const formData = new FormData();
        formData.append('message', rawMessage);

        if (selectedMediaFile) {
            formData.append('media', selectedMediaFile);
        }

        if (isAdmin) {
            const receiverId = document.getElementById('receiverId').value;
            if (!receiverId) {
                alert('Pilih user terlebih dahulu');
                return;
            }
            formData.append('receiver_id', receiverId);
        }

        sendButton.disabled = true;
        messageInput.value = '';

        const tempId = `pending-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
        appendMessage({
            tempId,
            sender_id: String(userId),
            receiver_id: isAdmin ? document.getElementById('receiverId')?.value : null,
            message: rawMessage,
            created_at: new Date().toISOString(),
            is_read: false,
            chat_type: 'cs',
        }, true);
        scrollToBottom();

        // Clear media preview
        window.clearMediaPreview();

        axios.post('/chat/send', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
            .then(response => {
                if (!replacePendingMessage(tempId, response.data.message)) {
                    appendMessage(response.data.message, false);
                }
                scrollToBottom();

                if (isAdmin && window.selectedUserId) {
                    moveUserToTop(window.selectedUserId);
                }
            })
            .catch(error => {
                const pendingNode = chatMessages.querySelector(`[data-temp-id="${tempId}"]`);
                if (pendingNode) pendingNode.remove();
                console.error('Send error:', error);
                showErrorDialog({
                    title: 'Gagal Mengirim Pesan',
                    message: error.response?.data?.error || 'Silakan coba lagi.'
                });
            })
            .finally(() => {
                sendButton.disabled = false;
                messageInput.focus();
                if (messageInput.tagName === 'TEXTAREA') {
                    autoResizeMessageInput();
                }
            });
    });

    chatMessages.addEventListener('click', function (event) {
        const editBtn = event.target.closest('.js-edit-message');
        if (editBtn) {
            editMessage(editBtn.dataset.messageId);
            return;
        }

        const deleteBtn = event.target.closest('.js-delete-message');
        if (deleteBtn) {
            deleteMessage(deleteBtn.dataset.messageId);
        }
    });

    // Setup WebSocket listener with retry mechanism
    function setupWebSocketListener() {
        if (!window.Echo) {
            setTimeout(setupWebSocketListener, 100);
            return;
        }

        if (!userId) {
            console.error('[CSChat] Missing userId, skip websocket subscription');
            return;
        }

        const channel = `chat.${userId}`;
        const privateChannel = window.Echo.private(channel);
        const wsConnection = window.Echo?.connector?.pusher?.connection;

        if (wsConnection) {
            const markConnected = () => {
                isSocketConnected = true;
            };
            const markDisconnected = () => {
                isSocketConnected = false;
                refreshMessagesFallback();
                scheduleSocketReconnect();
            };

            wsConnection.bind('connected', markConnected);
            wsConnection.bind('disconnected', markDisconnected);
            wsConnection.bind('unavailable', markDisconnected);
            wsConnection.bind('error', markDisconnected);

            isSocketConnected = wsConnection.state === 'connected';
        } else {
            isSocketConnected = false;
        }

        privateChannel
            .listen('MessageSent', (e) => {
                processIncomingMessage(e);
            })
            .listen('.MessageSent', (e) => {
                processIncomingMessage(e);
            })
            .listen('MessageRead', (e) => {
                updateMessageReadStatus(e.message_ids);
            })
            .listen('.MessageRead', (e) => {
                updateMessageReadStatus(e.message_ids);
            })
            .listen('.message.read', (e) => {
                updateMessageReadStatus(e.message_ids);
            })
            .listen('MessageUpdated', (e) => {
                processMessageUpdated(e);
            })
            .listen('.MessageUpdated', (e) => {
                processMessageUpdated(e);
            });

        function processIncomingMessage(e) {
            // Only process CS chat messages (ignore admin/billing chat)
            const chatType = e.chat_type || 'cs';
            if (chatType !== 'cs') {
                return; // Ignore non-CS messages
            }

            const currentUserId = String(userId);
            const eventSenderId = String(e.sender_id);
            const eventReceiverId = String(e.receiver_id);

            // Don't process messages sent by ourselves
            if (eventSenderId === currentUserId) {
                return;
            }

            if (isAdmin) {
                // For CS admin: only process messages FROM customers (not from other CS/admin)
                // Check if sender is a customer by seeing if receiver is an admin
                const selectedUserId = String(window.selectedUserId || '');

                // Always move customer to top and update badge when customer sends message
                moveUserToTop(e.sender_id);
                
                // Play sound for any incoming customer message
                playNotificationSound();

                if (!window.selectedUserId || selectedUserId !== eventSenderId) {
                    // Not viewing this customer's chat - update badge
                    updateUnreadBadge(e.sender_id);
                } else {
                    // Currently viewing this customer's chat - show message
                    appendMessage(e, false);
                    scrollToBottom();
                    axios.post(`/chat/mark-read/${e.sender_id}`)
                        .catch(err => { });
                }
            } else {
                // For customer: show all incoming CS messages
                appendMessage(e, false);
                scrollToBottom();
                playNotificationSound();
                axios.post(`/chat/mark-read/${e.sender_id}`)
                    .catch(err => { });
            }
        }

        function processMessageUpdated(e) {
            if (!e || !e.message) return;
            const payload = e.message;
            const chatType = payload.chat_type || 'cs';
            if (chatType !== 'cs') return;

            const senderId = String(payload.sender_id || '');
            const receiverId = String(payload.receiver_id || '');
            const currentUserId = String(userId || '');

            if (isAdmin) {
                const selectedUserId = String(window.selectedUserId || '');
                if (!selectedUserId) return;

                if (selectedUserId === senderId || selectedUserId === receiverId) {
                    updateMessageRealtime(payload, { appendIfMissing: true });
                }
            } else if (senderId === currentUserId || receiverId === currentUserId) {
                updateMessageRealtime(payload, { appendIfMissing: true });
            }
        }
    }

    // Start WebSocket listener
    setupWebSocketListener();
    ensureFallbackPolling();

    // Load unread counts on page load (for admin)
    if (isAdmin) {
        loadUnreadCounts();
    }

    // Admin specific: Handle user selection
    if (isAdmin) {
        const userItems = document.querySelectorAll('.user-item');
        const chatTitle = document.getElementById('chatTitle');
        const chatAvatar = document.getElementById('chatAvatar');
        const chatActions = document.getElementById('chatActions');
        const chatInputContainer = document.getElementById('chatInputContainer');
        const receiverIdInput = document.getElementById('receiverId');
        const tabButtons = document.querySelectorAll('.tab-button');

        tabButtons.forEach(button => {
            button.addEventListener('click', function () {
                tabButtons.forEach(tab => tab.classList.remove('active'));
                this.classList.add('active');
                userFilterState.mode = this.dataset.filter === 'unread' ? 'unread' : 'all';
                applyUserFilter();
            });
        });

        userItems.forEach(item => {
            item.addEventListener('click', function () {
                // Remove active class from all
                userItems.forEach(u => u.classList.remove('active'));

                // Add active class to clicked
                this.classList.add('active');

                // Get user info
                const targetUserId = this.dataset.userId;
                const userName = this.dataset.userName;

                // Update global variable
                window.selectedUserId = targetUserId;

                // Clear unread badge
                clearUnreadBadge(targetUserId);

                // Update UI
                chatTitle.textContent = userName;
                chatAvatar.style.display = 'flex';
                chatAvatar.innerHTML = getInitials(userName);
                chatActions.style.display = 'flex';
                receiverIdInput.value = targetUserId;
                chatInputContainer.style.display = 'block';

                // Load messages for this user
                loadMessages(targetUserId, { autoScroll: true, resetSignature: true });

                axios.post(`/chat/mark-read/${targetUserId}`)
                    .catch(err => { });
            });
        });

        applyUserFilter();
    } else {
        // User: Load messages immediately
        loadMessages(null, { autoScroll: true, resetSignature: true });
    }

    // Search functionality for admin
    if (isAdmin) {
        const searchInput = document.querySelector('.chat-search-input');
        if (searchInput) {
            let clearButton = null;

            // Add clear button first
            const searchWrapper = searchInput.parentElement;
            if (searchWrapper) {
                clearButton = document.createElement('button');
                clearButton.className = 'search-clear-btn';
                clearButton.type = 'button';
                clearButton.innerHTML = '<i class="fas fa-times"></i>';
                clearButton.style.cssText = `
                    position: absolute;
                    right: 14px;
                    top: 50%;
                    transform: translateY(-50%);
                    background: none;
                    border: none;
                    color: #94a3b8;
                    cursor: pointer;
                    font-size: 14px;
                    padding: 6px;
                    display: none;
                    transition: all 0.2s;
                    z-index: 10;
                `;

                clearButton.addEventListener('mouseenter', function () {
                    this.style.color = '#ef4444';
                    this.style.transform = 'translateY(-50%) scale(1.1)';
                });

                clearButton.addEventListener('mouseleave', function () {
                    this.style.color = '#94a3b8';
                    this.style.transform = 'translateY(-50%) scale(1)';
                });

                clearButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    searchInput.value = '';
                    const event = new Event('input', { bubbles: true });
                    searchInput.dispatchEvent(event);
                    searchInput.focus();
                });

                searchWrapper.appendChild(clearButton);
            }

            // Real-time search
            searchInput.addEventListener('input', function (e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                userFilterState.search = searchTerm;
                applyUserFilter();

                // Update clear button visibility
                if (clearButton) {
                    clearButton.style.display = searchTerm ? 'block' : 'none';
                }
            });

            // Prevent form submission
            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                }
            });
        }
    }
    };

    waitForAxiosThenInit();
});
