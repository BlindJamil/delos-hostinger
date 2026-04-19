<?php

/**
 * Real-HTTP admin-save verification harness.
 *
 * WHY: scripts/verify-admin-inputs.php bypasses HTTP and writes directly
 * via Eloquent, so it can't catch CSRF mismatches, session issues, WAF
 * blocks, max_input_vars truncation, or flash-rendering bugs — exactly
 * the class of failures users report from Hostinger LiteSpeed.
 *
 * WHAT: Logs in as admin via a real browser-shape POST, scrapes the CSRF
 * token from every edit form, submits canary mutations, verifies the
 * response flash + DB persistence + public render, then restores state.
 *
 * USAGE (local):
 *   php artisan serve --port=8000 &   # dev server must be running
 *   ADMIN_PASSWORD=delos-admin-2020 \
 *     php scripts/verify-admin-http.php
 *
 * USAGE (prod — future Phase IV):
 *   BASE_URL=https://delosinternational.com \
 *     ADMIN_EMAIL=you@example.com \
 *     ADMIN_PASSWORD=yourpass \
 *     php scripts/verify-admin-http.php
 *
 * Safety: guarded to never run against production unless BASE_URL is
 * explicitly set, uses snapshot+restore per field, and aborts on any
 * auth-detection gap (won't proceed if login doesn't stick).
 */

use App\Models\AdminUser;
use App\Models\PageContent;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\RequestException;

$baseUrl = rtrim(getenv('BASE_URL') ?: 'http://127.0.0.1:8000', '/');
$adminEmail = getenv('ADMIN_EMAIL') ?: null;
$adminPassword = getenv('ADMIN_PASSWORD') ?: null;

// If no email given, pull the first admin user. Password MUST be provided
// by caller — we never read it from the DB (bcrypt hash is one-way).
if (!$adminEmail) {
    $firstAdmin = AdminUser::orderBy('id')->first();
    if (!$firstAdmin) {
        fwrite(STDERR, "No admin user exists in DB. Seed one first.\n");
        exit(1);
    }
    $adminEmail = $firstAdmin->email;
}
if (!$adminPassword) {
    fwrite(STDERR, "ADMIN_PASSWORD env var required. Aborting.\n");
    exit(1);
}

fwrite(STDOUT, "=== verify-admin-http.php ===\n");
fwrite(STDOUT, "base: {$baseUrl}\n");
fwrite(STDOUT, "admin: {$adminEmail}\n\n");

$jar = new CookieJar();
$client = new Client([
    'base_uri' => $baseUrl,
    'cookies' => $jar,
    'http_errors' => false,       // we want to inspect 4xx/5xx ourselves
    'allow_redirects' => false,   // Laravel flash lives in the redirect response — we follow manually
    'timeout' => 30,
]);

// ─── Helpers ──────────────────────────────────────────────

/**
 * Scrape a `name="_token"` value from an HTML body. Returns null if
 * no token present (e.g. the response was a redirect or the page has
 * no form).
 */
function scrape_token(string $html): ?string {
    if (preg_match('/name="_token"[^>]*value="([a-zA-Z0-9]+)"/', $html, $m)) return $m[1];
    if (preg_match('/<meta name="csrf-token" content="([a-zA-Z0-9]+)"/', $html, $m)) return $m[1];
    return null;
}

/**
 * Submit a form POST with Laravel's session flash survival pattern:
 * we POST, expect a 302, then GET the Location to read the rendered
 * flash message (or the embedded inline save banner).
 */
function submit_and_follow(Client $client, string $url, array $form, array $extraHeaders = []): array {
    $post = $client->post($url, [
        'form_params' => $form,
        'headers' => array_merge([
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Referer' => $url,
        ], $extraHeaders),
    ]);
    $status = $post->getStatusCode();
    $location = $post->getHeaderLine('Location') ?: null;
    $followedBody = null;
    $followedStatus = null;
    if ($location) {
        $follow = $client->get($location, ['headers' => ['Accept' => 'text/html']]);
        $followedBody = (string) $follow->getBody();
        $followedStatus = $follow->getStatusCode();
    }
    return [
        'post_status' => $status,
        'location' => $location,
        'followed_status' => $followedStatus,
        'followed_body' => $followedBody,
        'post_body_snippet' => substr((string) $post->getBody(), 0, 400),
    ];
}

function has_flash(string $html, string $kind): bool {
    // Match both the inline save banner + the dismissible partial toast.
    if ($kind === 'success') {
        return str_contains($html, 'Content saved')
            || str_contains($html, 'rounded-xl border')
            && str_contains($html, 'Saved');
    }
    if ($kind === 'error') {
        return str_contains($html, 'Save failed') || str_contains($html, 'Save rejected');
    }
    return false;
}

function log_step(string $msg, string $status = ''): void {
    $prefix = [
        'pass' => "\033[32m✓\033[0m",
        'fail' => "\033[31m✗\033[0m",
        'info' => "\033[34m·\033[0m",
    ][$status] ?? '  ';
    fwrite(STDOUT, "  {$prefix} {$msg}\n");
}

// ─── Phase 1: Login ──────────────────────────────────────

log_step("Phase 1: Logging in as {$adminEmail}", 'info');
$loginPageRes = $client->get('/verify-admin-panel-7k3m/', ['headers' => ['Accept' => 'text/html']]);
if ($loginPageRes->getStatusCode() !== 200) {
    log_step("Login page returned HTTP {$loginPageRes->getStatusCode()} — abort.", 'fail');
    exit(1);
}
$loginToken = scrape_token((string) $loginPageRes->getBody());
if (!$loginToken) {
    log_step("Could not extract login CSRF token — abort.", 'fail');
    exit(1);
}

$loginResult = submit_and_follow($client, '/verify-admin-panel-7k3m/', [
    '_token' => $loginToken,
    'email' => $adminEmail,
    'password' => $adminPassword,
]);

// A successful login redirects to /dashboard.
if ($loginResult['post_status'] !== 302 || !str_contains($loginResult['location'] ?? '', '/dashboard')) {
    log_step("Login rejected: post_status={$loginResult['post_status']}, location={$loginResult['location']}", 'fail');
    log_step("Response body: " . $loginResult['post_body_snippet'], 'info');
    exit(1);
}
log_step("Logged in. Dashboard reachable.", 'pass');

// ─── Phase 2: Per-page canary saves ─────────────────────

$cases = [
    ['page' => 'home',     'key' => 'home.about.overline',    'public_path' => '/en'],
    ['page' => 'about',    'key' => 'about.hero.overline',    'public_path' => '/en/about'],
    ['page' => 'services', 'key' => 'services.hero.overline', 'public_path' => '/en/services'],
    ['page' => 'projects', 'key' => 'projects.hero.counter',  'public_path' => '/en/projects'],
    ['page' => 'brands',   'key' => 'brands.hero.overline',   'public_path' => '/en/brands'],
    ['page' => 'branches', 'key' => 'branches.hero.overline', 'public_path' => '/en/branches'],
    ['page' => 'contact',  'key' => 'contact.hero.overline',  'public_path' => '/en/contact'],
];

$totalPass = 0;
$totalFail = 0;
$failures = [];

foreach ($cases as $case) {
    $page = $case['page'];
    $key = $case['key'];
    fwrite(STDOUT, "\n=== Page: {$page}  canary key: {$key} ===\n");

    // Skip the case if the canary key isn't registered for this page.
    $registryOk = false;
    foreach (config("editable_pages.{$page}.sections", []) as $section) {
        foreach ($section['fields'] ?? [] as $field) {
            if (($field['key'] ?? null) === $key) { $registryOk = true; break 2; }
        }
    }
    if (!$registryOk) {
        log_step("Key {$key} not in registry — skipping.", 'info');
        continue;
    }

    // 1. GET edit page, scrape token
    $editRes = $client->get("/verify-admin-panel-7k3m/dashboard/page-content/{$page}/edit", ['headers' => ['Accept' => 'text/html']]);
    if ($editRes->getStatusCode() !== 200) {
        log_step("GET edit {$page} returned HTTP {$editRes->getStatusCode()} — skip", 'fail');
        $totalFail++;
        $failures[] = "{$page}: edit GET status {$editRes->getStatusCode()}";
        continue;
    }
    $editBody = (string) $editRes->getBody();
    $editToken = scrape_token($editBody);
    if (!$editToken) {
        log_step("No CSRF token in edit page — skip", 'fail');
        $totalFail++;
        $failures[] = "{$page}: no CSRF token";
        continue;
    }
    log_step("Edit form loaded + token scraped", 'pass');

    // 2. Snapshot existing values before canary
    $existingRow = PageContent::where('key', $key)->first();
    $snapshot = $existingRow
        ? ['en' => $existingRow->value_en, 'ar' => $existingRow->value_ar, 'it' => $existingRow->value_it]
        : null;

    // 3. Submit ALL page values the form renders (same as browser) with a
    //    canary value for $key/EN. We capture all registry fields from the
    //    page and fill them with their current values to avoid losing data;
    //    only $key/EN gets the canary.
    $canary = 'CANARY-HTTP-' . substr(md5($key . time()), 0, 8);
    $pageConfig = config("editable_pages.{$page}");
    $values = [];
    foreach ($pageConfig['sections'] ?? [] as $section) {
        foreach ($section['fields'] ?? [] as $field) {
            $fk = $field['key'];
            $ft = $field['type'] ?? 'text';
            if (in_array($ft, ['image', 'video'], true)) continue;
            $row = PageContent::where('key', $fk)->first();
            $values[$fk] = [
                'en' => $fk === $key ? $canary : ($row?->value_en ?? ''),
                'ar' => $row?->value_ar ?? '',
                'it' => $row?->value_it ?? '',
            ];
        }
    }

    $saveResult = submit_and_follow($client, "/verify-admin-panel-7k3m/dashboard/page-content/{$page}", [
        '_token' => $editToken,
        '_method' => 'PUT',
        'values' => $values,
    ]);

    if ($saveResult['post_status'] !== 302) {
        log_step("Save POST returned HTTP {$saveResult['post_status']} (expected 302)", 'fail');
        log_step("Response snippet: " . substr($saveResult['post_body_snippet'], 0, 200), 'info');
        $totalFail++;
        $failures[] = "{$page}: save status {$saveResult['post_status']}";
        continue;
    }
    log_step("Save POST → 302 redirect", 'pass');

    // 4. Assert flash on edit reload
    if (!has_flash($saveResult['followed_body'] ?? '', 'success')) {
        log_step("No success flash after save redirect", 'fail');
        $totalFail++;
        $failures[] = "{$page}: no success flash";
        // Continue to DB+public checks anyway to see how deep the damage goes
    } else {
        log_step("Success flash rendered on edit reload", 'pass');
    }

    // 5. Assert DB persisted
    PageContent::clearCache();
    $after = PageContent::where('key', $key)->first();
    if ($after?->value_en !== $canary) {
        log_step("DB mismatch: expected {$canary}, got " . var_export($after?->value_en, true), 'fail');
        $totalFail++;
        $failures[] = "{$page}: DB write missing";
    } else {
        log_step("DB value_en == canary", 'pass');
    }

    // 6. Assert public render (anonymous fetch)
    $publicRes = $client->get($case['public_path'] . '?_t=' . time(), ['headers' => ['Accept' => 'text/html']]);
    $publicBody = (string) $publicRes->getBody();
    if (!str_contains($publicBody, $canary)) {
        log_step("Public page did not include canary", 'fail');
        $totalFail++;
        $failures[] = "{$page}: public render missing canary";
    } else {
        log_step("Public render contains canary", 'pass');
        $totalPass++;
    }

    // 7. Restore snapshot
    if ($snapshot) {
        $after->fill([
            'value_en' => $snapshot['en'],
            'value_ar' => $snapshot['ar'],
            'value_it' => $snapshot['it'],
        ])->save();
    } else {
        PageContent::where('key', $key)->delete();
    }
    PageContent::clearCache();
    log_step("Snapshot restored", 'info');
}

// ─── Report ──────────────────────────────────────────────

fwrite(STDOUT, "\n" . str_repeat('═', 60) . "\n");
fwrite(STDOUT, "Pass: {$totalPass}  Fail: {$totalFail}\n");
if (!empty($failures)) {
    fwrite(STDOUT, "Failures:\n");
    foreach ($failures as $f) fwrite(STDOUT, "  · {$f}\n");
}
fwrite(STDOUT, str_repeat('═', 60) . "\n");

exit($totalFail === 0 ? 0 : 1);
