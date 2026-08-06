import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/mobile-pwa.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('@fullcalendar')) {
                            return 'vendor-calendar';
                        }
                        if (id.includes('apexcharts') || id.includes('chart.js')) {
                            return 'vendor-charts';
                        }
                        if (id.includes('swiper') || id.includes('jsvectormap')) {
                            return 'vendor-ui-libs';
                        }
                        return 'vendor';
                    }
                },
            },
        },
    },
});
