<?php

/**
 * Admin Input Verification Sweep.
 *
 * Exhaustively tests that every admin-editable field, when changed in the
 * DB, actually renders on the public site. Strategy per field:
 *
 *   1. Snapshot the current value
 *   2. Set it to a unique canary string
 *   3. Bust the relevant cache
 *   4. Fetch the public URL where it should appear (in all 3 locales)
 *   5. Assert the canary is present in the returned HTML
 *   6. Restore the original value (try/finally so a crash doesn't leave
 *      mutated rows in the DB)
 *
 * Run via tinker:
 *   php artisan tinker --execute="\$mode='all'; require 'scripts/verify-admin-inputs.php';"
 *
 * Modes: 'text' | 'images' | 'models' | 'settings' | 'siblings' | 'all'
 *
 * Requires `php artisan serve` running on :8000.
 */

use App\Models\Brand;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\PageContent;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;

// ─── Safety guard ─────────────────────────────────────────────
if (config('app.env') === 'production') {
    echo "✗ REFUSING to run on production (app.env=production)." . PHP_EOL;
    return;
}

$mode    = $mode    ?? 'all';
$locales = ['en', 'ar', 'it'];
$baseUrl = 'http://localhost:8000';

// Keys that legitimately render only under specific conditions the harness
// can't easily stage (e.g. "fallback when NO active branches exist" or
// "only when this branch has a whatsapp number set"). Skipped with a note.
$conditionalKeys = [
    // @empty fallback in footer — only when the branches table is empty
    'common.footer.showroom_erbil_soran.title',
    'common.footer.showroom_erbil_soran.address',
    'common.footer.showroom_erbil_soran.phone',
    'common.footer.showroom_erbil_gulan.title',
    'common.footer.showroom_erbil_gulan.address',
    'common.footer.showroom_erbil_gulan.phone',
    'common.footer.other_cities',
    // CTA label rendered only when the branch row has a whatsapp number
    'branches.whatsapp_cta',
];

$pass     = 0;
$fail     = 0;
$failures = [];

$startAt = microtime(true);

// ─── Helpers ──────────────────────────────────────────────────
function canary(string $label): string {
    $slug = preg_replace('/[^a-z0-9.]/i', '-', $label);
    return 'CANARY-' . $slug . '-' . bin2hex(random_bytes(2));
}

function fetchHtml(string $url): ?string {
    // One retry on transient failure.
    for ($attempt = 0; $attempt < 2; $attempt++) {
        $ctx = stream_context_create(['http' => ['ignore_errors' => true, 'timeout' => 10]]);
        $html = @file_get_contents($url, false, $ctx);
        if ($html !== false && $html !== '') return $html;
        usleep(200000);
    }
    return null;
}

function checkCanary(string $canary, string $url, string $label, array &$failures, int &$pass, int &$fail): bool {
    $html = fetchHtml($url);
    if ($html === null) {
        $fail++;
        $failures[] = ['label' => $label, 'reason' => 'fetch failed', 'url' => $url];
        return false;
    }
    if (str_contains($html, $canary)) { $pass++; return true; }
    $fail++;
    $failures[] = ['label' => $label, 'reason' => 'canary absent', 'url' => $url, 'canary' => $canary];
    return false;
}

// Resolve a public URL the field is rendered on. Centralised so section
// logic doesn't hardcode paths.
function urlFor(string $pageSlug, string $locale, string $baseUrl): string {
    $map = [
        'home'     => "/{$locale}",
        'about'    => "/{$locale}/about",
        'services' => "/{$locale}/services",
        'projects' => "/{$locale}/projects",
        'brands'   => "/{$locale}/brands",
        'branches' => "/{$locale}/branches",
        'contact'  => "/{$locale}/contact",
        'layout'   => "/{$locale}",  // nav/footer/marquee render on every page; home is a fine anchor
        'seo'      => "/{$locale}",  // seo tags live in <head>
    ];
    return $baseUrl . ($map[$pageSlug] ?? "/{$locale}/{$pageSlug}");
}

// For "common.*" keys registered under the layout page — some CTAs like
// `common.ctas.visit_official_website` only render on a specific page
// (brands, about, etc.), not on home. When a canary isn't found on the
// primary URL, the harness searches the rest of the public surface.
function checkCanaryAcrossPages(string $canary, string $locale, string $baseUrl, string $label, array &$failures, int &$pass, int &$fail): bool {
    $urls = [
        "/{$locale}",
        "/{$locale}/about",
        "/{$locale}/services",
        "/{$locale}/projects",
        "/{$locale}/brands",
        "/{$locale}/branches",
        "/{$locale}/contact",
    ];
    // Add a dynamic employee-show URL so CTAs rendered only on employee
    // profile pages (back_to_team / work_with_team / about_company) are found.
    $empId = App\Models\Employee::query()->active()->value('id');
    if ($empId) $urls[] = "/{$locale}/team/{$empId}";

    foreach ($urls as $path) {
        $html = fetchHtml($baseUrl . $path);
        if ($html !== null && str_contains($html, $canary)) { $pass++; return true; }
    }
    $fail++;
    $failures[] = ['label' => $label, 'reason' => 'canary absent on all public pages', 'url' => 'layout-scan', 'canary' => $canary];
    return false;
}

// ─── Section 1: page-content text fields ─────────────────────
if ($mode === 'text' || $mode === 'all') {
    echo "── Section 1: page-content text fields ──" . PHP_EOL;
    $sec1Pass = $sec1Fail = 0;
    foreach (config('editable_pages', []) as $pageSlug => $pageConfig) {
        if ($pageSlug === 'seo') continue; // SEO tested separately via Section 1b
        foreach ($pageConfig['sections'] ?? [] as $sectionSlug => $section) {
            foreach ($section['fields'] ?? [] as $field) {
                $type = $field['type'] ?? 'text';
                if (!in_array($type, ['text', 'textarea', 'url'], true)) continue;
                $key = $field['key'];
                if (in_array($key, $conditionalKeys, true)) continue;

                foreach ($locales as $locale) {
                    $row = PageContent::where('key', $key)->first();
                    $snapshot = $row
                        ? ['value_en' => $row->value_en, 'value_ar' => $row->value_ar, 'value_it' => $row->value_it]
                        : null;

                    try {
                        $c = canary($key . '-' . $locale);
                        PageContent::updateOrCreate(
                            ['key' => $key],
                            [
                                'page' => $pageSlug,
                                'section' => (string) $sectionSlug,
                                'type' => $type,
                                "value_{$locale}" => $c,
                            ]
                        );
                        PageContent::clearCache();

                        if ($pageSlug === 'layout') {
                            // Layout keys (nav, footer, ctas, marquee) may render on
                            // any page. Search the full surface until we find one.
                            $ok = checkCanaryAcrossPages($c, $locale, $baseUrl, "{$key} [{$locale}]", $failures, $pass, $fail);
                        } else {
                            $url = urlFor($pageSlug, $locale, $baseUrl);
                            $ok = checkCanary($c, $url, "{$key} [{$locale}]", $failures, $pass, $fail);
                        }
                        $ok ? $sec1Pass++ : $sec1Fail++;
                    } finally {
                        if ($snapshot) {
                            PageContent::where('key', $key)->update($snapshot);
                        } else {
                            PageContent::where('key', $key)->delete();
                        }
                        PageContent::clearCache();
                    }
                }
            }
        }
    }
    echo "  Section 1: {$sec1Pass} passed, {$sec1Fail} failed." . PHP_EOL . PHP_EOL;
}

// ─── Section 2: page-content image fields (URL resolution) ───
if ($mode === 'images' || $mode === 'all') {
    echo "── Section 2: page-content image fields ──" . PHP_EOL;
    $sec2Pass = $sec2Fail = 0;
    foreach (config('editable_pages', []) as $pageSlug => $pageConfig) {
        foreach ($pageConfig['sections'] ?? [] as $sectionSlug => $section) {
            foreach ($section['fields'] ?? [] as $field) {
                if (($field['type'] ?? null) !== 'image') continue;
                $key = $field['key'];
                $row = PageContent::where('key', $key)->first();
                $snapshot = $row
                    ? ['value_en' => $row->value_en, 'value_ar' => $row->value_ar, 'value_it' => $row->value_it]
                    : null;

                try {
                    // Canary path that admin-style upload would produce.
                    $canaryFile = 'uploads/canary-' . bin2hex(random_bytes(3)) . '.jpg';
                    PageContent::updateOrCreate(
                        ['key' => $key],
                        [
                            'page' => $pageSlug,
                            'section' => (string) $sectionSlug,
                            'type' => 'image',
                            'value_en' => $canaryFile,
                            'value_ar' => $canaryFile,
                            'value_it' => $canaryFile,
                        ]
                    );
                    PageContent::clearCache();

                    // We expect the rendered HTML to include "/storage/uploads/canary-..."
                    $expected = '/storage/' . $canaryFile;
                    $url = urlFor($pageSlug, 'en', $baseUrl);
                    $html = fetchHtml($url);
                    if ($html !== null && str_contains($html, $expected)) {
                        $pass++; $sec2Pass++;
                    } else {
                        $fail++; $sec2Fail++;
                        $failures[] = ['label' => "{$key} [image]", 'reason' => 'storage URL not rendered', 'url' => $url, 'canary' => $expected];
                    }
                } finally {
                    if ($snapshot) {
                        PageContent::where('key', $key)->update($snapshot);
                    } else {
                        PageContent::where('key', $key)->delete();
                    }
                    PageContent::clearCache();
                }
            }
        }
    }
    echo "  Section 2: {$sec2Pass} passed, {$sec2Fail} failed." . PHP_EOL . PHP_EOL;
}

// ─── Section 3: model-backed localized columns ───────────────
if ($mode === 'models' || $mode === 'all') {
    echo "── Section 3: model-backed localized text columns ──" . PHP_EOL;
    $sec3Pass = $sec3Fail = 0;

    // (model class, listing URL template {locale}, localized columns, optional detail URL template {locale,id})
    $modelSpecs = [
        ['class' => Brand::class,    'page' => 'brands',    'cols' => ['category', 'origin', 'description']],
        ['class' => Service::class,  'page' => 'services',  'cols' => ['name', 'description']],
        ['class' => Project::class,  'page' => 'projects',  'cols' => ['title', 'type_label']],
        ['class' => Employee::class, 'page' => 'home',      'cols' => ['name', 'role', 'achievement'],
         'detail' => fn ($id, $locale, $base) => "{$base}/{$locale}/team/{$id}"],
        ['class' => Branch::class,   'page' => 'branches',  'cols' => ['name', 'address']],
    ];

    foreach ($modelSpecs as $spec) {
        $cls = $spec['class'];
        $model = $cls::query()->first();
        if (!$model) { echo "  ! no {$cls} rows — skipped" . PHP_EOL; continue; }

        foreach ($spec['cols'] as $col) {
            foreach ($locales as $locale) {
                $colLocalized = "{$col}_{$locale}";
                if (!array_key_exists($colLocalized, $model->getAttributes())) continue;

                $original = $model->{$colLocalized};
                try {
                    $c = canary("{$cls}-{$col}-{$locale}");
                    $model->{$colLocalized} = $c;
                    $model->save();

                    $listingUrl = urlFor($spec['page'], $locale, $baseUrl);
                    $ok = checkCanary($c, $listingUrl, class_basename($cls) . ".{$col} [{$locale}]", $failures, $pass, $fail);
                    $ok ? $sec3Pass++ : $sec3Fail++;

                    // Detail page check when available (employee, project).
                    if (isset($spec['detail'])) {
                        $detailUrl = ($spec['detail'])($model->id, $locale, $baseUrl);
                        $detailLabel = class_basename($cls) . ".{$col} [{$locale}] (detail)";
                        $ok2 = checkCanary($c, $detailUrl, $detailLabel, $failures, $pass, $fail);
                        $ok2 ? $sec3Pass++ : $sec3Fail++;
                    }
                } finally {
                    $model->{$colLocalized} = $original;
                    $model->save();
                }
            }
        }
    }
    echo "  Section 3: {$sec3Pass} passed, {$sec3Fail} failed." . PHP_EOL . PHP_EOL;
}

// ─── Section 4: site settings ────────────────────────────────
if ($mode === 'settings' || $mode === 'all') {
    echo "── Section 4: site settings ──" . PHP_EOL;
    $sec4Pass = $sec4Fail = 0;

    // Keys pulled from AppServiceProvider's view composer.
    //
    // Some settings are shared with every view for possible future wiring
    // (tagline, company address, secondary contact channels, youtube) but
    // aren't currently rendered anywhere. They're flagged `skip` so the
    // sweep stays honest — passing an unset key would be a false positive.
    $settings = [
        ['key' => 'site_tagline',            'localized' => true,  'skip' => 'no view binding'],
        ['key' => 'company_address_primary', 'localized' => true,  'skip' => 'no view binding'],
        ['key' => 'contact_phone',           'localized' => false],
        ['key' => 'contact_whatsapp',        'localized' => false, 'skip' => 'no view binding'],
        ['key' => 'contact_email',           'localized' => false, 'skip' => 'no view binding'],
        ['key' => 'social_instagram',        'localized' => false],
        ['key' => 'social_facebook',         'localized' => false],
        ['key' => 'social_youtube',          'localized' => false, 'skip' => 'no view binding'],
        ['key' => 'social_tiktok',           'localized' => false],
        ['key' => 'social_pinterest',        'localized' => false],
    ];

    $skipped = 0;
    foreach ($settings as $s) {
        if (!empty($s['skip'])) { $skipped++; continue; }
        $row = SiteSetting::where('key', $s['key'])->first();
        $snapshot = $row
            ? ['value_en' => $row->value_en, 'value_ar' => $row->value_ar, 'value_it' => $row->value_it]
            : null;

        $testLocales = $s['localized'] ? $locales : ['en'];
        foreach ($testLocales as $locale) {
            try {
                $c = 'https://canary.invalid/' . $s['key'] . '-' . $locale . '-' . bin2hex(random_bytes(2));
                // Setting canary value: for URL-type settings we need a valid URL shape so
                // href="{canary}" renders as-is. Text settings take the raw canary too.
                $attrs = ['value_en' => $row?->value_en, 'value_ar' => $row?->value_ar, 'value_it' => $row?->value_it];
                $attrs["value_{$locale}"] = $c;
                // For non-localized settings, only value_en is read, so canary goes there.
                if (!$s['localized']) $attrs['value_en'] = $c;
                SiteSetting::updateOrCreate(['key' => $s['key']], $attrs);
                SiteSetting::clearCache($s['key']);

                $url = $baseUrl . "/{$locale}";
                $ok = checkCanary($c, $url, "setting.{$s['key']} [{$locale}]", $failures, $pass, $fail);
                $ok ? $sec4Pass++ : $sec4Fail++;
            } finally {
                if ($snapshot) {
                    SiteSetting::where('key', $s['key'])->update($snapshot);
                } else {
                    SiteSetting::where('key', $s['key'])->delete();
                }
                SiteSetting::clearCache($s['key']);
            }
        }
    }
    echo "  Section 4: {$sec4Pass} passed, {$sec4Fail} failed, {$skipped} skipped (no view binding)." . PHP_EOL . PHP_EOL;
}

// ─── Section 5: sibling keys (mobile + focal) ────────────────
if ($mode === 'siblings' || $mode === 'all') {
    echo "── Section 5: hybrid-image sibling keys ──" . PHP_EOL;
    $sec5Pass = $sec5Fail = 0;

    foreach (config('editable_pages', []) as $pageSlug => $pageConfig) {
        foreach ($pageConfig['sections'] ?? [] as $sectionSlug => $section) {
            foreach ($section['fields'] ?? [] as $field) {
                if (($field['type'] ?? null) !== 'image') continue;
                $baseKey = $field['key'];

                // Video posters render as HTML5 <video poster="..."> attr —
                // no <picture> wrapper, no mobile variant, no focal point.
                // Skip them from sibling testing.
                if (str_ends_with($baseKey, '.poster')) continue;

                // ── 5a. Mobile variant ───────────────────────
                $mobileKey = $baseKey . '_mobile';
                $row = PageContent::where('key', $mobileKey)->first();
                $snapshot = $row ? ['value_en' => $row->value_en, 'value_ar' => $row->value_ar, 'value_it' => $row->value_it] : null;
                try {
                    $canaryFile = 'uploads/canary-mobile-' . bin2hex(random_bytes(3)) . '.jpg';
                    PageContent::updateOrCreate(
                        ['key' => $mobileKey],
                        ['page' => $pageSlug, 'section' => (string) $sectionSlug, 'type' => 'image',
                         'value_en' => $canaryFile, 'value_ar' => $canaryFile, 'value_it' => $canaryFile]
                    );
                    PageContent::clearCache();
                    $url = urlFor($pageSlug, 'en', $baseUrl);
                    $html = fetchHtml($url);
                    $expected = '/storage/' . $canaryFile;
                    $hasMedia = $html !== null && preg_match('/<source[^>]*media="\(max-width:\s*767px\)"[^>]*' . preg_quote(basename($canaryFile), '/') . '/', $html);
                    if ($html !== null && str_contains($html, $expected) && $hasMedia) {
                        $pass++; $sec5Pass++;
                    } else {
                        $fail++; $sec5Fail++;
                        $failures[] = ['label' => "{$baseKey} [mobile sibling]", 'reason' => 'mobile <source> not rendered', 'url' => $url, 'canary' => $expected];
                    }
                } finally {
                    if ($snapshot) PageContent::where('key', $mobileKey)->update($snapshot);
                    else PageContent::where('key', $mobileKey)->delete();
                    PageContent::clearCache();
                }

                // ── 5b. Focal point ──────────────────────────
                $focalKey = $baseKey . '_focal';
                $row = PageContent::where('key', $focalKey)->first();
                $snapshot = $row ? ['value_en' => $row->value_en, 'value_ar' => $row->value_ar, 'value_it' => $row->value_it] : null;
                try {
                    $focalValue = '27% 83%';  // non-centered, non-collision-prone
                    PageContent::updateOrCreate(
                        ['key' => $focalKey],
                        ['page' => $pageSlug, 'section' => (string) $sectionSlug, 'type' => 'text',
                         'value_en' => $focalValue, 'value_ar' => $focalValue, 'value_it' => $focalValue]
                    );
                    PageContent::clearCache();
                    $url = urlFor($pageSlug, 'en', $baseUrl);
                    $html = fetchHtml($url);
                    if ($html !== null && str_contains($html, 'object-position: ' . $focalValue)) {
                        $pass++; $sec5Pass++;
                    } else {
                        $fail++; $sec5Fail++;
                        $failures[] = ['label' => "{$baseKey} [focal sibling]", 'reason' => 'focal style not rendered', 'url' => $url, 'canary' => $focalValue];
                    }
                } finally {
                    if ($snapshot) PageContent::where('key', $focalKey)->update($snapshot);
                    else PageContent::where('key', $focalKey)->delete();
                    PageContent::clearCache();
                }
            }
        }
    }
    echo "  Section 5: {$sec5Pass} passed, {$sec5Fail} failed." . PHP_EOL . PHP_EOL;
}

// ─── Report ──────────────────────────────────────────────────
$duration = round(microtime(true) - $startAt, 1);
echo "═══════════════════════════════════════════════════════════" . PHP_EOL;
echo "Total: {$pass} passed, {$fail} failed  ({$duration}s)" . PHP_EOL;
if ($failures) {
    echo PHP_EOL . "Failures:" . PHP_EOL;
    // Cap at 30 to keep output readable; full list is in $failures.
    foreach (array_slice($failures, 0, 30) as $f) {
        echo "  ✗ {$f['label']} — {$f['reason']} @ {$f['url']}" . PHP_EOL;
    }
    if (count($failures) > 30) {
        echo "  … and " . (count($failures) - 30) . " more" . PHP_EOL;
    }
}
echo "═══════════════════════════════════════════════════════════" . PHP_EOL;
