<?php
/**
 * One-shot cache purge. Visit this URL once after deploy:
 *   https://darkgoldenrod-dove-850949.hostingersite.com/_purge.php
 *
 * Clears PHP OPcache (which caches compiled PHP in memory and ignores
 * file changes on disk) + Laravel's compiled views + bootstrap cache.
 * After visiting, delete this file or it will just show "already done".
 */

$results = [];

// 1. Clear OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    $results[] = 'OPcache cleared';
} else {
    $results[] = 'OPcache not available';
}

// 2. Clear Laravel compiled views
$viewsDir = __DIR__ . '/laravel/storage/framework/views';
$count = 0;
if (is_dir($viewsDir)) {
    foreach (glob($viewsDir . '/*.php') ?: [] as $file) {
        @unlink($file);
        $count++;
    }
}
$results[] = "Deleted {$count} compiled views";

// 3. Clear bootstrap cache
$cacheDir = __DIR__ . '/laravel/bootstrap/cache';
if (is_dir($cacheDir)) {
    foreach (glob($cacheDir . '/*.php') ?: [] as $file) {
        if (basename($file) !== '.gitignore') {
            @unlink($file);
        }
    }
    $results[] = 'Bootstrap cache cleared';
}

// 4. Clear data cache
$dataDir = __DIR__ . '/laravel/storage/framework/cache/data';
if (is_dir($dataDir)) {
    foreach (glob($dataDir . '/*') ?: [] as $item) {
        if (is_file($item)) @unlink($item);
        if (is_dir($item)) {
            foreach (glob($item . '/*') ?: [] as $sub) @unlink($sub);
            @rmdir($item);
        }
    }
    $results[] = 'Data cache cleared';
}

header('Content-Type: text/plain');
echo "PURGE COMPLETE\n\n";
echo implode("\n", $results) . "\n\n";
echo "Now visit the site normally. You should see all recent changes.\n";
echo "Delete this file when done: /_purge.php\n";
