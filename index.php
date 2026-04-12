<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

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
$purgeMarker = __DIR__ . '/laravel/storage/framework/.cache-purged-v2';
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

$app->handleRequest(Request::capture());
