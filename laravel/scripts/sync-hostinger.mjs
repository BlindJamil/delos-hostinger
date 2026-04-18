import { cpSync, existsSync, mkdirSync, rmSync } from 'node:fs';
import path from 'node:path';
import process from 'node:process';

const projectRoot = process.cwd();
const workspaceRoot = path.resolve(projectRoot, '..');
const hostingerRoot = path.join(workspaceRoot, 'hostinger-Delos');

const mappings = [
    ['app', 'laravel/app'],
    ['bootstrap/app.php', 'laravel/bootstrap/app.php'],
    ['composer.json', 'laravel/composer.json'],
    ['config/app.php', 'laravel/config/app.php'],
    // Admin panel requires the 'admin' guard + 'admin_users' provider
    // to be registered. Without syncing auth.php the AppServiceProvider
    // view composer (auth('admin')->check()) 500s on every request.
    ['config/auth.php', 'laravel/config/auth.php'],
    // Page Content Editor registry — new in April 2026. Drives which
    // lang keys admins can edit from /admin/page-content.
    ['config/editable_pages.php', 'laravel/config/editable_pages.php'],
    // Database migrations + seeders need to travel with the code so
    // production can run `php artisan migrate` and the PageContent
    // seeder after deploy.
    ['database/migrations', 'laravel/database/migrations'],
    ['database/seeders', 'laravel/database/seeders'],
    ['docs', 'laravel/docs'],
    ['eslint.config.js', 'laravel/eslint.config.js'],
    ['lang', 'laravel/lang'],
    ['package-lock.json', 'laravel/package-lock.json'],
    ['package.json', 'laravel/package.json'],
    ['playwright.config.js', 'laravel/playwright.config.js'],
    ['resources/css', 'laravel/resources/css'],
    ['resources/js', 'laravel/resources/js'],
    // Inline SVG assets read by blade components (notably the accurate
    // Iraq outline used by x-iraq-map on the branches page). Without
    // this mapping, file_get_contents() returns '' and the map renders
    // with branch pins but no geography.
    ['resources/svg', 'laravel/resources/svg'],
    ['resources/views', 'laravel/resources/views'],
    ['routes/web.php', 'laravel/routes/web.php'],
    ['scripts', 'laravel/scripts'],
    ['tests/e2e', 'laravel/tests/e2e'],
    ['tests/Feature', 'laravel/tests/Feature'],
    ['public/build', 'build'],
    ['public/images', 'images'],
    ['public/videos', 'videos'],
    // Raised PHP upload limits so admins can replace the homepage
    // brand video (~42MB) from the page-content editor.
    ['public/.user.ini', '.user.ini'],
    // Apache/LiteSpeed config — cache-control, PHP input limits, and
    // rewrite rules. Previously edited in the Hostinger repo by hand;
    // now sourced from delos-website so the two stay in lockstep.
    ['public/.htaccess', '.htaccess'],
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
