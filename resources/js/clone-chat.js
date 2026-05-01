// Chat functionality
document.addEventListener('DOMContentLoaded', function() {
    // Wait for axios to be available
    if (typeof window.axios === 'undefined') {
        setTimeout(() => {
            window.location.reload();
        }, 500);
        return;
    }
    
    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    const mediaInput = document.getElementById('mediaInput');
    const attachButton = document.getElementById('attachButton');
    const mediaPreview = document.getElementById('mediaPreview');
    
    let selectedMediaFile = null;
    
    if (!chatMessages || !chatForm) {
        return;
    }
    
    // Setup attach button click
    if (attachButton && mediaInput) {
        attachButton.addEventListener('click', function(e) {
            e.preventDefault();
            mediaInput.click();
        });
        
        mediaInput.addEventListener('change', function(e) {
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
    window.clearMediaPreview = function() {
        selectedMediaFile = null;
        if (mediaInput) mediaInput.value = '';
        if (mediaPreview) {
            mediaPreview.innerHTML = '';
            mediaPreview.style.display = 'none';
        }
    };
    
    const isAdmin = window.isAdmin || false;
    const userId = window.userId;
    
    // Load unread counts for admin on page load
    function loadUnreadCounts() {
        if (!isAdmin) return;
        
        axios.get('/chat/unread-count')
            .then(response => {
                const unreadCounts = response.data;
                
                // Update all badges
                Object.keys(unreadCounts).forEach(senderId => {
                    const count = unreadCounts[senderId];
                    const badge = document.getElementById(`unread-${senderId}`);
                    
                    if (badge && count > 0) {
                        badge.textContent = count;
                        badge.style.display = 'inline-block';
                    }
                });
            })
            .catch(error => {});
    }
    
    // Function to get initials from name
    function getInitials(name) {
        return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
    }
    
    // Load messages
    function loadMessages(targetUserId = null) {
        const url = isAdmin && targetUserId 
            ? `/chat/messages/${targetUserId}` 
            : '/chat/messages';
        
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
                        // Get admin ID from first admin message
                        const adminId = unreadAdminMessages[0].sender_id;
                        
                        axios.post(`/chat/mark-read/${adminId}`)
                            .catch(err => {});
                    }
                }
            })
            .catch(error => {});
    }
    
    // Display messages
    function displayMessages(messages) {
        chatMessages.innerHTML = '';
        
        if (messages.length === 0) {
            chatMessages.innerHTML = `
                <div class="no-chat-selected">
                    <i class="fas fa-inbox no-chat-icon"></i>
                    <div class="no-chat-text">Belum ada pesan</div>
                    <div class="no-chat-subtext">Mulai percakapan dengan mengirim pesan</div>
                </div>
            `;
            return;
        }
        
        messages.forEach(message => {
            appendMessage(message);
        });
    }
    
    // Append single message
    function appendMessage(message, isPending = false) {
        // Cek duplikat berdasarkan message ID
        if (message.id && chatMessages.querySelector(`[data-message-id="${message.id}"]`)) {
            return;
        }
        
        const messageDiv = document.createElement('div');
        
        // Convert to string for UUID comparison
        const currentUserId = String(userId);
        const messageSenderId = String(message.sender_id);
        const isSent = messageSenderId === currentUserId;
        
        messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
        if (message.id) {
            messageDiv.dataset.messageId = message.id;
        }
        if (message.tempId) {
            messageDiv.dataset.tempId = message.tempId;
        }
        
        const time = new Date(message.created_at).toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        const senderName = message.sender ? message.sender.name : 'Unknown';
        const initials = getInitials(senderName);
        
        // Icon status untuk pesan yang dikirim
        let statusIcon = '';
        if (isSent) {
            if (isPending) {
                statusIcon = '<i class="fas fa-clock message-status"></i>';
            } else if (message.is_read) {
                statusIcon = '<i class="fas fa-check-double message-status read"></i>';
            } else {
                statusIcon = '<i class="fas fa-check message-status"></i>';
            }
        }
        
        // Render media content
        let mediaContent = '';
        if (message.media_url && message.media_type) {
            if (message.media_type === 'image') {
                mediaContent = `
                    <div class="message-media">
                        <img src="${message.media_url}" alt="Image" onclick="window.open('${message.media_url}', '_blank')" style="max-width: 250px; max-height: 200px; border-radius: 8px; cursor: pointer; margin-bottom: 6px;">
                    </div>
                `;
            } else if (message.media_type === 'video') {
                mediaContent = `
                    <div class="message-media">
                        <video controls style="max-width: 250px; max-height: 200px; border-radius: 8px; margin-bottom: 6px;">
                            <source src="${message.media_url}" type="video/mp4">
                            Browser tidak mendukung video.
                        </video>
                    </div>
                `;
            }
        }
        
        // Text content (only show if not empty)
        let textContent = '';
        if (message.message && message.message.trim() !== '') {
            textContent = `<div class="message-text">${escapeHtml(message.message)}</div>`;
        }
        
        messageDiv.innerHTML = `
            <div class="message-avatar">${initials}</div>
            <div class="message-bubble">
                <div class="message-content">
                    ${mediaContent}
                    ${textContent}
                    <div class="message-info">
                        ${statusIcon}
                        ${time}
                    </div>
                </div>
            </div>
        `;
        
        chatMessages.appendChild(messageDiv);
        
        // Hilangkan notifikasi suara - tidak perlu lagi
        // if (!isSent && !isPending) {
        //     playNotificationSound();
        //     showNotification(senderName, message.message);
        // }
    }
    
    // Notification sound
    function playNotificationSound() {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSyCz/LZiTYIGGS57OehUBELTqfj8bllHAU2jdXvzH4yBSh+zPLaizsKFFix6OyrWBQKQ5zd8sFuJAUrhM/y2Ik3CBhiu+zom1ARC0ym4/G5ZBwGNovU78x+MwUoc8zy3Ik2CBVes+jqq1kUCj+Z3PLEcSQFK4PO8tmJNwgZYrnq5p1RDwtMpuPxuWQcBjaM1e/MfjMFJ3DN8tyKOwgUXLPn6qtZFAo/mdzyxHEkBSuDzvLZiTcIGWK56uadUQ8LTKbj8blkHAY2i9XvzH4zBSdwzfLcizsIFF2z5+qrWRQKPpTa8cJvIwQqf87y2oo7CBZguerpnlEPC0ym4/G5ZBsFNYnU8Mx+MwUncMzy34s3CRVdsefrq3oM');
        audio.volume = 0.3;
        audio.play().catch(e => {});
    }
    
    // Show browser notification
    function showNotification(sender, message) {
        if (!('Notification' in window)) return;
        
        if (Notification.permission === 'granted') {
            new Notification(sender, {
                body: message.substring(0, 100),
                icon: '/favicon.ico'
            });
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    new Notification(sender, {
                        body: message.substring(0, 100),
                        icon: '/favicon.ico'
                    });
                }
            });
        }
    }
    
    // Update unread badge untuk admin (reload from server for accuracy)
    function updateUnreadBadge(senderId) {
        const badge = document.getElementById(`unread-${senderId}`);
        if (!badge) return;
        
        // Reload count from server to ensure accuracy
        axios.get('/chat/unread-count')
            .then(response => {
                const unreadCounts = response.data;
                const count = unreadCounts[senderId] || 0;
                
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(error => {
                // Fallback: increment manually
                const currentCount = parseInt(badge.textContent) || 0;
                badge.textContent = currentCount + 1;
                badge.style.display = 'inline-block';
            });
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
    }
    
    // Update message read status (centang 1 -> centang 2)
    function updateMessageReadStatus(messageIds) {
        if (!messageIds || messageIds.length === 0) return;
        
        messageIds.forEach(messageId => {
            const messageDiv = chatMessages.querySelector(`[data-message-id="${messageId}"]`);
            
            if (messageDiv) {
                const statusIcon = messageDiv.querySelector('.message-status');
                
                if (statusIcon) {
                    statusIcon.className = 'fas fa-check-double message-status read';
                }
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
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        
        // Check if we have message or media
        if (!message && !selectedMediaFile) return;
        
        // Use FormData for file upload
        const formData = new FormData();
        formData.append('message', message);
        
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
        
        // Clear media preview
        window.clearMediaPreview();
        
        axios.post('/chat/send', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
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
                sendButton.disabled = false;
                messageInput.focus();
            });
    });
    
    // Setup WebSocket listener with retry mechanism
    function setupWebSocketListener() {
        if (!window.Echo) {
            setTimeout(setupWebSocketListener, 100);
            return;
        }
        
        const channel = `chat.${userId}`;
        const privateChannel = window.Echo.private(channel);
        
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
            });
        
        function processIncomingMessage(e) {
            const currentUserId = String(userId);
            const eventSenderId = String(e.sender_id);
            
            if (eventSenderId === currentUserId) {
                return;
            }
            
            if (isAdmin) {
                const selectedUserId = String(window.selectedUserId);
                
                moveUserToTop(e.sender_id);
                
                if (!window.selectedUserId || selectedUserId !== eventSenderId) {
                    updateUnreadBadge(e.sender_id);
                    
                    // Show notification for admin
                    if (window.chatNotifications) {
                        const senderName = e.sender ? e.sender.name : 'User';
                        window.chatNotifications.show({
                            id: e.id,
                            sender: senderName,
                            message: e.message,
                            time: new Date(e.created_at).toLocaleTimeString('id-ID', { 
                                hour: '2-digit', 
                                minute: '2-digit' 
                            }),
                            senderId: e.sender_id
                        });
                    }
                }
                
                if (window.selectedUserId && selectedUserId === eventSenderId) {
                    appendMessage(e, false);
                    scrollToBottom();
                    
                    axios.post(`/chat/mark-read/${e.sender_id}`)
                        .catch(err => {});
                }
            } else {
                appendMessage(e, false);
                scrollToBottom();
                
                axios.post(`/chat/mark-read/${e.sender_id}`)
                    .catch(err => {});
            }
        }
    }
    
    // Start WebSocket listener
    setupWebSocketListener();
    
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
        
        userItems.forEach(item => {
            item.addEventListener('click', function() {
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
                loadMessages(targetUserId);
                
                axios.post(`/chat/mark-read/${targetUserId}`)
                    .catch(err => {});
            });
        });
    } else {
        // User: Load messages immediately
        loadMessages();
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
                
                clearButton.addEventListener('mouseenter', function() {
                    this.style.color = '#ef4444';
                    this.style.transform = 'translateY(-50%) scale(1.1)';
                });
                
                clearButton.addEventListener('mouseleave', function() {
                    this.style.color = '#94a3b8';
                    this.style.transform = 'translateY(-50%) scale(1)';
                });
                
                clearButton.addEventListener('click', function(e) {
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
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                const userItems = document.querySelectorAll('.user-item');
                
                let visibleCount = 0;
                
                userItems.forEach(item => {
                    const userName = (item.dataset.userName || '').toLowerCase();
                    const userType = (item.querySelector('.user-type')?.textContent || '').toLowerCase();
                    
                    // Show all if search is empty
                    if (searchTerm === '') {
                        item.style.display = 'block';
                        visibleCount++;
                        return;
                    }
                    
                    // Search by name or type
                    if (userName.includes(searchTerm) || userType.includes(searchTerm)) {
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Show "no results" message
                const userList = document.getElementById('userList');
                if (userList) {
                    let noResults = userList.querySelector('.no-results-message');
                    
                    if (visibleCount === 0 && searchTerm !== '') {
                        if (!noResults) {
                            noResults = document.createElement('div');
                            noResults.className = 'no-results-message';
                            noResults.innerHTML = `
                                <div style="text-align: center; padding: 40px 20px; color: #94a3b8;">
                                    <i class="fas fa-search" style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
                                    <div style="font-size: 14px; margin-bottom: 4px; font-weight: 500;">Tidak ada hasil</div>
                                    <div style="font-size: 12px;">Coba kata kunci lain</div>
                                </div>
                            `;
                            userList.appendChild(noResults);
                        }
                        noResults.style.display = 'block';
                    } else if (noResults) {
                        noResults.style.display = 'none';
                    }
                }
                
                // Update clear button visibility
                if (clearButton) {
                    clearButton.style.display = searchTerm ? 'block' : 'none';
                }
            });
            
            // Prevent form submission
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                }
            });
        }
    }
});