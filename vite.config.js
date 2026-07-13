import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('alpinejs')) return 'vendor-alpine';
                    if (id.includes('axios')) return 'vendor-axios';
                    if (id.includes('lucide')) return 'vendor-icons';
                    if (id.includes('chart.js')) return 'vendor-chart';
                },
            },
        },
        chunkSizeWarningLimit: 200,
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
