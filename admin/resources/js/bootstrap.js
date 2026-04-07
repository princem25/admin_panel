import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import $ from 'jquery';

window.axios = axios;
window.$ = $;
window.jQuery = $;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next, we will create an instance of Pusher as our broadcasting client.
 * This is used to subscribe to channels and listen for events that are
 * broadcast by Laravel. Echo abstracts all of this complexity for you.
 */

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    useTLS: true,
    encrypted: true,
});
