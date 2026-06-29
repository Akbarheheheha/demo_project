import axios from 'axios';
import Alpine from 'alpinejs';

// Bind Alpine globally
window.Alpine = Alpine;
Alpine.start();

// Bind Axios globally
window.axios = axios;

// Configure default headers
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Configure CSRF Token for Axios from meta tag
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

console.log('SmartBiz ERP App loaded. Axios and AlpineJS initialized successfully.');
