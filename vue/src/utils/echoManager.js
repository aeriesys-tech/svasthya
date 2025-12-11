import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { ref } from 'vue';

let echoInstance = null;

export const isEchoReady = ref(false);

export const initEcho = (token) => {
    if (!echoInstance) {
        window.Pusher = Pusher;

        echoInstance = new Echo({
            broadcaster: 'pusher',
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
            forceTLS: true,
            authEndpoint: `${import.meta.env.VITE_PUBLIC_URL}/api/broadcasting/auth`,
            auth: {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }
        });

        isEchoReady.value = true;
    }

    return echoInstance;
};

export const getEcho = () => {
    return echoInstance;
};

export const disconnectEcho = () => {
    if (echoInstance) {
        echoInstance.disconnect();
        echoInstance = null;
        isEchoReady.value = false;
    }
};
