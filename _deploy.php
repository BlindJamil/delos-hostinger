<?php
/**
 * One-shot post-deploy runner. Visit once in browser after pushing a release:
 *   https://<your-domain>/_deploy.php?token=delos-deploy-2026
 *
 * What it does (in order):
 *   1. Runs all pending migrations (--force; safe for production)
 *   2. Seeds baseline data (idempotent — uses firstOrCreate)
 *   3. Ensures public/storage symlink exists so uploaded media is reachable
 *   4. Clears Laravel view/config/route/cache compiled files
 *   5. Resets PHP OPcache
 *
 * Idempotent — running twice is harmless. When done, DELETE this file
 * from the Hostinger file manager; leaving it around is a security risk.
 */

// --- 1. Token gate -----------------------------------------------------
$expectedToken = 'delos-deploy-2026';
if (($_GET['token'] ?? '') !== $expectedToken) {
    http_response_code(403);
    header('Content-Type: text/plain');
    echo "Forbidden. Append ?token=… to the URL.\n";
    exit;
}

// --- 2. Boot Laravel console kernel ------------------------------------
require __DIR__ . '/laravel/vendor/autoload.php';
$app = require __DIR__ . '/laravel/bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

header('Content-Type: text/plain; charset=utf-8');
echo "DELOS POST-DEPLOY RUNNER\n";
echo str_repeat('=', 60) . "\n\n";

function runArtisan($kernel, string $command, array $params = []): string
{
    $output = new \Symfony\Component\Console\Output\BufferedOutput();
    try {
        $kernel->call($command, $params, $output);
        return trim($output->fetch());
    } catch (\Throwable $e) {
        return 'ERROR: ' . $e->getMessage();
    }
}

// --- 3. Clear stale caches FIRST so migrations + seeders see fresh config
echo "[1/6] CLEAR STALE CACHES\n";
echo str_repeat('-', 60) . "\n";
foreach (['config:clear', 'cache:clear', 'view:clear'] as $cmd) {
    echo "- {$cmd}: " . runArtisan($kernel, $cmd, []) . "\n";
}
if (function_exists('opcache_reset')) { opcache_reset(); echo "- OPcache reset\n"; }
echo "\n";

// --- 4. Migrations -----------------------------------------------------
echo "[2/6] MIGRATIONS\n";
echo str_repeat('-', 60) . "\n";
echo runArtisan($kernel, 'migrate', ['--force' => true]) . "\n\n";

// --- 5. Seeders + direct backfill ----------------------------------------
echo "[3/6] SEEDERS\n";
echo str_repeat('-', 60) . "\n";
// SAFE deployer seeder list. The 5 lang-based content seeders
// (Employees / Projects / Brands / Services / Branches) are
// INTENTIONALLY EXCLUDED here — firstOrCreate is update-safe but
// not delete-safe, so re-running them on every deploy resurrects
// admin-deleted records (Ahmed K., Villa Moderna Kitchen, etc.).
// If a fresh install ever needs to seed those, run them once from
// SSH: php artisan db:seed --class=EmployeesFromLangSeeder
$seeders = [
    'Database\\Seeders\\SiteSettingsSeeder',      // system config (site-wide settings)
    'Database\\Seeders\\AdminUserSeeder',         // admin auth seed
    'Database\\Seeders\\PageContentFromLangSeeder', // safe: backfills empty fields only
];
foreach ($seeders as $seederClass) {
    $name = substr($seederClass, strrpos($seederClass, '\\') + 1);
    echo "- {$name}: ";
    $result = runArtisan($kernel, 'db:seed', [
        '--class' => $seederClass,
        '--force' => true,
    ]);
    // Most seeders output "INFO  Database seeding completed successfully."
    // when they run; condense that to a tick.
    if (stripos($result, 'completed successfully') !== false || $result === '') {
        echo "ok\n";
    } else {
        echo "\n  " . str_replace("\n", "\n  ", $result) . "\n";
    }
}

// Direct PHP backfill: walk every page_content row and fill empty
// locale values from the lang file using data_get(). This is the
// nuclear option — doesn't rely on the seeder, config cache, or
// Laravel's translator. Reads files directly.
echo "\n  Direct backfill of empty fields:\n";
$langDir = __DIR__ . '/laravel/lang';
$backfilled = 0;
try {
    $allRows = \App\Models\PageContent::all();
    $fileCache = [];
    foreach ($allRows as $pcRow) {
        $key = $pcRow->key;
        $dotPos = strpos($key, '.');
        if ($dotPos === false) continue;
        $group = substr($key, 0, $dotPos);
        $path = substr($key, $dotPos + 1);
        $dirty = false;
        foreach (['en', 'ar', 'it'] as $loc) {
            $col = "value_{$loc}";
            if ($pcRow->$col !== null && $pcRow->$col !== '') continue;
            $ck = "{$loc}/{$group}";
            if (!isset($fileCache[$ck])) {
                $f = "{$langDir}/{$loc}/{$group}.php";
                $fileCache[$ck] = file_exists($f) ? include $f : [];
            }
            $v = data_get($fileCache[$ck], $path);
            if ($v !== null && !is_array($v)) {
                $pcRow->$col = (string) $v;
                $dirty = true;
            }
        }
        if ($dirty) { $pcRow->save(); $backfilled++; }
    }
    echo "  {$backfilled} rows backfilled from lang files.\n";
} catch (\Throwable $e) {
    echo "  Backfill error: " . $e->getMessage() . "\n";
}
echo "\n";

// --- 5. Storage symlink -----------------------------------------------
echo "[4/6] STORAGE SYMLINK\n";
echo str_repeat('-', 60) . "\n";
// Hostinger's document root is the top-level folder (this script's dir).
// Laravel's `storage:link` command creates laravel/public/storage -> ../
// storage/app/public. But on this deployment the public root is at the
// top level, NOT laravel/public — so we symlink <root>/storage too.
$linkTarget = __DIR__ . '/laravel/storage/app/public';
$linkPath = __DIR__ . '/storage';
if (!file_exists($linkTarget)) {
    @mkdir($linkTarget, 0755, true);
}
if (!file_exists($linkPath) && !is_link($linkPath)) {
    if (@symlink($linkTarget, $linkPath)) {
        echo "Created: /storage -> /laravel/storage/app/public\n";
    } else {
        echo "WARNING: could not create /storage symlink.\n";
        echo "Manually run in SSH: ln -s " . $linkTarget . " " . $linkPath . "\n";
    }
} else {
    echo "Already linked: /storage\n";
}
// Ensure page-content uploads dir exists.
$uploadsDir = $linkTarget . '/uploads/page-content';
if (!is_dir($uploadsDir)) {
    @mkdir($uploadsDir, 0755, true);
    echo "Created upload dir: /storage/uploads/page-content\n";
}
echo "\n";

// --- 6. Clear Laravel caches -----------------------------------------
echo "[5/6] CLEAR POST-SEED CACHES\n";
echo str_repeat('-', 60) . "\n";
foreach (['view:clear', 'config:clear', 'route:clear', 'cache:clear'] as $cmd) {
    $r = runArtisan($kernel, $cmd, []);
    echo "- {$cmd}: " . ($r === '' ? 'ok' : $r) . "\n";
}
echo "\n";

// --- 7. OPcache reset --------------------------------------------------
echo "[6/6] OPCACHE RESET\n";
echo str_repeat('-', 60) . "\n";
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache cleared.\n";
} else {
    echo "OPcache not available (nothing to do).\n";
}
echo "\n";

// --- 8. Done -----------------------------------------------------------
echo str_repeat('=', 60) . "\n";
echo "DEPLOY COMPLETE.\n\n";
echo "Next steps:\n";
echo "  1. Visit / and /verify-admin-panel-7k3m to confirm all is well.\n";
echo "  2. Log in with the admin credentials from AdminUserSeeder.\n";
echo "  3. DELETE this file (_deploy.php) from the Hostinger file manager.\n";
