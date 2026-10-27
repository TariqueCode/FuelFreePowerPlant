'use strict';

const assert = require('node:assert/strict');
const { chromium } = require(process.env.PLAYWRIGHT_MODULE || '/tmp/ffp-browser-qa/node_modules/playwright');

const baseUrl = process.env.NAVIGATION_QA_BASE_URL || 'http://127.0.0.1:8000';
const email = 'navigation-qa@example.test';
const password = 'Navigation-QA-2026-10-27';
const pageErrors = [];

async function signIn(page) {
    await page.goto(baseUrl + '/login', { waitUntil: 'domcontentloaded' });
    await page.locator('#email').fill(email);
    await page.locator('#password').fill(password);

    await Promise.all([
        page.waitForURL(url => url.pathname === '/admin', { timeout: 15000 }),
        page.locator('button[type="submit"]').click(),
    ]);
}

async function ensureWebsiteOpen(page) {
    const group = page.locator('details.nav-group').filter({ hasText: 'Website' }).first();
    assert.equal(await group.count(), 1, 'Website navigation group must render exactly once');

    if (!await group.evaluate(element => element.open)) {
        await group.locator('summary.nav-parent').click();
    }

    assert.equal(await group.evaluate(element => element.open), true, 'Website group should be expanded');
}

async function clickDestination(page, label, path) {
    const link = page.locator('.sidebar a[href]').filter({ hasText: label }).first();
    assert.equal(await link.count(), 1, label + ' navigation link must exist');

    const responsePromise = page.waitForResponse(response =>
        response.request().isNavigationRequest()
        && new URL(response.url()).pathname.replace(/\/+$/, '') === path.replace(/\/+$/, ''),
    { timeout: 15000 });

    await link.click();
    const response = await responsePromise;
    assert.equal(response.status(), 200, label + ' destination should return HTTP 200');
    await page.waitForLoadState('domcontentloaded');
    assert.equal(
        new URL(page.url()).pathname.replace(/\/+$/, ''),
        path.replace(/\/+$/, ''),
        label + ' click should navigate to ' + path,
    );
}

async function verifyDesktop(browser) {
    const context = await browser.newContext({ viewport: { width: 1365, height: 900 } });
    const page = await context.newPage();
    page.on('pageerror', error => pageErrors.push('desktop: ' + error.message));

    try {
        await signIn(page);
        const response = await page.goto(baseUrl + '/admin', { waitUntil: 'domcontentloaded' });
        assert.equal(response.status(), 200, 'Desktop dashboard should return HTTP 200');
        assert.equal(await page.locator('#admin-sidebar').count(), 1);
        assert.equal(await page.locator('#mobile-menu-toggle').isVisible(), false, 'Desktop should not show mobile menu toggle');

        const destinations = [
            ['Profile Builder', '/admin/profile-builder'],
            ['Page Builder', '/admin/page-builder'],
            ['Menu Builder', '/admin/menu-builder'],
        ];

        for (const [label, path] of destinations) {
            await ensureWebsiteOpen(page);
            await clickDestination(page, label, path);
        }

        await page.screenshot({ path: '/tmp/ffp-navigation-desktop.png', fullPage: true });
    } catch (error) {
        await page.screenshot({ path: '/tmp/ffp-navigation-desktop-failure.png', fullPage: true }).catch(() => {});
        throw error;
    } finally {
        await context.close();
    }
}

async function verifyMobile(browser) {
    const context = await browser.newContext({
        viewport: { width: 390, height: 844 },
        isMobile: true,
        hasTouch: true,
    });
    const page = await context.newPage();
    page.on('pageerror', error => pageErrors.push('mobile: ' + error.message));

    try {
        await signIn(page);
        const response = await page.goto(baseUrl + '/admin', { waitUntil: 'domcontentloaded' });
        assert.equal(response.status(), 200, 'Mobile dashboard should return HTTP 200');
        const toggle = page.locator('#mobile-menu-toggle');
        assert.equal(await toggle.isVisible(), true, 'Mobile navigation toggle should be visible');

        await toggle.click();
        assert.equal(
            await page.locator('#admin-sidebar').evaluate(element => element.classList.contains('mobile-open')),
            true,
            'Mobile menu button should open the drawer',
        );

        const destinations = [
            ['Profile Builder', '/admin/profile-builder'],
            ['Page Builder', '/admin/page-builder'],
            ['Menu Builder', '/admin/menu-builder'],
        ];

        for (const [label, path] of destinations) {
            await page.waitForFunction(
                () => document.querySelector('#admin-sidebar')?.classList.contains('mobile-open'),
                null,
                { timeout: 5000 },
            );
            await ensureWebsiteOpen(page);
            await clickDestination(page, label, path);
            await page.waitForFunction(
                () => document.querySelector('#admin-sidebar')?.classList.contains('mobile-open'),
                null,
                { timeout: 5000 },
            );
        }

        await page.screenshot({ path: '/tmp/ffp-navigation-mobile.png', fullPage: true });
    } catch (error) {
        await page.screenshot({ path: '/tmp/ffp-navigation-mobile-failure.png', fullPage: true }).catch(() => {});
        throw error;
    } finally {
        await context.close();
    }
}

(async () => {
    const browser = await chromium.launch({ headless: true });
    try {
        await verifyDesktop(browser);
        await verifyMobile(browser);
        assert.deepEqual(pageErrors, [], 'Browser JavaScript errors: ' + pageErrors.join('; '));
        process.stdout.write('PASS: desktop and mobile navigation clicks reached all three builder destinations with HTTP 200.\n');
    } finally {
        await browser.close();
    }
})().catch(error => {
    console.error(error);
    process.exitCode = 1;
});
