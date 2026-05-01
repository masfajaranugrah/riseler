// Chat Sound System - Only for Admin (Notifications removed for performance)
class ChatSoundManager {
    constructor() {
        this.soundEnabled = true;
        this.audioUnlocked = false;
        this.preloadedAudio = null;
        this.init();
        this.unlockAudio();
    }

    init() {
        // No container needed - sound only
    }

    unlockAudio() {
        this.preloadedAudio = new Audio('/sounds/42289.mp3');
        this.preloadedAudio.volume = 0.5;
        this.preloadedAudio.load();

        const unlockAudioContext = () => {
            if (this.audioUnlocked) return;

            this.preloadedAudio.play()
                .then(() => {
                    this.preloadedAudio.pause();
                    this.preloadedAudio.currentTime = 0;
                    this.audioUnlocked = true;
                })
                .catch(() => { });
        };

        const events = ['click', 'touchstart', 'keydown', 'scroll', 'mousemove'];
        events.forEach(eventType => {
            document.addEventListener(eventType, unlockAudioContext, { once: true, passive: true });
        });
    }

    // Play sound only - no visual notification
    playSound() {
        if (!this.audioUnlocked) {
            return;
        }

        try {
            if (this.preloadedAudio) {
                this.preloadedAudio.currentTime = 0;
                this.preloadedAudio.play().catch(() => { });
            } else {
                const audio = new Audio('/sounds/42289.mp3');
                audio.volume = 0.5;
                audio.play().catch(() => { });
            }
        } catch (error) { }
    }
}

if (window.isAdmin) {
    window.chatSoundManager = new ChatSoundManager();

    // Keep backward compatibility
    window.chatNotifications = {
        show: function (data) {
            // Only play sound, no visual notification
            if (window.chatSoundManager) {
                window.chatSoundManager.playSound();
            }
        }
    };

    window.testSound = function () {
        if (window.chatSoundManager) {
            window.chatSoundManager.playSound();
        }
    };
}
