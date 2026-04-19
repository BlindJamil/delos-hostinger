<?php

/**
 * Diagnose why the admin panel edits aren't showing up on the public site.
 *
 * Run this on Hostinger via SSH in the Laravel app directory:
 *
 *   php artisan tinker --execute="require 'scripts/diagnose-admin-save.php';"
 *
 * It reports three things, in order:
 *
 *   1. Which version of the code is live (commit hash + deploy time)
 *   2. Whether Laravel has any cached config/routes/views
 *   3. Whether the home.about.overline value in the DB matches what the
 *      public site is currently serving (cache freshness check)
 *
 * Nothing is mutated — read-only diagnostic only.
 */

use App\Models\PageContent;

echo "─── 1. Code version check ───" . PHP_EOL;
$commitFile = base_path('.git/HEAD');
if (file_exists($commitFile)) {
    $head = trim(file_get_contents($commitFile));
    if (str_starts_with($head, 'ref: ')) {
        $refPath = base_path('.git/' . substr($head, 5));
        $commit = file_exists($refPath) ? substr(trim(file_get_contents($refPath)), 0, 7) : '(unknown)';
    } else {
        $commit = substr($head, 0, 7);
    }
    echo "  Current commit: {$commit}" . PHP_EOL;
} else {
    echo "  (.git not present — deployed via zip or FTP, not git pull)" . PHP_EOL;
}
$indexPhp = public_path('index.php');
if (file_exists($indexPhp)) {
    echo "  index.php mtime: " . date('Y-m-d H:i:s', filemtime($indexPhp)) . PHP_EOL;
}

echo PHP_EOL . "─── 2. Laravel cache state ───" . PHP_EOL;
$caches = [
    'config'  => base_path('bootstrap/cache/config.php'),
    'routes'  => base_path('bootstrap/cache/routes-v7.php'),
    'events'  => base_path('bootstrap/cache/events.php'),
];
foreach ($caches as $name => $path) {
    if (file_exists($path)) {
        echo "  {$name}: CACHED (mtime " . date('Y-m-d H:i:s', filemtime($path)) . ")" . PHP_EOL;
    } else {
        echo "  {$name}: not cached" . PHP_EOL;
    }
}

$viewsDir = base_path('storage/framework/views');
if (is_dir($viewsDir)) {
    $count = count(glob($viewsDir . '/*.php'));
    echo "  compiled views: {$count} files" . PHP_EOL;
}

echo PHP_EOL . "─── 3. home.about.overline freshness ───" . PHP_EOL;
$row = PageContent::where('key', 'home.about.overline')->first();
echo "  DB value (EN): " . var_export($row?->value_en, true) . PHP_EOL;
echo "  DB value (AR): " . var_export($row?->value_ar, true) . PHP_EOL;
echo "  DB value (IT): " . var_export($row?->value_it, true) . PHP_EOL;
echo "  Last updated: " . ($row?->updated_at?->format('Y-m-d H:i:s') ?? '(never)') . PHP_EOL;

echo PHP_EOL . "─── 4. pcontent() resolves to ───" . PHP_EOL;
echo "  via pcontent helper:      " . var_export(pcontent('home.about.overline'), true) . PHP_EOL;
echo "  via direct table read:    " . var_export($row?->value_en, true) . PHP_EOL;
echo "  ↑ if these differ, PageContent cache is stale." . PHP_EOL;

echo PHP_EOL . "─── 5. Editable registry contains the key? ───" . PHP_EOL;
$found = false;
foreach (config('editable_pages.home.sections', []) as $slug => $section) {
    foreach ($section['fields'] ?? [] as $f) {
        if ($f['key'] === 'home.about.overline') {
            echo "  ✓ Found in section [{$slug}] type [{$f['type']}]" . PHP_EOL;
            $found = true;
            break 2;
        }
    }
}
if (!$found) {
    echo "  ✗ NOT FOUND — config cache is serving an outdated registry." . PHP_EOL;
    echo "    Fix: php artisan config:clear" . PHP_EOL;
}

echo PHP_EOL . "─── 6. PHP input limits (affects large form saves) ───" . PHP_EOL;
echo "  max_input_vars: " . ini_get('max_input_vars') . PHP_EOL;
echo "  post_max_size:  " . ini_get('post_max_size') . PHP_EOL;

echo PHP_EOL . "─── Suggested fix, depending on what's above ───" . PHP_EOL;
echo "  • If §2 shows any CACHED file → run:  php artisan cache:clear && php artisan config:clear && php artisan view:clear && php artisan route:clear" . PHP_EOL;
echo "  • If §5 says NOT FOUND → same command above, then re-test" . PHP_EOL;
echo "  • If §6 shows max_input_vars < 1000 → check .htaccess + .user.ini are in the webroot" . PHP_EOL;
echo "  • If §3 DB value looks right but public site doesn't match → LiteSpeed/browser cache; hard-refresh (Cmd-Shift-R)" . PHP_EOL;
