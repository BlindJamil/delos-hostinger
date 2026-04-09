import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
    testDir: './tests/e2e',
    fullyParallel: true,
    timeout: 30_000,
    expect: {
        timeout: 5_000,
    },
    use: {
        baseURL: 'http://127.0.0.1:8123',
        trace: 'retain-on-failure',
        video: 'retain-on-failure',
    },
    webServer: {
        command: 'php artisan serve --host=127.0.0.1 --port=8123',
        cwd: '.',
        reuseExistingServer: !process.env.CI,
        timeout: 120_000,
        url: 'http://127.0.0.1:8123',
    },
    projects: [
        {
            name: 'desktop',
            use: {
                ...devices['Desktop Chrome'],
                viewport: { width: 1440, height: 900 },
            },
        },
        {
            name: 'reduced-motion',
            use: {
                ...devices['Desktop Chrome'],
                reducedMotion: 'reduce',
                viewport: { width: 1440, height: 900 },
            },
        },
        {
            name: 'mobile',
            use: {
                ...devices['iPhone 13'],
            },
        },
    ],
    reporter: [['list'], ['html', { open: 'never' }]],
});
