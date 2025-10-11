import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/www.css',
                'resources/js/www.js',
                'resources/css/admin.css',
                'resources/js/admin.js',],
            refresh: true,
        }),
    ],
});
