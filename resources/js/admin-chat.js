// Admin Chat functionality - Uses /admin-chat/ endpoints for billing chat
// This is separate from CS chat (/chat/ endpoints)

document.addEventListener('DOMContentLoaded', function () {
    // Wait for axios to be available
    if (typeof window.axios === 'undefined') {
        console.error('[AdminChat] axios is not available. Skip chat init to avoid reload loop.');
        const fallbackContainer = document.getElementById('chatMessages');
        if (fallbackContainer) {
            fallbackContainer.innerHTML = `
                <div class="no-chat-selected">
                    <i class="fas fa-circle-exclamation no-chat-icon" style="color:#ef4444;"></i>
                    <div class="no-chat-text">Gagal memuat modul chat</div>
                    <div class="no-chat-subtext">Silakan refresh halaman. Jika masih terjadi, cek pemuatan asset JS.</div>
                </div>
            `;
        }
        return;
    }

    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    const mediaInput = document.getElementById('mediaInput');
    const attachButton = document.getElementById('attachButton');
    const mediaPreview = document.getElementById('mediaPreview');
    const quickReplies = document.getElementById('quickReplies');
    const editMessageModal = document.getElementById('editMessageModal');
    const editMessageForm = document.getElementById('editMessageForm');
    const editMessageInput = document.getElementById('editMessageInput');
    const editMessageError = document.getElementById('editMessageError');
    const editMessageSave = document.getElementById('editMessageSave');
    const editMessageCancel = document.getElementById('editMessageCancel');
    const editMessageModalClose = document.getElementById('editMessageModalClose');
    const deleteMessageModal = document.getElementById('deleteMessageModal');
    const deleteMessageConfirm = document.getElementById('deleteMessageConfirm');
    const deleteMessageCancel = document.getElementById('deleteMessageCancel');
    const deleteMessageModalClose = document.getElementById('deleteMessageModalClose');
    const adminChatContainer = document.querySelector('.admin-chat-container');
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const sidebarToggleText = document.getElementById('sidebarToggleText');
    const MESSAGE_EDIT_WINDOW_MS = 15 * 60 * 1000;
    const messageStore = new Map();

    let selectedMediaFile = null;
    let activeEditMessageId = null;
    let activeDeleteMessageId = null;

    function initSidebarToggle() {
        if (!adminChatContainer || !sidebarToggleBtn) return;

        const storageKey = 'adminBillingSidebarMinimized';
        const safeGetState = () => {
            try {
                return localStorage.getItem(storageKey) === '1';
            } catch (_) {
                return false;
            }
        };
        const safeSetState = (isMinimized) => {
            try {
                localStorage.setItem(storageKey, isMinimized ? '1' : '0');
            } catch (_) { }
        };

        const applyState = (isMinimized) => {
            adminChatContainer.classList.toggle('sidebar-minimized', isMinimized);
            if (sidebarToggleText) {
                sidebarToggleText.textContent = isMinimized ? 'Tampilkan Sidebar' : 'Lebarkan Chat';
            }

            const icon = sidebarToggleBtn.querySelector('i');
            if (icon) {
                icon.className = isMinimized ? 'fas fa-compress-alt' : 'fas fa-expand-alt';
            }
        };

        applyState(safeGetState());

        sidebarToggleBtn.addEventListener('click', function () {
            const nextState = !adminChatContainer.classList.contains('sidebar-minimized');
            applyState(nextState);
            safeSetState(nextState);
        });
    }
    
    // Sound system for admin chat
    let audioUnlocked = false;
    let preloadedAudio = null;
    
    function initSound() {
        const unlockAudioContext = () => {
            if (audioUnlocked) return;

            if (!preloadedAudio) {
                preloadedAudio = new Audio('/sounds/42289.mp3');
                preloadedAudio.volume = 0.5;
            }
            
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
    
    function playNotificationSound() {
        if (!audioUnlocked) return;
        const now = Date.now();
        if (window.__lastSound && now - window.__lastSound < 1200) {
            return; // throttle to avoid continuous sound
        }
        window.__lastSound = now;
        
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
    
    // Initialize sound system during idle time to keep first render fast.
    if (typeof window.requestIdleCallback === 'function') {
        window.requestIdleCallback(() => initSound(), { timeout: 2000 });
    } else {
        setTimeout(() => initSound(), 800);
    }

    if (!chatMessages || !chatForm) {
        return;
    }

    initSidebarToggle();

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
                    <i class="fas fa-video" style="font-size: 24px; color: #10b981;"></i>
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

    const hasReceiverField = !!document.getElementById('receiverId');
    const isAdmin = window.isAdmin === true && hasReceiverField;
    const userId = window.userId;
    const PINNED_STORAGE_KEY = `adminBillingPinnedChats:${String(userId || 'anonymous')}`;
    const HIDDEN_STORAGE_KEY = `adminBillingHiddenChats:${String(userId || 'anonymous')}`;

    // API Base URLs - KEY DIFFERENCE: Uses /admin-chat/ instead of /chat/
    const API_BASE = '/admin-chat';
    const INITIAL_LOAD_LIMIT = 150;
    let pinnedChats = new Set();
    let hiddenChats = new Set();

    const QUICK_REPLY_TEMPLATES = [
        'Tagihan Anda sudah terbit. Silakan lakukan pembayaran sebelum jatuh tempo agar layanan tetap aktif.',
        'Silakan kirim bukti pembayaran (foto/screenshot) agar kami bantu verifikasi lebih cepat.',
        'Terima kasih, pembayaran Anda sedang kami proses. Mohon tunggu beberapa saat.',
        'Terima kasih sudah menghubungi Admin Billing. Jika ada kendala, kami siap bantu.',
        'Untuk kendala teknis layanan, silakan lanjutkan ke CS agar ditangani lebih cepat.'
    ];

    // ===== Broadcast UI (admin only) =====
    if (isAdmin) {
        initSidebarBroadcast();
    }

    function applyQuickReplyByIndex(index) {
        const text = QUICK_REPLY_TEMPLATES[index];
        if (!text || !messageInput) return;
        messageInput.value = text;
        messageInput.focus();
        messageInput.setSelectionRange(messageInput.value.length, messageInput.value.length);
    }

    if (isAdmin && quickReplies) {
        quickReplies.style.display = 'flex';

        quickReplies.addEventListener('click', function (event) {
            const button = event.target.closest('.quick-reply-chip');
            if (!button) return;
            const index = parseInt(button.dataset.replyIndex || '', 10);
            if (!Number.isNaN(index)) {
                applyQuickReplyByIndex(index);
            }
        });

        document.addEventListener('keydown', function (event) {
            if (!event.altKey || !window.selectedUserId) return;
            const key = parseInt(event.key, 10);
            if (Number.isNaN(key) || key < 1 || key > QUICK_REPLY_TEMPLATES.length) return;
            event.preventDefault();
            applyQuickReplyByIndex(key - 1);
        });
    }

    function initSidebarBroadcast() {
        const panel = document.getElementById('broadcastPanel');
        const toggle = document.getElementById('toggleBroadcast');
        const form = document.getElementById('bcFormSidebar');
        const typeEl = document.getElementById('bcTypeSidebar');
        const variantEl = document.getElementById('bcVariantSidebar');
        const messageEl = document.getElementById('bcMessageSidebar');
        const sendBtn = document.getElementById('bcSendSidebar');
        const statusEl = document.getElementById('bcStatusSidebar');
        const progressBox = document.getElementById('bcProgressSidebar');
        const progressText = document.getElementById('bcProgressTextSidebar');
        const progressPct = document.getElementById('bcProgressPctSidebar');
        const progressBar = document.getElementById('bcProgressBarSidebar');

        if (!panel || !toggle || !form || !typeEl || !variantEl || !messageEl || !sendBtn) {
            return;
        }

        let pollTimer = null;
        let total = 0;

        function setBroadcastVisible(visible) {
            panel.style.display = visible ? 'block' : 'none';
            toggle.textContent = visible ? 'Sembunyikan' : 'Tampilkan';
        }

        // default: hidden, supaya list pelanggan lebih luas
        setBroadcastVisible(false);

        toggle.addEventListener('click', function () {
            const visible = panel.style.display === 'none';
            setBroadcastVisible(visible);
        });

        function syncVariantVisibility() {
            const variantField = variantEl.closest('.broadcast-field');
            const showVariant = typeEl.value === 'greeting';
            if (variantField) {
                variantField.style.display = showVariant ? 'block' : 'none';
            } else {
                variantEl.style.display = showVariant ? 'block' : 'none';
            }
        }

        typeEl.addEventListener('change', syncVariantVisibility);
        syncVariantVisibility();

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const payload = { type: typeEl.value };
            if (payload.type === 'greeting') payload.variant = variantEl.value;
            if (messageEl.value.trim()) payload.message = messageEl.value.trim();

            if (payload.type === 'custom' && !payload.message) {
                alert('Isi pesan wajib untuk custom');
                return;
            }

            sendBtn.disabled = true;
            sendBtn.textContent = 'Mengirim...';
            if (statusEl) statusEl.style.display = 'none';
            if (progressBox) progressBox.style.display = 'block';
            updateProgress(0, total);

            axios.post('/admin-chat/broadcast', payload)
                .then(res => {
                    total = res.data.total || 0;
                    updateProgress(0, total);
                    if (statusEl) {
                        statusEl.textContent = `Broadcast dimulai (${total} pelanggan)`;
                        statusEl.style.display = 'inline-flex';
                    }
                    if (res.data.broadcast_id) {
                        startPoll(res.data.broadcast_id, total);
                    }
                })
                .catch(err => {
                    alert(err.response?.data?.error || 'Gagal kirim broadcast');
                    if (progressBox) progressBox.style.display = 'none';
                })
                .finally(() => {
                    sendBtn.disabled = false;
                    sendBtn.textContent = 'Kirim ke semua pelanggan';
                });
        });

        function startPoll(broadcastId, fallbackTotal) {
            stopPoll();
            pollTimer = setInterval(() => {
                axios.get(`/admin-chat/broadcast/${broadcastId}/progress`)
                    .then(res => {
                        const data = res.data || {};
                        const safeTotal = data.total || total || fallbackTotal || 0;
                        updateProgress(data.done || 0, safeTotal);

                        if (data.status === 'completed') {
                            stopPoll();
                            if (statusEl) {
                                statusEl.textContent = `Selesai ${data.done}/${safeTotal}`;
                                statusEl.style.display = 'inline-flex';
                            }
                        } else if (data.status === 'failed') {
                            stopPoll();
                            if (statusEl) {
                                statusEl.textContent = `Gagal: ${data.error || 'unknown'}`;
                                statusEl.style.display = 'inline-flex';
                            }
                        }
                    })
                    .catch(() => {
                        stopPoll();
                        if (statusEl) {
                            statusEl.textContent = 'Gagal memantau progress';
                            statusEl.style.display = 'inline-flex';
                        }
                    });
            }, 2000);
        }

        function stopPoll() {
            if (pollTimer) {
                clearInterval(pollTimer);
                pollTimer = null;
            }
        }

        function updateProgress(done, totalCount) {
            if (!progressText || !progressPct || !progressBar) return;

            const hasTotal = totalCount > 0;
            const percent = hasTotal ? Math.floor((done / totalCount) * 100) : 0;
            progressText.textContent = hasTotal ? `${done}/${totalCount} dikirim` : `${done} dikirim...`;
            progressPct.textContent = hasTotal ? `${percent}%` : '...';
            progressBar.style.width = hasTotal ? `${Math.min(percent, 100)}%` : '0%';
        }
    }

    function setUnreadBadgeCount(senderId, rawCount) {
        const count = Math.max(0, parseInt(rawCount, 10) || 0);
        const badge = document.getElementById(`unread-${senderId}`);
        if (badge) {
            if (count > 0) {
                badge.textContent = String(count);
                badge.style.display = 'inline-block';
            } else {
                badge.textContent = '0';
                badge.style.display = 'none';
            }
        }

        const userItem = document.querySelector(`.user-item[data-user-id="${senderId}"]`);
        if (userItem) userItem.dataset.unread = String(count);
    }

    // Load unread counts for admin on page load
    function loadUnreadCounts() {
        if (!isAdmin) return;

        axios.get(`${API_BASE}/unread-count`)
            .then(response => {
                const unreadCounts = response.data || {};
                document.querySelectorAll('.user-item').forEach(item => {
                    const id = item.dataset.userId;
                    if (id) setUnreadBadgeCount(id, unreadCounts[id] || 0);
                });
                applyUserFilter();
            })
            .catch(error => { });
    }

    // Function to get initials from name
    function getInitials(name) {
        return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
    }

    // WhatsApp-style date formatting
    let lastDisplayedDate = null;

    function formatWhatsAppDate(dateString) {
        const date = new Date(dateString);
        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);

        const dateOnly = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        const todayOnly = new Date(today.getFullYear(), today.getMonth(), today.getDate());

        const diffTime = todayOnly - dateOnly;
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays === 0) {
            return 'Hari ini';
        } else if (diffDays === 1) {
            return 'Kemarin';
        } else if (diffDays < 7) {
            const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            return dayNames[date.getDay()];
        } else {
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'numeric', year: 'numeric' });
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
        return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
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

    // Load messages - Uses /admin-chat/messages endpoint
    function loadMessages(targetUserId = null) {
        const query = `limit=${encodeURIComponent(String(INITIAL_LOAD_LIMIT))}`;
        const url = isAdmin && targetUserId
            ? `${API_BASE}/messages/${targetUserId}?${query}`
            : `${API_BASE}/messages?${query}`;

        axios.get(url)
            .then(response => {
                displayMessages(response.data);
                scrollToBottom();

                // For customers, mark admin messages as read
                if (!isAdmin) {
                    const unreadAdminMessages = response.data.filter(m =>
                        String(m.sender_id) !== String(userId) && !m.is_read
                    );

                    if (unreadAdminMessages.length > 0) {
                        const adminId = unreadAdminMessages[0].sender_id;
                        axios.post(`${API_BASE}/mark-read/${adminId}`)
                            .catch(err => { });
                    }
                }
            })
            .catch(error => { });
    }

    // Display messages
    function displayMessages(messages) {
        chatMessages.innerHTML = '';
        lastDisplayedDate = null;
        messageStore.clear();

        if (!Array.isArray(messages) || messages.length === 0) {
            chatMessages.innerHTML = `
                <div class="no-chat-selected">
                    <i class="fas fa-inbox no-chat-icon" style="font-size: 48px; color: #10b981; margin-bottom: 16px;"></i>
                    <div class="no-chat-text" style="color: #10b981;">Belum ada pesan pembayaran</div>
                    <div class="no-chat-subtext">Mulai percakapan dengan mengirim pesan tentang pembayaran</div>
                </div>
            `;
            return;
        }

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

        normalized.chat_type = normalized.chat_type || 'admin';
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
                    <span class="message-status-text pending">Menunggu</span>
                </span>
            `;
        }

        if (message.is_read) {
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
                    </video>
                </div>
            `;
        }

        return '';
    }

    function renderMessageText(message) {
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
        if (!message || !message.created_at || message.is_deleted) return false;
        const createdAt = new Date(message.created_at).getTime();
        if (Number.isNaN(createdAt)) return false;
        return (Date.now() - createdAt) <= MESSAGE_EDIT_WINDOW_MS;
    }

    function renderMessageActions(message, isSent) {
        if (!isAdmin) {
            return '';
        }

        const canManage = isSent && canManageMessage(message) && message.id;
        if (!canManage) {
            return '';
        }

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

        messageDiv.dataset.messageId = message.id ? String(message.id) : '';
        messageDiv.dataset.createdAt = message.created_at || '';
        bubble.innerHTML = renderMessageContent(message, isSent, isPending);
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

    // Append single message
    function appendMessage(message, isPending = false, _isLoading = false) {
        const normalized = cacheMessage(message);
        if (!normalized) return;

        if (normalized.id) {
            const existingMessage = chatMessages.querySelector(`[data-message-id="${normalized.id}"]`);
            if (existingMessage) {
                patchMessageElement(existingMessage, normalized, isPending);
                return;
            }
        }

        if (normalized.created_at && shouldShowDateDivider(normalized.created_at)) {
            chatMessages.appendChild(createDateDivider(normalized.created_at));
        }

        const messageDiv = document.createElement('div');
        const currentUserId = String(userId);
        const messageSenderId = String(normalized.sender_id);
        const isSent = messageSenderId === currentUserId;
        const senderName = normalized.sender ? normalized.sender.name : 'Unknown';
        const initials = getInitials(senderName);

        messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
        if (normalized.id) {
            messageDiv.dataset.messageId = normalized.id;
        }
        messageDiv.dataset.createdAt = normalized.created_at || '';

        messageDiv.innerHTML = `
            <div class="message-avatar">${initials}</div>
            <div class="message-bubble">
                ${renderMessageContent(normalized, isSent, isPending)}
            </div>
        `;

        chatMessages.appendChild(messageDiv);
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

    function showModal(modal) {
        if (!modal) return;
        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function hideModal(modal) {
        if (!modal) return;
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');

        const anotherOpen = (editMessageModal && editMessageModal.classList.contains('show')) ||
            (deleteMessageModal && deleteMessageModal.classList.contains('show'));
        if (!anotherOpen) {
            document.body.style.overflow = '';
        }
    }

    function openEditModal(messageId) {
        if (!editMessageModal || !editMessageInput) return;
        const messageData = messageStore.get(String(messageId)) || getMessageFromDom(messageId);
        if (!messageData || messageData.is_deleted) return;

        activeEditMessageId = String(messageId);
        editMessageInput.value = messageData.message || '';
        if (editMessageError) {
            editMessageError.style.display = 'none';
            editMessageError.textContent = '';
        }
        showModal(editMessageModal);

        setTimeout(() => {
            editMessageInput.focus();
            editMessageInput.setSelectionRange(editMessageInput.value.length, editMessageInput.value.length);
        }, 50);
    }

    function closeEditModal() {
        activeEditMessageId = null;
        if (editMessageForm) {
            editMessageForm.reset();
        }
        if (editMessageError) {
            editMessageError.style.display = 'none';
            editMessageError.textContent = '';
        }
        hideModal(editMessageModal);
    }

    function openDeleteModal(messageId) {
        if (!deleteMessageModal) return;
        activeDeleteMessageId = String(messageId);
        showModal(deleteMessageModal);
    }

    function closeDeleteModal() {
        activeDeleteMessageId = null;
        hideModal(deleteMessageModal);
    }

    if (editMessageForm) {
        editMessageForm.addEventListener('submit', function (event) {
            event.preventDefault();
            if (!activeEditMessageId) return;

            const text = (editMessageInput.value || '').trim();
            if (!text) {
                if (editMessageError) {
                    editMessageError.textContent = 'Pesan tidak boleh kosong.';
                    editMessageError.style.display = 'block';
                }
                return;
            }

            if (editMessageSave) {
                editMessageSave.disabled = true;
                editMessageSave.textContent = 'Menyimpan...';
            }

            axios.put(`${API_BASE}/messages/${activeEditMessageId}`, { message: text })
                .then(response => {
                    const updatedMessage = response?.data?.message;
                    if (updatedMessage) {
                        updateMessageRealtime(updatedMessage, { appendIfMissing: true });
                    }
                    closeEditModal();
                })
                .catch(err => {
                    if (editMessageError) {
                        editMessageError.textContent = err.response?.data?.error || 'Gagal edit pesan';
                        editMessageError.style.display = 'block';
                    }
                })
                .finally(() => {
                    if (editMessageSave) {
                        editMessageSave.disabled = false;
                        editMessageSave.textContent = 'Simpan perubahan';
                    }
                });
        });
    }

    if (editMessageCancel) {
        editMessageCancel.addEventListener('click', closeEditModal);
    }
    if (editMessageModalClose) {
        editMessageModalClose.addEventListener('click', closeEditModal);
    }
    if (editMessageModal) {
        editMessageModal.addEventListener('click', function (event) {
            if (event.target === editMessageModal) {
                closeEditModal();
            }
        });
    }

    if (deleteMessageConfirm) {
        deleteMessageConfirm.addEventListener('click', function () {
            if (!activeDeleteMessageId) return;

            deleteMessageConfirm.disabled = true;
            deleteMessageConfirm.textContent = 'Menghapus...';

            axios.delete(`${API_BASE}/messages/${activeDeleteMessageId}`)
                .then(response => {
                    const updatedMessage = response?.data?.message;
                    if (updatedMessage) {
                        updateMessageRealtime(updatedMessage, { appendIfMissing: true });
                    }
                    closeDeleteModal();
                })
                .catch(err => {
                    alert(err.response?.data?.error || 'Gagal hapus pesan');
                })
                .finally(() => {
                    deleteMessageConfirm.disabled = false;
                    deleteMessageConfirm.textContent = 'Hapus pesan';
                });
        });
    }

    if (deleteMessageCancel) {
        deleteMessageCancel.addEventListener('click', closeDeleteModal);
    }
    if (deleteMessageModalClose) {
        deleteMessageModalClose.addEventListener('click', closeDeleteModal);
    }
    if (deleteMessageModal) {
        deleteMessageModal.addEventListener('click', function (event) {
            if (event.target === deleteMessageModal) {
                closeDeleteModal();
            }
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;
        if (editMessageModal && editMessageModal.classList.contains('show')) {
            closeEditModal();
            return;
        }
        if (deleteMessageModal && deleteMessageModal.classList.contains('show')) {
            closeDeleteModal();
        }
    });

    chatMessages.addEventListener('click', function (event) {
        const editBtn = event.target.closest('.js-edit-message');
        if (editBtn) {
            openEditModal(editBtn.dataset.messageId);
            return;
        }

        const deleteBtn = event.target.closest('.js-delete-message');
        if (deleteBtn) {
            openDeleteModal(deleteBtn.dataset.messageId);
        }
    });

    function updateUnreadBadge(senderId) {
        const userItem = document.querySelector(`.user-item[data-user-id="${senderId}"]`);
        const badge = document.getElementById(`unread-${senderId}`);
        const currentCount = parseInt(userItem?.dataset?.unread || badge?.textContent || '0', 10) || 0;
        setUnreadBadgeCount(senderId, currentCount + 1);
        applyUserFilter();
    }

    function loadPinnedChatsFromStorage() {
        try {
            const raw = localStorage.getItem(PINNED_STORAGE_KEY);
            if (!raw) return new Set();
            const parsed = JSON.parse(raw);
            if (!Array.isArray(parsed)) return new Set();
            return new Set(parsed.map(value => String(value)));
        } catch (_) {
            return new Set();
        }
    }

    function loadHiddenChatsFromStorage() {
        try {
            const raw = localStorage.getItem(HIDDEN_STORAGE_KEY);
            if (!raw) return new Set();
            const parsed = JSON.parse(raw);
            if (!Array.isArray(parsed)) return new Set();
            return new Set(parsed.map(value => String(value)));
        } catch (_) {
            return new Set();
        }
    }

    function savePinnedChatsToStorage() {
        try {
            localStorage.setItem(PINNED_STORAGE_KEY, JSON.stringify(Array.from(pinnedChats)));
        } catch (_) { }
    }

    function saveHiddenChatsToStorage() {
        try {
            localStorage.setItem(HIDDEN_STORAGE_KEY, JSON.stringify(Array.from(hiddenChats)));
        } catch (_) { }
    }

    function setPinnedVisualState(userItem, isPinned) {
        if (!userItem) return;
        userItem.dataset.pinned = isPinned ? '1' : '0';
        userItem.classList.toggle('pinned', isPinned);
    }

    function setHiddenVisualState(userItem, isHidden) {
        if (!userItem) return;
        userItem.dataset.hidden = isHidden ? '1' : '0';
        userItem.classList.toggle('is-hidden', isHidden);
    }

    function hideChatFromList() { /* removed feature */ }
    function unhideChatFromList() { /* removed feature */ }
    function togglePinnedChat() { /* removed feature */ }

    function touchUserActivity(userId) {
        const userList = document.getElementById('userList');
        if (!userList) return;
        const userItem = userList.querySelector(`.user-item[data-user-id="${userId}"]`);
        if (!userItem) return;
        userItem.dataset.lastActivity = String(Date.now());
    }

    function initPinnedChats() {
        if (!isAdmin) return;
        const userList = document.getElementById('userList');
        if (!userList) return;

        const userItems = Array.from(userList.querySelectorAll('.user-item'));
        if (userItems.length === 0) return;

        // Reset pin/hidden state because fitur pin/hide dimatikan sementara
        pinnedChats = new Set();
        hiddenChats = new Set();
        try {
            localStorage.removeItem(PINNED_STORAGE_KEY);
            localStorage.removeItem(HIDDEN_STORAGE_KEY);
        } catch (_) { }

        const baselineActivity = Date.now();
        userItems.forEach((item, index) => {
            if (!item.dataset.lastActivity) {
                item.dataset.lastActivity = String(baselineActivity - index);
            }
            const currentUserId = String(item.dataset.userId || '');
            setPinnedVisualState(item, pinnedChats.has(currentUserId));
            setHiddenVisualState(item, hiddenChats.has(currentUserId));
        });

        // default urutan sudah dari server
    }

    function moveUserToTop(userId) {
        touchUserActivity(userId);
    }

    function clearUnreadBadge(userId) {
        setUnreadBadgeCount(userId, 0);
        applyUserFilter();
    }

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

            const cacheKey = String(messageId);
            const cachedMessage = messageStore.get(cacheKey);
            if (cachedMessage) {
                messageStore.set(cacheKey, {
                    ...cachedMessage,
                    is_read: true,
                });
            }
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function scrollToBottom() {
        setTimeout(() => {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 100);
    }

    let isSendingMessage = false;
    let sendMessageFallbackTimer = null;

    function clearSendFallbackTimer() {
        if (sendMessageFallbackTimer) {
            clearTimeout(sendMessageFallbackTimer);
            sendMessageFallbackTimer = null;
        }
    }

    function startSendFallbackTimer() {
        clearSendFallbackTimer();
        sendMessageFallbackTimer = setTimeout(() => {
            isSendingMessage = false;
            if (sendButton) sendButton.disabled = false;
        }, 15000);
    }

    // Enter-to-send for billing chat input (single line input only)
    if (messageInput && messageInput.tagName === 'INPUT') {
        messageInput.addEventListener('keydown', function (event) {
            if (event.isComposing) return;
            if (event.key !== 'Enter' || event.shiftKey) return;
            event.preventDefault();
            if (typeof chatForm.requestSubmit === 'function') {
                chatForm.requestSubmit();
            } else {
                chatForm.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
            }
        });
    }

    // Send message - Uses /admin-chat/send endpoint
    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();
        if (isSendingMessage) return;

        const message = messageInput.value.trim();
        if (!message && !selectedMediaFile) return;

        const formData = new FormData();
        formData.append('message', message);

        if (selectedMediaFile) {
            formData.append('media', selectedMediaFile);
        }

        if (isAdmin) {
            const receiverId = document.getElementById('receiverId').value;
            if (!receiverId) {
                alert('Pilih pelanggan terlebih dahulu');
                return;
            }
            formData.append('receiver_id', receiverId);
        }

        isSendingMessage = true;
        sendButton.disabled = true;
        startSendFallbackTimer();
        messageInput.value = '';
        window.clearMediaPreview();

        axios.post(`${API_BASE}/send`, formData, {
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
                console.error('Send error:', error);
                alert('Gagal mengirim pesan: ' + (error.response?.data?.error || 'Unknown error'));
            })
            .finally(() => {
                clearSendFallbackTimer();
                isSendingMessage = false;
                sendButton.disabled = false;
                messageInput.focus();
            });
    });

    // WebSocket listener
    function setupWebSocketListener() {
        if (!window.Echo) {
            setTimeout(setupWebSocketListener, 100);
            return;
        }

        if (!userId) {
            console.error('[AdminChat] Missing userId, skip websocket subscription');
            return;
        }

    const channel = `billing-chat.${userId}`;
    const privateChannel = window.Echo.private(channel);

    privateChannel
        // Support both custom broadcastAs event name (.MessageSent) and namespaced fallback.
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
        .listen('MessageUpdated', (e) => {
            processMessageUpdated(e);
        })
        .listen('.MessageUpdated', (e) => {
            processMessageUpdated(e);
        });

        function processIncomingMessage(e) {
            // Only process admin/billing chat messages (strict filter)
            const chatType = e.chat_type || 'cs';
            if (chatType !== 'admin') {
                return; // Ignore non-admin messages (including CS chat)
            }

            const currentUserId = String(userId);
            const eventSenderId = String(e.sender_id);
            // Don't process messages sent by ourselves
            if (eventSenderId === currentUserId) return;

            if (isAdmin) {
                // For billing admin: only process messages FROM customers
                const selectedUserId = String(window.selectedUserId || '');

                // Always move customer to top and play sound when customer sends message
                moveUserToTop(e.sender_id);
                playNotificationSound();

                if (!window.selectedUserId || selectedUserId !== eventSenderId) {
                    // Not viewing this customer's chat - update badge
                    updateUnreadBadge(e.sender_id);
                } else {
                    // Currently viewing this customer's chat - show message
                    appendMessage(e, false);
                    scrollToBottom();
                    axios.post(`${API_BASE}/mark-read/${e.sender_id}`)
                        .catch(err => { });
                }
            } else {
                // For customer: show all incoming billing admin messages
                appendMessage(e, false);
                scrollToBottom();
                playNotificationSound();
                axios.post(`${API_BASE}/mark-read/${e.sender_id}`)
                    .catch(err => { });
            }
        }

        function processMessageUpdated(e) {
            if (!e || !e.message) return;
            const payload = e.message;
            if ((payload.chat_type || 'cs') !== 'admin') return;

            const senderId = String(payload.sender_id || '');
            const receiverId = String(payload.receiver_id || '');

            if (isAdmin) {
                const selectedUserId = String(window.selectedUserId || '');
                if (!selectedUserId) return;

                if (selectedUserId === senderId || selectedUserId === receiverId) {
                    updateMessageRealtime(payload, { appendIfMissing: true });
                }
            } else {
                const currentUserId = String(userId);
                if (senderId === currentUserId || receiverId === currentUserId) {
                    updateMessageRealtime(payload, { appendIfMissing: true });
                }
            }
        }
    }

    setupWebSocketListener();

    if (isAdmin) {
        loadUnreadCounts();
    }

    // Admin specific: Handle user selection
    if (isAdmin) {
        initPinnedChats();

        const userList = document.getElementById('userList');
        const chatTitle = document.getElementById('chatTitle');
        const chatAvatar = document.getElementById('chatAvatar');
        const chatSubtitle = document.getElementById('chatSubtitle');
        const chatInputContainer = document.getElementById('chatInputContainer');
        const receiverIdInput = document.getElementById('receiverId');
        const getVisibleUserItems = () => userList ? userList.querySelectorAll('.user-item') : [];

        function openUserChatByItem(item) {
            if (!item) return;

            getVisibleUserItems().forEach(u => u.classList.remove('active'));
            item.classList.add('active');

            const targetUserId = item.dataset.userId;
            const userName = item.dataset.userName;

            window.selectedUserId = targetUserId;
            clearUnreadBadge(targetUserId);
            touchUserActivity(targetUserId);

            if (chatTitle) {
                chatTitle.textContent = userName;
            }
            if (chatAvatar) {
                chatAvatar.style.display = 'flex';
                chatAvatar.innerHTML = getInitials(userName);
            }
            if (chatSubtitle) chatSubtitle.style.display = 'block';
            if (receiverIdInput) receiverIdInput.value = targetUserId;
            if (chatInputContainer) chatInputContainer.style.display = 'block';

            loadMessages(targetUserId);

            axios.post(`${API_BASE}/mark-read/${targetUserId}`)
                .catch(err => { });
        }

        function resetSelectedChatPanel() {
            window.selectedUserId = null;
            if (receiverIdInput) {
                receiverIdInput.value = '';
            }
            getVisibleUserItems().forEach(u => u.classList.remove('active'));

            if (chatTitle) {
                chatTitle.textContent = 'Pilih pelanggan untuk memulai chat';
            }
            if (chatAvatar) {
                chatAvatar.style.display = 'none';
                chatAvatar.innerHTML = '<i class="fas fa-user"></i>';
            }
            if (chatSubtitle) {
                chatSubtitle.style.display = 'none';
            }
            if (chatInputContainer) {
                chatInputContainer.style.display = 'none';
            }

            if (chatMessages) {
                chatMessages.innerHTML = `
                    <div class="no-chat-selected">
                        <i class="fas fa-comments-dollar no-chat-icon"></i>
                        <div class="no-chat-text">Chat Pembayaran</div>
                        <div class="no-chat-subtext">Pilih pelanggan dari sidebar untuk memulai percakapan tentang pembayaran</div>
                    </div>
                `;
            }
        }

        if (userList) {
            userList.addEventListener('click', function (event) {
                const userItem = event.target.closest('.user-item');
                if (!userItem || !userList.contains(userItem)) return;
                openUserChatByItem(userItem);
            });

            // Fallback: bind directly in case delegated click is blocked by layout layer.
            userList.querySelectorAll('.user-item').forEach((item) => {
                if (item.dataset.boundOpen === '1') return;
                item.dataset.boundOpen = '1';
                item.addEventListener('click', function () {
                    openUserChatByItem(this);
                });
            });
        }
    } else {
        // Customer: Load messages immediately
        loadMessages();
    }

    // Search functionality for admin
    if (isAdmin) {
        const searchInput = document.querySelector('.chat-search-input');
        if (searchInput) {
            let searchDebounceTimer = null;
            searchInput.addEventListener('input', function (e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                if (searchDebounceTimer) {
                    clearTimeout(searchDebounceTimer);
                }
                searchDebounceTimer = setTimeout(() => {
                    applyUserFilter(searchTerm);
                }, 120);
            });

            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') e.preventDefault();
            });
        }

        // tab filter
        const tabAll = document.getElementById('tabAll');
        const tabUnread = document.getElementById('tabUnread');
        if (tabAll && tabUnread) {
            tabAll.addEventListener('click', () => {
                tabAll.classList.add('active');
                tabUnread.classList.remove('active');
                applyUserFilter(searchInput?.value || '');
            });
            tabUnread.addEventListener('click', () => {
                tabUnread.classList.add('active');
                tabAll.classList.remove('active');
                applyUserFilter(searchInput?.value || '');
            });
        }
    }
});

    // Filter helper: search + tab (all/unread)
    function applyUserFilter(searchTerm = '') {
        const isAdmin = window.isAdmin === true && !!document.getElementById('receiverId');
        if (!isAdmin) return;

        const search = (searchTerm || '').toLowerCase().trim();
        const tabUnreadActive = document.getElementById('tabUnread')?.classList.contains('active');
        const userItems = document.querySelectorAll('.user-item');

        userItems.forEach(item => {
            const userName = (item.dataset.userName || '').toLowerCase();
            const userType = (item.querySelector('.user-type')?.textContent || '').toLowerCase();
            const unread = parseInt(item.dataset.unread || '0', 10);

            let visible = true;
            if (search && !(userName.includes(search) || userType.includes(search))) {
                visible = false;
            }
            if (tabUnreadActive && unread <= 0) {
                visible = false;
        }

        item.style.display = visible ? 'block' : 'none';
    });
}
