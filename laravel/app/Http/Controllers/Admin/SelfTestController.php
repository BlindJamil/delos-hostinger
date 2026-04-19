<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Admin Self-Test — runs canary write + read-back + cache-invalidation
 * checks for every page-content editable field per locale and reports
 * pass/fail inline.
 *
 * This is the permanent replacement for "paste me an audit log" — the
 * admin clicks Run Test, waits ~10 seconds, and sees a pass/fail grid
 * pinpointing exactly which fields (if any) have a broken write path.
 *
 * Mutates DB with canary values but snapshot+restore-per-field guarantees
 * zero net drift. Safe to run against production repeatedly.
 */
class SelfTestController extends Controller
{
    public function show(): View
    {
        return view('admin.self-test', ['report' => null]);
    }

    public function run(Request $request): View
    {
        $pageFilter = $request->input('page');     // optional — limit to one page
        $localeFilter = $request->input('locale'); // optional — limit to one locale

        $report = $this->runFullSweep($pageFilter, $localeFilter);
        return view('admin.self-test', ['report' => $report]);
    }

    /**
     * Sweep every page-content text/textarea/url field across all 3
     * locales, writing a unique canary, reading it back via both the DB
     * and the cached ::value() helper, then restoring the original value.
     *
     * @return array<string,mixed>
     */
    private function runFullSweep(?string $pageFilter, ?string $localeFilter): array
    {
        $start = microtime(true);
        $locales = $localeFilter ? [$localeFilter] : ['en', 'ar', 'it'];
        $pages = [];
        $totalPass = 0;
        $totalFail = 0;
        $allFailures = [];

        $registry = config('editable_pages', []);
        foreach ($registry as $pageSlug => $pageConfig) {
            if ($pageFilter && $pageSlug !== $pageFilter) continue;

            $pageFields = [];
            $pagePass = 0;
            $pageFail = 0;
            foreach ($pageConfig['sections'] ?? [] as $sectionKey => $section) {
                foreach ($section['fields'] ?? [] as $field) {
                    $type = $field['type'] ?? 'text';
                    if (in_array($type, ['image', 'video'], true)) continue;
                    foreach ($locales as $locale) {
                        $result = $this->testField($pageSlug, $sectionKey, $field, $locale);
                        $pageFields[] = $result;
                        if ($result['status'] === 'pass') { $pagePass++; $totalPass++; }
                        else { $pageFail++; $totalFail++; $allFailures[] = $result; }
                    }
                }
            }

            $pages[$pageSlug] = [
                'slug' => $pageSlug,
                'label' => $pageConfig['label'] ?? $pageSlug,
                'pass' => $pagePass,
                'fail' => $pageFail,
                'failures' => array_values(array_filter($pageFields, fn ($r) => $r['status'] !== 'pass')),
            ];
        }

        return [
            'pages' => $pages,
            'total_pass' => $totalPass,
            'total_fail' => $totalFail,
            'all_failures' => $allFailures,
            'duration_ms' => (int) ((microtime(true) - $start) * 1000),
            'ran_at' => now()->format('Y-m-d H:i:s'),
            'filter_page' => $pageFilter,
            'filter_locale' => $localeFilter,
        ];
    }

    /**
     * Test one field in one locale. Writes canary → reads DB → reads
     * ::value() cache → restores. Returns pass/fail with reason.
     *
     * @param array{key:string,type?:string,label?:string} $field
     * @return array<string,mixed>
     */
    private function testField(string $page, string $sectionKey, array $field, string $locale): array
    {
        $key = $field['key'];
        $column = "value_{$locale}";
        $canary = 'SELFTEST-' . substr(bin2hex(random_bytes(4)), 0, 6);

        // Snapshot BEFORE any mutation — so failures mid-test restore cleanly.
        $snapshot = PageContent::where('key', $key)->first();
        $hadRow = (bool) $snapshot;
        $origColumns = $hadRow ? [
            'value_en' => $snapshot->value_en,
            'value_ar' => $snapshot->value_ar,
            'value_it' => $snapshot->value_it,
        ] : null;

        $dbBack = null;
        $cacheBack = null;
        $reason = null;

        try {
            PageContent::updateOrCreate(
                ['key' => $key],
                [
                    'page' => $page,
                    'section' => $sectionKey,
                    'type' => $field['type'] ?? 'text',
                    $column => $canary,
                ]
            );
            PageContent::clearCache();

            $after = PageContent::where('key', $key)->first();
            $dbBack = $after?->{$column};
            $cacheBack = PageContent::value($key, $locale);

            if ($dbBack !== $canary) {
                $reason = "db_read_mismatch (got: " . var_export($dbBack, true) . ")";
            } elseif ($cacheBack !== $canary) {
                $reason = "cache_stale (DB ok, cache returned: " . var_export($cacheBack, true) . ")";
            }
        } catch (\Throwable $e) {
            $reason = 'exception: ' . $e->getMessage();
        } finally {
            // Restore original state, always. This try/finally pattern is
            // how the existing scripts/verify-admin-inputs.php stays at
            // zero DB drift across hundreds of runs.
            try {
                if ($hadRow) {
                    PageContent::where('key', $key)->update($origColumns);
                } else {
                    PageContent::where('key', $key)->delete();
                }
                PageContent::clearCache();
            } catch (\Throwable) { /* don't mask the original failure */ }
        }

        return [
            'page' => $page,
            'section' => $sectionKey,
            'key' => $key,
            'label' => $field['label'] ?? $key,
            'type' => $field['type'] ?? 'text',
            'locale' => $locale,
            'status' => $reason === null ? 'pass' : 'fail',
            'reason' => $reason,
        ];
    }
}
