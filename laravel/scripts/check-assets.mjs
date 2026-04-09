import { existsSync } from 'node:fs';
import { spawn } from 'node:child_process';
import { setTimeout as delay } from 'node:timers/promises';
import path from 'node:path';
import process from 'node:process';

const PORT = 8131;
const BASE_URL = `http://127.0.0.1:${PORT}`;
const ROUTES = ['/', '/about', '/services', '/projects', '/brands', '/branches', '/contact'];
const PROJECT_ROOT = process.cwd();
const PUBLIC_ROOT = path.join(PROJECT_ROOT, 'public');

async function main() {
    const server = spawn('php', ['artisan', 'serve', '--host=127.0.0.1', `--port=${PORT}`], {
        cwd: PROJECT_ROOT,
        stdio: 'ignore',
    });

    try {
        await waitForServer();

        const assetPaths = new Set();
        for (const route of ROUTES) {
            const response = await fetch(`${BASE_URL}${route}`);
            if (!response.ok) {
                throw new Error(`Failed to render ${route}: ${response.status} ${response.statusText}`);
            }

            const html = await response.text();
            for (const asset of extractAssetPaths(html)) {
                assetPaths.add(asset);
            }
        }

        const missingAssets = Array.from(assetPaths).filter((assetPath) => !existsSync(path.join(PUBLIC_ROOT, assetPath)));
        if (missingAssets.length) {
            console.error('Missing public assets detected:');
            missingAssets.forEach((assetPath) => console.error(`- ${assetPath}`));
            process.exitCode = 1;
            return;
        }

        console.log(`Verified ${assetPaths.size} public images/videos across ${ROUTES.length} routes.`);
    } finally {
        server.kill('SIGTERM');
    }
}

function extractAssetPaths(html) {
    const assets = new Set();
    const regex = /\b(?:src|poster)=["']([^"']+)["']/g;

    for (const match of html.matchAll(regex)) {
        const url = new URL(match[1], BASE_URL);
        if (!url.pathname.startsWith('/images/') && !url.pathname.startsWith('/videos/')) {
            continue;
        }

        assets.add(url.pathname.slice(1));
    }

    return assets;
}

async function waitForServer() {
    for (let attempt = 0; attempt < 40; attempt += 1) {
        try {
            const response = await fetch(BASE_URL);
            if (response.ok) {
                return;
            }
        } catch {
            // Keep retrying until the dev server is available.
        }

        await delay(250);
    }

    throw new Error(`Timed out waiting for ${BASE_URL}`);
}

await main();
