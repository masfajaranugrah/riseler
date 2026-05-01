import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Pusher masih diperlukan karena Reverb menggunakan Pusher protocol
window.Pusher = Pusher;

const browserHost = window.location.hostname;
const envHost = (import.meta.env.VITE_REVERB_HOST || '').trim();
const isBrowserLocal = ['localhost', '127.0.0.1'].includes(browserHost);
const isEnvLocal = ['localhost', '127.0.0.1'].includes(envHost);
const browserIsHttps = window.location.protocol === 'https:';

// Safety fallback: if built asset still contains localhost host but app is opened on production domain,
// force websocket host to current browser host.
const wsHost = !isBrowserLocal && isEnvLocal ? browserHost : (envHost || browserHost);

const envScheme = (import.meta.env.VITE_REVERB_SCHEME || '').toLowerCase();
const forceTLS = browserIsHttps || envScheme === 'https';
const envPort = Number(import.meta.env.VITE_REVERB_PORT || 0);
const defaultPort = forceTLS ? 443 : 80;
const wsPort = (!isBrowserLocal && browserIsHttps)
    ? (envPort > 0 && envPort !== 8080 ? envPort : 443)
    : (envPort > 0 ? envPort : defaultPort);
const transports = forceTLS ? ['wss'] : ['ws', 'wss'];

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: wsHost,
    wsPort: wsPort,
    wssPort: wsPort,
    forceTLS: forceTLS,
    enabledTransports: transports,
    activityTimeout: 60000,
    pongTimeout: 60000,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    }
});

if (window.Echo?.connector?.pusher?.connection) {
    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.info('[Echo] connected', { wsHost, wsPort, forceTLS, transports });
    });

    window.Echo.connector.pusher.connection.bind('error', (event) => {
        console.error('[Echo] connection error', event);
    });
}
