import js from '@eslint/js';
import svelte from 'eslint-plugin-svelte';
import globals from 'globals';
import ts from 'typescript-eslint';
import svelteConfig from './svelte.config.js';

export default ts.config(
    { ignores: ['public/', 'vendor/', 'node_modules/', 'storage/', 'bootstrap/ssr/', 'scripts/', 'docs/'] },
    js.configs.recommended,
    ...ts.configs.recommended,
    ...svelte.configs.recommended,
    {
        languageOptions: { globals: { ...globals.browser, ...globals.node } },
    },
    {
        files: ['**/*.svelte', '**/*.svelte.ts'],
        languageOptions: {
            parserOptions: { projectService: true, extraFileExtensions: ['.svelte'], parser: ts.parser, svelteConfig },
        },
    },
    {
        // The exam client shares no code with the school app (D-094).
        files: ['resources/exam-client/**'],
        rules: {
            'no-restricted-imports': ['error', { patterns: ['@/*', '**/js/*'] }],
        },
    },
);
