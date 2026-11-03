import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { svelte } from '@sveltejs/vite-plugin-svelte';

export default defineConfig({
    plugins: [
        laravel({
            // The school app, the exam client (D-094), and the staff console's school map (D-170).
            input: ['resources/js/app.ts', 'resources/exam-client/app.ts', 'resources/js/admin/school-map.ts'],
            refresh: true,
        }),
        tailwindcss(),
        svelte(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
