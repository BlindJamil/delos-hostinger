import { expect, test } from '@playwright/test';

test.describe('Visual smoke', () => {
    test('home hero matches the desktop baseline', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'desktop', 'Desktop-only visual smoke');

        await gotoAndStabilize(page, '/');
        await expect(page.locator('#hero')).toHaveScreenshot('home-hero.png', {
            animations: 'disabled',
            caret: 'hide',
            maxDiffPixels: 10,
        });
    });

    test('employee showcase matches the desktop baseline', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'desktop', 'Desktop-only visual smoke');

        await gotoAndStabilize(page, '/');

        const section = page.locator('#employees-section');
        await section.scrollIntoViewIfNeeded();
        await stabilizeForScreenshot(page);

        await expect(section).toHaveScreenshot('home-employees.png', {
            animations: 'disabled',
            caret: 'hide',
        });
    });

    test('home brands section matches the desktop baseline', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'desktop', 'Desktop-only visual smoke');

        await gotoAndStabilize(page, '/');

        const section = page.locator('#brands-section');
        await section.scrollIntoViewIfNeeded();
        await stabilizeForScreenshot(page);

        await expect(section).toHaveScreenshot('home-brands.png', {
            animations: 'disabled',
            caret: 'hide',
        });
    });

    test('about hero matches the desktop baseline', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'desktop', 'Desktop-only visual smoke');

        await gotoAndStabilize(page, '/about');
        await expect(page.locator('[data-motion-hero]').first()).toHaveScreenshot('about-hero.png', {
            animations: 'disabled',
            caret: 'hide',
        });
    });
});

async function gotoAndStabilize(page, route) {
    await page.goto(route, { waitUntil: 'load' });
    await page.waitForLoadState('networkidle');
    await page.waitForFunction(() => {
        const loader = document.querySelector('[data-page-loader]');
        const loaderReady = !loader || loader.classList.contains('is-loaded');
        const motionReady =
            document.documentElement.classList.contains('reduced-motion') ||
            Boolean(document.documentElement.dataset.scrollMode);

        return loaderReady && motionReady;
    });
    await page.evaluate(async () => {
        try {
            await document.fonts.ready;
        } catch {
            // Continue if the browser does not expose the Font Loading API.
        }
    });
    await stabilizeForScreenshot(page);
}

async function stabilizeForScreenshot(page) {
    await page.addStyleTag({
        content: `
            *, *::before, *::after {
                animation: none !important;
                transition: none !important;
                caret-color: transparent !important;
            }

            .custom-cursor,
            [data-page-loader],
            [data-page-transition] {
                display: none !important;
            }
        `,
    });

    await page.evaluate(() => {
        document.querySelector('[data-page-loader]')?.classList.add('is-loaded');
        document.querySelector('[data-page-transition]')?.classList.remove('is-active', 'is-exiting');

        document.querySelectorAll('[data-motion], [data-motion-line]').forEach((element) => {
            element.classList.add('is-visible');
        });

        document.querySelectorAll('[data-motion-counter]').forEach((counter) => {
            const value = Number(counter.getAttribute('data-motion-counter'));
            if (Number.isFinite(value)) {
                counter.textContent = value.toLocaleString();
            }
        });

        document.querySelectorAll('.stat-suffix').forEach((suffix) => {
            suffix.classList.add('is-visible');
        });
    });

    await page.waitForTimeout(100);
}
