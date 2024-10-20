import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',  // Make sure this file exists
                'resources/js/app.js',      // Make sure this file exists
            ],
            refresh: true,
        }),
    ],
});
