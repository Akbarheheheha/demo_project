import axios from 'axios';
import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;
Alpine.start();

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

function renderLucideIcons(options = {}) {
    createIcons({ icons, ...options });
}

window.lucide = { createIcons: renderLucideIcons, icons };

document.addEventListener('DOMContentLoaded', () => {
    renderLucideIcons();
});
