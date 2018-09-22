// IE11 needs a promise polyfill
require('es6-promise').polyfill();

window._ = require('lodash');

try {
    window.$ = window.jQuery = require('jquery');
} catch (e) {}



window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';



let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}



import Echo from 'laravel-echo'

window.Pusher = require('pusher-js');

// If a view includes "helpers.dont-connect-echo", then don't connect
// Echo. This is necessary because the site consistently
// exceeds the 100 allowed Pusher connections.
if (typeof dontConnectEcho === 'undefined') {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: Laravel.pusherKey,
        cluster: Laravel.pusherCluster,
        encrypted: Laravel.pusherEncrypted,
    });
}

require('./vendor/jquery.maskedinput.min');

require('./vendor/blockadblock');

require('./scripts/drag-and-drop');
