import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import { quasar, transformAssetUrls } from '@quasar/vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/admin.css', 'resources/js/admin/app.ts'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
        vue({
            template: { transformAssetUrls },
        }),
        quasar({
            sassVariables: false,
        }),
    ],
    resolve: {
        alias: {
            'ziggy-js': '/vendor/tightenco/ziggy',
        },
    },
    css: {
        preprocessorOptions: {
            sass: {
                silenceDeprecations: ['import'],
            },
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
