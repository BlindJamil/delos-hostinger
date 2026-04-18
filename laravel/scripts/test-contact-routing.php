<?php

/**
 * Temporarily point ALL four branch phone/whatsapp/email fields at a single
 * tester so the new contact form can be end-to-end verified on production
 * without bothering the real branch staff.
 *
 * MODES:
 *   set     — snapshots the current values to storage/app/branches-backup.json,
 *             then overwrites phone/whatsapp/email on every active branch.
 *   restore — reads the snapshot and restores the original values. Safe to
 *             run twice; the backup file is deleted after a successful
 *             restore so a stale backup can't accidentally wipe live data.
 *   status  — prints the current phone/whatsapp/email on every branch plus
 *             whether a backup file exists. Read-only.
 *
 * Run via tinker so the Laravel app context is bootstrapped:
 *
 *   # apply test values
 *   php artisan tinker --execute="\$mode='set';    require 'scripts/test-contact-routing.php';"
 *
 *   # after testing, restore real values
 *   php artisan tinker --execute="\$mode='restore';require 'scripts/test-contact-routing.php';"
 *
 *   # just inspect
 *   php artisan tinker --execute="\$mode='status'; require 'scripts/test-contact-routing.php';"
 */

use App\Models\Branch;
use Illuminate\Support\Facades\Storage;

// ─── Config — the tester's contact info ─────────────────────────
const TEST_EMAIL    = 'blindmizuri71@gmail.com';
const TEST_WHATSAPP = '07824412345';
const TEST_PHONE    = '07824412345';
const BACKUP_PATH   = 'branches-backup.json'; // lives on the 'local' disk (storage/app/)

$mode = $mode ?? 'status';

if (!in_array($mode, ['set', 'restore', 'status'], true)) {
    echo "Unknown mode: {$mode}. Use 'set', 'restore', or 'status'." . PHP_EOL;
    return;
}

// ─── STATUS ─────────────────────────────────────────────────────
if ($mode === 'status') {
    echo '── Branch contact status ─────────────────' . PHP_EOL;
    Branch::query()->active()->ordered()->get()->each(function ($b) {
        printf("  %-14s  phone=%-20s  whatsapp=%-20s  email=%s" . PHP_EOL,
            $b->city_key,
            $b->phone     ?: '(empty)',
            $b->whatsapp  ?: '(empty)',
            $b->email     ?: '(empty)'
        );
    });
    echo PHP_EOL;
    echo 'Backup file: ' . (Storage::disk('local')->exists(BACKUP_PATH)
        ? 'EXISTS at storage/app/' . BACKUP_PATH
        : '(none — nothing to restore)') . PHP_EOL;
    return;
}

// ─── SET — snapshot then overwrite ──────────────────────────────
if ($mode === 'set') {
    if (Storage::disk('local')->exists(BACKUP_PATH)) {
        echo "✗ Refusing to overwrite existing backup at storage/app/" . BACKUP_PATH . PHP_EOL;
        echo "  Run with \$mode='restore' first to return branches to real values," . PHP_EOL;
        echo "  then re-run \$mode='set' if you want to apply test values again." . PHP_EOL;
        return;
    }

    $snapshot = Branch::query()->get()->map(fn ($b) => [
        'id'       => $b->id,
        'city_key' => $b->city_key,
        'phone'    => $b->phone,
        'whatsapp' => $b->whatsapp,
        'email'    => $b->email,
    ])->values()->all();

    Storage::disk('local')->put(BACKUP_PATH, json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo '✓ Backup saved to storage/app/' . BACKUP_PATH . ' (' . count($snapshot) . ' rows)' . PHP_EOL;

    $updated = 0;
    foreach ($snapshot as $row) {
        $b = Branch::find($row['id']);
        if (!$b) continue;
        $b->phone    = TEST_PHONE;
        $b->whatsapp = TEST_WHATSAPP;
        $b->email    = TEST_EMAIL;
        $b->save();
        $updated++;
    }
    echo "✓ Overwrote phone/whatsapp/email on {$updated} branch row(s)." . PHP_EOL;
    echo "  Every showroom option on /contact will now route to you." . PHP_EOL;
    return;
}

// ─── RESTORE ───────────────────────────────────────────────────
if ($mode === 'restore') {
    if (!Storage::disk('local')->exists(BACKUP_PATH)) {
        echo '✗ No backup file at storage/app/' . BACKUP_PATH . ' — nothing to restore.' . PHP_EOL;
        return;
    }

    $snapshot = json_decode(Storage::disk('local')->get(BACKUP_PATH), true);
    if (!is_array($snapshot)) {
        echo '✗ Backup file is not valid JSON — refusing to restore.' . PHP_EOL;
        return;
    }

    $restored = 0;
    foreach ($snapshot as $row) {
        $b = Branch::find($row['id']);
        if (!$b) { echo "  ! branch id {$row['id']} ({$row['city_key']}) not found, skipped" . PHP_EOL; continue; }
        $b->phone    = $row['phone'];
        $b->whatsapp = $row['whatsapp'];
        $b->email    = $row['email'];
        $b->save();
        $restored++;
    }
    Storage::disk('local')->delete(BACKUP_PATH);
    echo "✓ Restored phone/whatsapp/email on {$restored} branch row(s)." . PHP_EOL;
    echo '✓ Backup file deleted (storage/app/' . BACKUP_PATH . ')' . PHP_EOL;
    return;
}
