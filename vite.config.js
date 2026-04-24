import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/components/userlogin.js',
                'resources/js/components/transitions.js',
                'resources/js/components/scripts.js',
                'resources/js/components/account.js',
                'resources/js/data/womenData.js',
                'resources/js/data/menData.js',
                'resources/js/data/kidsData.js',
            ],
            refresh: true,
        }),
    ],
});
