import { expect, test } from '@playwright/test';

const ROUTES = [
    { path: '/', title: /Delos International/ },
    { path: '/about', title: /About Delos International/ },
    { path: '/services', title: /Services/ },
    { path: '/projects', title: /Projects/ },
    { path: '/brands', title: /Italian Brand Partners/ },
    { path: '/branches', title: /Our Branches/ },
    { path: '/contact', title: /Contact Us/ },
];

test.describe('Desktop quality gates', () => {
    test('public routes load without console warnings or missing assets', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'desktop', 'Desktop-only health checks');
        test.slow();

        for (const route of ROUTES) {
            const diagnostics = createDiagnostics();
            const routePage = await page.context().newPage();
            attachDiagnostics(routePage, diagnostics);

            await routePage.goto(route.path, { waitUntil: 'load' });
            await routePage.waitForLoadState('networkidle');
            await waitForPageBoot(routePage);

            await expect(routePage).toHaveTitle(route.title);
            expect(diagnostics.consoleMessages, `Console issues on ${route.path}`).toEqual([]);
            expect(diagnostics.httpErrors, `HTTP errors on ${route.path}`).toEqual([]);

            await routePage.close();
        }
    });

    test('motion targets complete across the full page and counters resolve', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'desktop', 'Desktop-only motion checks');
        test.slow();

        for (const route of ROUTES) {
            await page.goto(route.path, { waitUntil: 'load' });
            await page.waitForLoadState('networkidle');
            await waitForPageBoot(page);
            await scrollPage(page);
            const state = await waitForMotionSettle(page, route.path);

            expect(state.hiddenMotion, `Hidden motion targets remain on ${route.path}`).toBe(0);
            expect(state.hiddenLines, `Hidden motion lines remain on ${route.path}`).toBe(0);
            expect(state.mismatchedCounters, `Counter mismatch on ${route.path}`).toEqual([]);
        }
    });

    test('same-origin absolute links trigger the page transition overlay', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'desktop', 'Desktop-only transition checks');

        await page.goto('/', { waitUntil: 'load' });
        await page.waitForLoadState('networkidle');
        await waitForPageBoot(page);

        const overlay = page.locator('[data-page-transition]');
        const aboutLink = page.locator('a[href$="/about"]').first();

        await aboutLink.click({ noWaitAfter: true });
        await expect(overlay).toHaveClass(/is-active/);
        await page.waitForURL('**/about');
    });

    test('narrow desktop windows keep the employee showcase pinned to vertical scroll', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'desktop', 'Desktop-only employee showcase regression');
        test.slow();

        await page.setViewportSize({ width: 900, height: 900 });
        await page.goto('/', { waitUntil: 'load' });
        await page.waitForLoadState('networkidle');
        await waitForPageBoot(page);

        const before = await page.evaluate(() => {
            const section = document.querySelector('#employees-section');
            const track = document.querySelector('#employee-track');
            const transform = getComputedStyle(track).transform;

            return {
                overflowX: getComputedStyle(section).overflowX,
                sectionTop: section.getBoundingClientRect().top + window.scrollY,
                sectionWidth: section.clientWidth,
                trackWidth: track.scrollWidth,
                x: transform === 'none' ? 0 : new DOMMatrixReadOnly(transform).m41,
            };
        });

        await page.evaluate((sectionTop) => {
            window.scrollTo(0, sectionTop);
        }, before.sectionTop);
        await page.waitForTimeout(250);

        await page.evaluate(() => {
            window.scrollBy(0, 900);
        });
        await page.waitForTimeout(400);

        const after = await page.evaluate(() => {
            const track = document.querySelector('#employee-track');
            const transform = getComputedStyle(track).transform;

            return {
                x: transform === 'none' ? 0 : new DOMMatrixReadOnly(transform).m41,
            };
        });

        expect(['hidden', 'clip']).toContain(before.overflowX);
        expect(before.trackWidth).toBeGreaterThan(before.sectionWidth);
        expect(after.x).toBeLessThan(before.x - 40);
    });

    test('employee header spacing stays within the approved desktop range', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'desktop', 'Desktop-only employee spacing regression');
        test.slow();

        const viewports = [
            { width: 900, height: 900 },
            { width: 900, height: 1100 },
        ];

        for (const viewport of viewports) {
            await page.setViewportSize(viewport);
            await page.goto('/', { waitUntil: 'load' });
            await page.waitForLoadState('networkidle');
            await waitForPageBoot(page);
            await page.locator('[data-motion-group="employee-header"]').scrollIntoViewIfNeeded();
            await page.waitForTimeout(150);

            const spacing = await readEmployeeSpacing(page);

            expect(spacing.cardVisible, `Employee card should remain visible at ${viewport.width}x${viewport.height}`).toBe(true);
            expect(spacing.gap, `Employee header gap is too small at ${viewport.width}x${viewport.height}`).toBeGreaterThanOrEqual(180);
            expect(spacing.gap, `Employee header gap is too large at ${viewport.width}x${viewport.height}`).toBeLessThanOrEqual(220);
        }
    });
});

test.describe('Reduced motion behavior', () => {
    test('reduced-motion mode renders final content state immediately', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'reduced-motion', 'Reduced-motion checks only');

        await page.emulateMedia({ reducedMotion: 'reduce' });
        await page.goto('/', { waitUntil: 'load' });
        await page.waitForLoadState('networkidle');
        await waitForPageBoot(page);
        await waitForReducedMotionReady(page);

        const state = await readMotionState(page);
        state.marqueeAnimation = await page.evaluate(() => getComputedStyle(document.querySelector('.marquee-track')).animationName);
        state.brandAnimation = await page.evaluate(() => getComputedStyle(document.querySelector('.brand-marquee')).animationName);

        expect(state.hiddenMotion).toBe(0);
        expect(state.hiddenLines).toBe(0);
        expect(state.mismatchedCounters).toEqual([]);
        expect(state.scrollMode).toBe('native');
        expect(state.transitionDisplay).toBe('none');
        expect(state.marqueeAnimation).toBe('none');
        expect(state.brandAnimation).toBe('none');
    });

    test('reduced-motion mode bypasses page transition gating', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'reduced-motion', 'Reduced-motion checks only');

        await page.emulateMedia({ reducedMotion: 'reduce' });
        await page.goto('/', { waitUntil: 'load' });
        await page.waitForLoadState('networkidle');
        await waitForPageBoot(page);

        await page.locator('a[href$="/about"]').first().click();
        await page.waitForURL('**/about');
        await page.waitForLoadState('networkidle');
        await waitForPageBoot(page);
        await waitForReducedMotionReady(page);

        const state = await page.evaluate(() => ({
            transitionKey: sessionStorage.getItem('delos:page-transition'),
            transitionVisible: getComputedStyle(document.querySelector('[data-page-transition]')).display,
        }));

        expect(state.transitionKey).toBeNull();
        expect(state.transitionVisible).toBe('none');
    });
});

test.describe('Mobile behavior', () => {
    test('mobile navigation stays accessible and disables desktop-only motion affordances', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'mobile', 'Mobile-only checks');

        await page.goto('/', { waitUntil: 'load' });
        await page.waitForLoadState('networkidle');
        await waitForPageBoot(page);

        const before = await page.evaluate(() => ({
            customCursor: document.body.classList.contains('has-custom-cursor'),
            expanded: document.querySelector('#mobile-menu-btn')?.getAttribute('aria-expanded'),
            scrollMode: document.documentElement.dataset.scrollMode,
        }));

        await page.click('#mobile-menu-btn');

        const after = await page.evaluate(() => ({
            expanded: document.querySelector('#mobile-menu-btn')?.getAttribute('aria-expanded'),
            firstDelay: document.querySelector('#mobile-menu a')?.style.transitionDelay ?? '',
            menuOpen: document.querySelector('#mobile-menu')?.classList.contains('menu-open'),
        }));

        expect(before.customCursor).toBe(false);
        expect(before.expanded).toBe('false');
        expect(before.scrollMode).toBe('native');
        expect(after.expanded).toBe('true');
        expect(after.menuOpen).toBe(true);
        expect(after.firstDelay).toBe('0.1s');
    });

    test('employee showcase remains touch-scrollable on mobile', async ({ page }, testInfo) => {
        test.skip(testInfo.project.name !== 'mobile', 'Mobile-only checks');

        await page.goto('/', { waitUntil: 'load' });
        await page.waitForLoadState('networkidle');
        await waitForPageBoot(page);

        const state = await page.evaluate(() => {
            const section = document.querySelector('#employees-section');
            const track = document.querySelector('#employee-track');

            return {
                overflowX: getComputedStyle(section).overflowX,
                sectionWidth: section?.clientWidth ?? 0,
                trackWidth: track?.scrollWidth ?? 0,
            };
        });

        expect(['auto', 'scroll']).toContain(state.overflowX);
        expect(state.trackWidth).toBeGreaterThan(state.sectionWidth);
    });
});

function attachDiagnostics(page, diagnostics) {
    page.on('console', (message) => {
        if (message.type() === 'warning' || message.type() === 'error') {
            diagnostics.consoleMessages.push(`${message.type()}: ${message.text()}`);
        }
    });

    page.on('response', (response) => {
        if (response.status() >= 400 && isPublicAssetOrRoute(response.url())) {
            diagnostics.httpErrors.push(`${response.status()} ${new URL(response.url()).pathname}`);
        }
    });
}

function createDiagnostics() {
    return {
        consoleMessages: [],
        httpErrors: [],
    };
}

function isPublicAssetOrRoute(url) {
    const parsed = new URL(url);
    return (
        parsed.origin === 'http://127.0.0.1:8123' &&
        !parsed.pathname.startsWith('/build/')
    );
}

async function scrollPage(page) {
    await page.evaluate(async () => {
        let maxScroll = document.documentElement.scrollHeight - window.innerHeight;

        for (let position = 0; position <= maxScroll; position += 700) {
            window.scrollTo(0, position);
            await new Promise((resolve) => window.setTimeout(resolve, 220));
            maxScroll = document.documentElement.scrollHeight - window.innerHeight;
        }

        const settleDeadline = performance.now() + 5000;
        while (performance.now() < settleDeadline) {
            maxScroll = document.documentElement.scrollHeight - window.innerHeight;
            window.scrollTo(0, maxScroll);

            await new Promise((resolve) => window.setTimeout(resolve, 140));

            if (maxScroll - window.scrollY <= 12) {
                break;
            }
        }
    });

    await page.waitForTimeout(500);
}

async function waitForPageBoot(page) {
    await page.waitForFunction(() => {
        const loader = document.querySelector('[data-page-loader]');
        const loaderReady = !loader || loader.classList.contains('is-loaded');
        const motionReady =
            document.documentElement.classList.contains('reduced-motion') ||
            Boolean(document.documentElement.dataset.scrollMode);

        return loaderReady && motionReady;
    });

    await page.waitForTimeout(150);
}

async function readMotionState(page) {
    return page.evaluate(() => {
        const counters = Array.from(document.querySelectorAll('[data-motion-counter]')).map((counter) => ({
            actual: counter.textContent?.trim(),
            expected: Number(counter.dataset.motionCounter).toLocaleString(),
        }));

        return {
            hiddenLines: document.querySelectorAll('[data-motion-line]:not(.is-visible)').length,
            hiddenMotion: document.querySelectorAll('[data-motion]:not(.is-visible)').length,
            mismatchedCounters: counters.filter((counter) => counter.actual !== counter.expected),
            reducedClass: document.documentElement.classList.contains('reduced-motion'),
            scrollMode: document.documentElement.dataset.scrollMode,
            transitionDisplay: getComputedStyle(document.querySelector('[data-page-transition]')).display,
        };
    });
}

async function waitForMotionSettle(page, routePath) {
    let settledState;

    await expect
        .poll(async () => {
            settledState = await readMotionState(page);

            return {
                hiddenLines: settledState.hiddenLines,
                hiddenMotion: settledState.hiddenMotion,
                mismatchedCounters: settledState.mismatchedCounters.length,
            };
        }, {
            message: `Motion did not settle on ${routePath}`,
            timeout: 8_000,
        })
        .toEqual({
            hiddenLines: 0,
            hiddenMotion: 0,
            mismatchedCounters: 0,
        });

    return settledState;
}

async function waitForReducedMotionReady(page) {
    let settledState;

    await expect
        .poll(async () => {
            settledState = await readMotionState(page);

            return {
                hiddenLines: settledState.hiddenLines,
                hiddenMotion: settledState.hiddenMotion,
                mismatchedCounters: settledState.mismatchedCounters.length,
                reducedClass: settledState.reducedClass,
                scrollMode: settledState.scrollMode,
                transitionDisplay: settledState.transitionDisplay,
            };
        }, {
            message: 'Reduced-motion mode did not reach its final state',
            timeout: 4_000,
        })
        .toEqual({
            hiddenLines: 0,
            hiddenMotion: 0,
            mismatchedCounters: 0,
            reducedClass: true,
            scrollMode: 'native',
            transitionDisplay: 'none',
        });
}

async function readEmployeeSpacing(page) {
    return page.evaluate(() => {
        const header = document.querySelector('[data-motion-group="employee-header"]');
        const firstCard = document.querySelector('#employee-track .employee-slide');

        if (!header || !firstCard) {
            return {
                cardVisible: false,
                gap: Number.NaN,
            };
        }

        const headerRect = header.getBoundingClientRect();
        const cardRect = firstCard.getBoundingClientRect();

        return {
            cardVisible: cardRect.top < window.innerHeight + 16 && cardRect.bottom > 0,
            gap: Math.ceil(cardRect.top - headerRect.bottom),
        };
    });
}
