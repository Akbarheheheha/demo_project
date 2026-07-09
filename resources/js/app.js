import axios from 'axios';
import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
Alpine.start();

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

function renderLucideIcons(options = {}) {
    createIcons({ icons, ...options });
}

window.lucide = { createIcons: renderLucideIcons, icons };
window.Chart = Chart;

document.addEventListener('DOMContentLoaded', () => {
    renderLucideIcons();
});
