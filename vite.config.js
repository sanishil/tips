import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/bootstrap.scss', 'resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Source Sans 3', {
                    weights: [400, 600, 700],
                }),
                bunny('Source Serif 4', {
                    weights: [600, 700],
                }),
            ],
        }),
        tailwindcss(),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                // Bootstrap 5.3's Sass still uses @import and global colour functions.
                quietDeps: true,
                silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'if-function'],
            },
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
