import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/catalogo.css',
                'resources/css/cart.css',
                'resources/css/admin.css',
                'resources/js/app.js',
                'resources/js/catalogo.js',
                'resources/js/cart.js',
                'resources/js/admin.js',

            ],
            refresh: true,
        }),
    ],
});
