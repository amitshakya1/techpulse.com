import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/web.css',
                'resources/js/web.js',
                'resources/css/admin.css',
                'resources/js/admin.js',],
            refresh: true,
        }),
    ],
});
