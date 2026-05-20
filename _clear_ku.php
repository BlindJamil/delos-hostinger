<?php
/**
 * One-shot Kurdish wipe.
 *
 *   https://<your-domain>/_clear_ku.php?token=delos-clear-ku-2026
 *
 * What it does:
 *   1. Sets every `value_ku` on page_contents back to NULL
 *   2. Sets every `*_ku` column on brands / branches / employees /
 *      projects / services back to NULL
 *   3. Flushes the PageContent cache + OPcache
 *
 * After this runs, /ku/ on the live site will render exactly the
 * same English text as /en/ for everything sourced from the DB.
 * The lang/ku/*.php files stay in place (so localhost is unchanged
 * if its DB still has Kurdish populated). As the user fills in any
 * Kurdish field from /verify-admin-panel-7k3m, that one section
 * starts rendering in Kurdish — the rest stay English until edited.
 *
 * Idempotent. Re-running this is safe. DELETE the file from cPanel
 * once you've used it.
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

// --- 1. Token gate -----------------------------------------------------
$expectedToken = 'delos-clear-ku-2026';
if (($_GET['token'] ?? '') !== $expectedToken) {
    http_response_code(403);
    header('Content-Type: text/plain');
    echo "Forbidden. Append ?token=… to the URL.\n";
    exit;
}

// --- 2. Boot Laravel ---------------------------------------------------
require __DIR__ . '/laravel/vendor/autoload.php';
$app = require __DIR__ . '/laravel/bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain; charset=utf-8');
echo "DELOS KURDISH WIPE\n";
echo str_repeat('=', 60) . "\n\n";

// --- 3. Run the clears -------------------------------------------------
$clears = [
    'page_contents' => ['value_ku'],
    'brands'        => ['category_ku', 'origin_ku', 'description_ku', 'specialties_ku'],
    'branches'      => ['name_ku', 'address_ku', 'hours_ku', 'established_ku'],
    'employees'     => ['name_ku', 'role_ku', 'achievement_ku'],
    'projects'      => ['title_ku', 'type_label_ku'],
    'services'      => ['name_ku', 'description_ku', 'features_ku'],
];

foreach ($clears as $table => $columns) {
    if (!Schema::hasTable($table)) {
        echo "SKIP $table — table missing\n";
        continue;
    }
    $update = [];
    $touched = [];
    foreach ($columns as $col) {
        if (Schema::hasColumn($table, $col)) {
            $update[$col] = null;
            $touched[] = $col;
        }
    }
    if (empty($update)) {
        echo "SKIP $table — no _ku columns present\n";
        continue;
    }
    $affected = DB::table($table)->update($update);
    echo "CLEARED $table → " . implode(', ', $touched) . " (rows touched: $affected)\n";
}

echo "\n";

// --- 4. Cache flush ----------------------------------------------------
echo "Flushing caches:\n";
try {
    Cache::forget(\App\Models\PageContent::CACHE_KEY);
    echo "- PageContent cache key forgotten\n";
} catch (\Throwable $e) {
    echo "- (PageContent cache skip: " . $e->getMessage() . ")\n";
}

foreach (['cache:clear', 'view:clear', 'config:clear'] as $cmd) {
    $kernel->call($cmd);
    echo "- artisan $cmd\n";
}

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "- OPcache reset\n";
}

echo "\n";
echo str_repeat('=', 60) . "\n";
echo "WIPE COMPLETE.\n\n";
echo "Now visit /ku/ — it should render with English copy everywhere.\n";
echo "Go into /verify-admin-panel-7k3m → Page Content Editor and fill\n";
echo "the KU tab on any section you want to translate.\n\n";
echo "DELETE this file (_clear_ku.php) from cPanel File Manager when done.\n";
