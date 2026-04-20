<?php
/**
 * One-shot Arabic reset runner. Visit once in browser:
 *   https://<your-domain>/_reset_arabic.php?token=delos-deploy-2026
 *
 * Force-updates every *_ar column across branches, brands, services,
 * projects, employees, site_settings, and page_contents back to the
 * formal MSA baseline defined in lang/ar/*.php and each seeder's rows().
 *
 * The ‎"وكيل/وكلاء" terminology is preserved — nothing is switched to
 * "شريك". English and Italian columns are untouched.
 *
 * Idempotent — running twice is harmless. DELETE this file from the
 * Hostinger file manager after running; leaving it is a security risk.
 */

$expectedToken = 'delos-deploy-2026';
if (($_GET['token'] ?? '') !== $expectedToken) {
    http_response_code(403);
    header('Content-Type: text/plain');
    echo "Forbidden. Append ?token=… to the URL.\n";
    exit;
}

require __DIR__ . '/laravel/vendor/autoload.php';
$app = require __DIR__ . '/laravel/bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

header('Content-Type: text/plain; charset=utf-8');
echo "DELOS ARABIC RESET RUNNER\n";
echo str_repeat('=', 60) . "\n\n";

// Clear caches so config('editable_pages') + lang files are fresh.
$output = new \Symfony\Component\Console\Output\BufferedOutput();
foreach (['config:clear', 'cache:clear', 'view:clear'] as $cmd) {
    try {
        $kernel->call($cmd, [], $output);
    } catch (\Throwable $e) {
        echo "- {$cmd}: ERROR {$e->getMessage()}\n";
    }
}
if (function_exists('opcache_reset')) { opcache_reset(); }
echo "Caches cleared.\n\n";

echo "Running ResetArabicFromLangSeeder...\n";
echo str_repeat('-', 60) . "\n";
$output = new \Symfony\Component\Console\Output\BufferedOutput();
try {
    $kernel->call('db:seed', [
        '--class' => 'Database\\Seeders\\ResetArabicFromLangSeeder',
        '--force' => true,
    ], $output);
    echo trim($output->fetch()) . "\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "DONE. Now DELETE this file from the Hostinger file manager.\n";
