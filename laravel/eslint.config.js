import js from '@eslint/js';
import globals from 'globals';

export default [
    {
        ignores: [
            'bootstrap/cache/**',
            'node_modules/**',
            'public/build/**',
            'storage/**',
            'vendor/**',
        ],
    },
    js.configs.recommended,
    {
        files: ['resources/js/**/*.js', 'playwright.config.js', 'tests/e2e/**/*.js'],
        languageOptions: {
            ecmaVersion: 'latest',
            globals: {
                ...globals.browser,
                ...globals.node,
            },
            sourceType: 'module',
        },
        rules: {
            'no-console': ['error', { allow: ['warn', 'error'] }],
            'no-unused-vars': ['error', { argsIgnorePattern: '^_' }],
        },
    },
    {
        files: ['scripts/**/*.mjs'],
        languageOptions: {
            ecmaVersion: 'latest',
            globals: globals.node,
            sourceType: 'module',
        },
        rules: {
            'no-console': 'off',
            'no-unused-vars': ['error', { argsIgnorePattern: '^_' }],
        },
    },
];
