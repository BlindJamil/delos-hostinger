<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Runtime safety net for PHP input limits. The admin page-content editor
// can submit 280+ localized fields in a single POST. On Hostinger shared
// hosting, .user.ini is not always respected depending on the doc-root
// layout — these ini_set calls are a belt-and-suspenders override that
// works when the host allows runtime changes. Silent @ prefix so a locked
// server doesn't emit a warning.
@ini_set('max_input_vars', '5000');
@ini_set('post_max_size', '64M');
@ini_set('upload_max_filesize', '64M');
@ini_set('memory_limit', '256M');

define('LARAVEL_START', microtime(true));

/*
 * ONE-SHOT CACHE PURGE — delete after first successful deploy.
 *
 * After the language picker revert deploy, Laravel's bootstrap cache
 * on the Hostinger filesystem still contained stale compiled routes
 * (locale-prefixed /en/*, /ar/*, /it/*) that referenced the removed
 * SetLocale middleware and lang files. Those caches are gitignored,
 * so deploy can't overwrite them. This block runs BEFORE Laravel boots
 * and wipes them so the next request regenerates clean caches from the
 * reverted code.
 *
 * Marker file `.cache-purged-2026-04-11` prevents this from running on
 * every request — it only runs until a successful purge leaves the
 * marker, then it's a no-op forever. Safe to leave in place; remove in
 * a future cleanup commit if you want.
 */
$purgeMarker = __DIR__ . '/laravel/storage/framework/.cache-purged-v3';
if (!file_exists($purgeMarker)) {
    $cacheDirs = [
        __DIR__ . '/laravel/bootstrap/cache',
        __DIR__ . '/laravel/storage/framework/views',
        __DIR__ . '/laravel/storage/framework/cache/data',
    ];
    foreach ($cacheDirs as $dir) {
        if (!is_dir($dir)) continue;
        foreach (glob($dir . '/*.php') ?: [] as $file) {
            @unlink($file);
        }
    }
    // Also clear subdirectories of cache/data (tag cache buckets)
    $cacheData = __DIR__ . '/laravel/storage/framework/cache/data';
    if (is_dir($cacheData)) {
        foreach (glob($cacheData . '/*', GLOB_ONLYDIR) ?: [] as $subdir) {
            foreach (glob($subdir . '/*') ?: [] as $file) {
                @unlink($file);
            }
            @rmdir($subdir);
        }
    }
    @mkdir(dirname($purgeMarker), 0755, true);
    @file_put_contents($purgeMarker, date('c'));
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/laravel/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/laravel/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/laravel/bootstrap/app.php';

$app->usePublicPath(__DIR__);

// NOTE: dropdown-fix.js used to be injected here via output buffering as a
// legacy language-globe implementation. It has been superseded by the
// Vite-bundled initLanguageGlobe() in resources/js/site/language-globe.js
// (loaded via @vite in the main layout). Keeping both caused a race: the
// legacy script ran first, mounted #delos-globe-wrap, and the Vite version
// bailed out on its "already mounted" guard — which meant the Vite-added
// homepage auto-open never fired. The injection is now removed; dropdown-fix.js
// is left on disk as a historical reference but no longer loaded.

$app->handleRequest(Request::capture());
