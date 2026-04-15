<?php
/**
 * Shows the last ~50 log entries from Laravel's daily log file. Visit:
 *   https://<your-domain>/_log.php?token=delos-deploy-2026
 *
 * Use this when the site is 500ing to see the actual exception + stack
 * trace. Delete this file after use.
 */

$expectedToken = 'delos-deploy-2026';
if (($_GET['token'] ?? '') !== $expectedToken) {
    http_response_code(403);
    header('Content-Type: text/plain');
    exit("Forbidden. Append ?token=… to the URL.\n");
}

header('Content-Type: text/plain; charset=utf-8');

$logsDir = __DIR__ . '/laravel/storage/logs';
if (!is_dir($logsDir)) {
    echo "No log directory at {$logsDir}\n";
    exit;
}

// Grab the most recently modified .log file (Laravel can have
// daily rotation, single file, or both).
$logs = glob($logsDir . '/*.log') ?: [];
if (empty($logs)) {
    echo "No log files in {$logsDir}\n";
    exit;
}
usort($logs, fn ($a, $b) => filemtime($b) <=> filemtime($a));
$path = $logs[0];

echo "Log file: {$path}\n";
echo "Size: " . number_format(filesize($path)) . " bytes\n";
echo "Modified: " . date('Y-m-d H:i:s', filemtime($path)) . "\n";
echo str_repeat('=', 70) . "\n\n";

// Read the last 200 KB (plenty for a stack trace or two without
// streaming the whole file into memory if it's huge).
$maxBytes = 200 * 1024;
$fh = fopen($path, 'rb');
if (!$fh) {
    echo "Could not open log file.\n";
    exit;
}
$size = filesize($path);
$offset = max(0, $size - $maxBytes);
fseek($fh, $offset);
$content = stream_get_contents($fh);
fclose($fh);

// Show only the last ~50 log entries (stack traces can be long).
// Each Laravel entry starts with "[YYYY-MM-DD HH:MM:SS]".
$entries = preg_split('/(?=^\[\d{4}-\d{2}-\d{2})/m', $content) ?: [$content];
$entries = array_filter($entries, fn ($e) => trim($e) !== '');
$entries = array_slice($entries, -50);

foreach ($entries as $entry) {
    echo $entry;
    if (!str_ends_with($entry, "\n")) echo "\n";
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "End of log. Delete this file (_log.php) after use.\n";
