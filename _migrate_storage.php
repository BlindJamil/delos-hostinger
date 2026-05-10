<?php
// One-shot storage migration fetcher.
// Pulls storage-backup.tar.gz from Hostinger, extracts into laravel/storage/app/.
// Token-protected. DELETE THIS FILE FROM THE REPO after a successful run.

$token = 'delos-mig-7k9q-2026';
if (($_GET['t'] ?? '') !== $token) {
    http_response_code(403);
    header('Content-Type: text/plain');
    exit('forbidden');
}

set_time_limit(0);
ignore_user_abort(false);
@ini_set('memory_limit', '512M');

header('Content-Type: text/plain; charset=utf-8');

$source    = 'https://darkgoldenrod-dove-850949.hostingersite.com/storage-backup.tar.gz';
$tarGz     = __DIR__ . '/laravel/storage/app/storage-backup.tar.gz';
$tar       = __DIR__ . '/laravel/storage/app/storage-backup.tar';
$extractTo = __DIR__ . '/laravel/storage/app/';

echo "Source: $source\n";
echo "Dest:   $tarGz\n";
echo "----\n";

// 1. Download via curl
echo "Downloading...\n";
$fp = fopen($tarGz, 'w+');
if (!$fp) { exit("FAIL: could not open destination for writing\n"); }
$ch = curl_init($source);
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 900);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
$ok   = curl_exec($ch);
$err  = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
fclose($fp);

if (!$ok || $code !== 200) {
    @unlink($tarGz);
    exit("FAIL: download HTTP $code · $err\n");
}

$size = filesize($tarGz);
echo "Downloaded: " . round($size / 1024 / 1024, 2) . " MB\n\n";

// 2. Decompress .tar.gz -> .tar (PharData does not handle .tar.gz extraction directly,
//    must decompress first, then extract).
echo "Decompressing tar.gz -> tar...\n";
try {
    $phar = new PharData($tarGz);
    if (file_exists($tar)) { @unlink($tar); }
    $phar->decompress();
} catch (Throwable $e) {
    exit("FAIL: decompress · " . $e->getMessage() . "\n");
}

if (!file_exists($tar)) {
    exit("FAIL: .tar not produced after decompress\n");
}

// 3. Extract the .tar
echo "Extracting...\n";
try {
    $tarPhar = new PharData($tar);
    $tarPhar->extractTo($extractTo, null, true);
} catch (Throwable $e) {
    exit("FAIL: extract · " . $e->getMessage() . "\n");
}

// 4. Permissions sweep on the extracted tree (775 dirs, 664 files)
echo "Setting permissions...\n";
$rootPublic = __DIR__ . '/laravel/storage/app/public';
if (is_dir($rootPublic)) {
    $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootPublic, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iter as $path) {
        @chmod($path->getPathname(), $path->isDir() ? 0775 : 0664);
    }
    @chmod($rootPublic, 0775);
}

// 5. Cleanup intermediate archives
@unlink($tarGz);
@unlink($tar);
echo "Cleanup: archives deleted.\n\n";

// 6. Sanity check
$count = 0;
if (is_dir($rootPublic)) {
    $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPublic, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($iter as $f) { if ($f->isFile()) { $count++; } }
}
echo "Files now under storage/app/public/: $count\n";

echo "\nDONE. Delete this file (_migrate_storage.php) and the Hostinger storage-backup.tar.gz when verified.\n";
