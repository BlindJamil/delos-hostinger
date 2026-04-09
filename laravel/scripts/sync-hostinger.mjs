import { cpSync, existsSync, mkdirSync, rmSync } from 'node:fs';
import path from 'node:path';
import process from 'node:process';

const projectRoot = process.cwd();
const workspaceRoot = path.resolve(projectRoot, '..');
const hostingerRoot = path.join(workspaceRoot, 'hostinger-Delos');

const mappings = [
    ['app/Http/Controllers/PageController.php', 'laravel/app/Http/Controllers/PageController.php'],
    ['docs', 'laravel/docs'],
    ['eslint.config.js', 'laravel/eslint.config.js'],
    ['package-lock.json', 'laravel/package-lock.json'],
    ['package.json', 'laravel/package.json'],
    ['playwright.config.js', 'laravel/playwright.config.js'],
    ['resources/css', 'laravel/resources/css'],
    ['resources/js', 'laravel/resources/js'],
    ['resources/views', 'laravel/resources/views'],
    ['routes/web.php', 'laravel/routes/web.php'],
    ['scripts', 'laravel/scripts'],
    ['tests/e2e', 'laravel/tests/e2e'],
    ['public/build', 'build'],
    ['public/images', 'images'],
    ['public/videos', 'videos'],
];

for (const [sourceRelativePath, destinationRelativePath] of mappings) {
    const source = path.join(projectRoot, sourceRelativePath);
    const destination = path.join(hostingerRoot, destinationRelativePath);

    if (!existsSync(source)) {
        console.warn(`Skipping missing source: ${sourceRelativePath}`);
        continue;
    }

    mkdirSync(path.dirname(destination), { recursive: true });
    rmSync(destination, { force: true, recursive: true });
    cpSync(source, destination, {
        force: true,
        recursive: true,
    });

    console.log(`Synced ${sourceRelativePath} -> hostinger-Delos/${destinationRelativePath}`);
}
