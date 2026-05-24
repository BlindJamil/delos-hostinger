<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Page Content editor — admin CRUD for the `page_contents` table.
 *
 * Pages are declared in config/editable_pages.php. Each page has sections;
 * each section has fields with (key, label, type). This controller renders
 * the editor and saves changes in bulk per-page. Media uploads for image
 * and video field types are handled separately via PageContentMediaController
 * (async per-field) so the main save POST stays under PHP's max_input_vars.
 */
class PageContentController extends Controller
{
    /**
     * Landing page — list all editable pages with override counts.
     */
    public function index(): View
    {
        $registry = config('editable_pages', []);
        // SQLite + MySQL agree on single-quoted string literals; double quotes
        // are treated as identifiers on SQLite which is why we avoid them.
        $stats = PageContent::query()
            ->selectRaw("page, COUNT(*) as total, SUM(CASE WHEN value_en IS NOT NULL AND value_en != '' THEN 1 ELSE 0 END) as filled")
            ->groupBy('page')
            ->get()
            ->keyBy('page');

        $pages = collect($registry)->map(function ($page, $slug) use ($stats) {
            $fieldCount = collect($page['sections'] ?? [])->sum(fn ($s) => count($s['fields'] ?? []));
            return [
                'slug' => $slug,
                'label' => $page['label'] ?? $slug,
                'description' => $page['description'] ?? null,
                'field_count' => $fieldCount,
                'filled' => (int) ($stats[$slug]?->filled ?? 0),
            ];
        })->values();

        return view('admin.page-content.index', compact('pages'));
    }

    /**
     * Render the editor for one page — all sections + fields + current
     * DB values + lang-file defaults for the "Reset to default" affordance.
     */
    public function edit(string $page): View
    {
        $pageConfig = $this->pageOrAbort($page);

        // Load ALL DB rows keyed by lang-key for O(1) lookup in the blade
        // template. The table is small (~340 rows) so no page filter is
        // needed — the registry controls which fields appear per page.
        // This avoids empty-field bugs when a row's `page` column is
        // out of sync with the current registry structure.
        $rows = PageContent::all()->keyBy('key');

        return view('admin.page-content.edit', [
            'pageSlug' => $page,
            'pageConfig' => $pageConfig,
            'rows' => $rows,
            'langDefaults' => $this->langDefaultsForPage($pageConfig),
        ]);
    }

    /**
     * Bulk-save every field for a page. Input shape:
     *
     *   values[<key>][en] = "..."
     *   values[<key>][ar] = "..."
     *   values[<key>][it] = "..."
     *   values[<key>][ku] = "..."
     *
     * Each key that arrives in the payload is either updated (existing row)
     * or created (new row). Keys absent from the payload are untouched so
     * a partial save doesn't wipe other sections.
     */
    public function update(Request $request, string $page): RedirectResponse
    {
        $pageConfig = $this->pageOrAbort($page);
        $values = $request->input('values', []);

        // Compute the registry's text-submittable field count so partial
        // truncation (silent max_input_vars drop) is detectable. image/video
        // fields don't participate in this form POST — they're AJAX-only.
        $textFieldKeys = [];
        foreach ($pageConfig['sections'] ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                $type = $field['type'] ?? 'text';
                if (!in_array($type, ['image', 'video'], true)) {
                    $textFieldKeys[] = $field['key'];
                }
            }
        }
        $expectedFieldCount = count($textFieldKeys);

        // Remote-diagnosable audit trail: every save attempt appends one
        // JSONL line to storage/app/admin-save-audit.jsonl. Exposed read-only
        // via /verify-health-ping-9k2x/save-audit.json so we can see from
        // outside what the server actually received. This is the only way
        // to diagnose "save silently fails" on shared hosting without SSH.
        // Server fingerprint: unambiguously distinguishes a local HTTP-kernel
        // simulation (SAPI=cli-server, LOCALHOST) from a real production LiteSpeed
        // request (SAPI=litespeed, public IP). Solves the "is this audit entry
        // from local or prod?" problem the prior rounds couldn't answer.
        $indexPhp = public_path('index.php');
        $auditBase = [
            'ts' => now()->format('c'),
            // Reuse the upstream middleware's request_id if present so a save
            // attempt can be traced across middleware_log ↔ controller_audits.
            'request_id' => $request->attributes->get('request_id') ?? (string) \Illuminate\Support\Str::uuid(),
            'page' => $page,
            'method' => $request->method(),
            'uri' => $request->getRequestUri(),
            'content_length' => (int) ($request->header('Content-Length') ?? 0),
            'raw_body_length' => strlen($request->getContent() ?? ''),
            'all_post_keys' => array_keys($request->all()),
            'values_is_array' => is_array($values),
            'values_count' => is_array($values) ? count($values) : 0,
            // Full keys list so a remote diff against the registry can pinpoint
            // exactly which field got dropped if truncation occurred.
            'values_keys' => is_array($values) ? array_keys($values) : [],
            'expected_field_count' => $expectedFieldCount,
            'has_csrf' => $request->has('_token'),
            'has_method_spoof' => $request->input('_method'),
            'max_input_vars' => (int) ini_get('max_input_vars'),
            'post_max_size' => ini_get('post_max_size'),
            'admin_user_id' => auth('admin')->id(),
            'server' => [
                'sapi' => php_sapi_name(),
                'software' => $_SERVER['SERVER_SOFTWARE'] ?? null,
                'name' => $_SERVER['SERVER_NAME'] ?? null,
                'remote_addr' => $request->ip(),
                'forwarded_for' => $request->header('X-Forwarded-For'),
            ],
            'deploy_marker' => is_file($indexPhp) ? filemtime($indexPhp) : null,
        ];

        // Watchlist: for a small set of high-interest keys, capture the exact
        // submitted value per locale (truncated to keep the public audit log
        // compact) AND the DB pre-save value. This is the diagnostic that
        // was missing in prior rounds — it tells us whether the user's input
        // actually reached the server as they typed it, vs. being replaced
        // by a form pre-population fallback or stripped by a WAF.
        $watchlist = ['home.about.body', 'home.about.overline', 'home.about.quote', 'home.hero.overline'];
        $watchlistData = [];
        foreach ($watchlist as $wk) {
            if (!is_array($values) || !isset($values[$wk])) {
                $watchlistData[$wk] = ['status' => 'not_in_payload'];
                continue;
            }
            $row = PageContent::where('key', $wk)->first();
            $trunc = fn ($v) => $v === null ? null : mb_substr((string) $v, 0, 200);
            $submitted = is_array($values[$wk]) ? $values[$wk] : [];
            $watchlistData[$wk] = [
                'status' => 'in_payload',
                'submitted_en' => $trunc($submitted['en'] ?? null),
                'submitted_ar' => $trunc($submitted['ar'] ?? null),
                'submitted_it' => $trunc($submitted['it'] ?? null),
                'submitted_ku' => $trunc($submitted['ku'] ?? null),
                'db_before_en' => $trunc($row?->value_en),
                'db_before_ar' => $trunc($row?->value_ar),
                'db_before_it' => $trunc($row?->value_it),
                'db_before_ku' => $trunc($row?->value_ku),
                'changed_en' => ($submitted['en'] ?? null) !== ($row?->value_en ?? null),
                'changed_ar' => ($submitted['ar'] ?? null) !== ($row?->value_ar ?? null),
                'changed_it' => ($submitted['it'] ?? null) !== ($row?->value_it ?? null),
                'changed_ku' => ($submitted['ku'] ?? null) !== ($row?->value_ku ?? null),
            ];
        }
        $auditBase['watchlist'] = $watchlistData;

        // Defensive: if PHP truncated the payload (max_input_vars) or the
        // client sent nothing, fail loudly instead of silently redirecting
        // back with no changes — that silent failure was the "save doesn't
        // work" symptom the user was hitting.
        if (!is_array($values) || empty($values)) {
            Log::warning('PageContent update received empty values payload', [
                'page' => $page,
                'post_keys' => array_keys($request->all()),
                'max_input_vars' => ini_get('max_input_vars'),
                'post_max_size' => ini_get('post_max_size'),
            ]);
            $this->appendSaveAudit($auditBase + [
                'result' => 'empty',
                'saved_count' => 0,
                'skipped_count' => 0,
            ]);
            return redirect()
                ->route('admin.page-content.edit', ['page' => $page, '_t' => time()])
                ->with('error', 'Nothing was saved — the form submission arrived empty. This often means PHP dropped the payload (max_input_vars limit). Check the server logs.');
        }

        // Partial-truncation guard: if the browser sent the full form but
        // PHP silently truncated the nested values[] array at some per-host
        // max_input_vars ceiling, we'd overwrite the DB with only the
        // earlier fields — losing the user's edit on later fields without
        // any visible error. Reject rather than partially write.
        $receivedCount = count($values);
        if ($expectedFieldCount > 20 && $receivedCount < (int) ceil($expectedFieldCount * 0.9)) {
            $missingKeys = array_values(array_diff($textFieldKeys, array_keys($values)));
            Log::warning('PageContent update received truncated payload', [
                'page' => $page,
                'expected' => $expectedFieldCount,
                'received' => $receivedCount,
                'missing_sample' => array_slice($missingKeys, 0, 10),
                'max_input_vars' => ini_get('max_input_vars'),
                'content_length' => $request->header('Content-Length'),
            ]);
            $this->appendSaveAudit($auditBase + [
                'result' => 'truncated',
                'saved_count' => 0,
                'skipped_count' => 0,
                'missing_keys' => $missingKeys,
            ]);
            return redirect()
                ->route('admin.page-content.edit', ['page' => $page, '_t' => time()])
                ->with('error', sprintf(
                    'Save rejected — only %d of %d expected fields arrived at the server (PHP max_input_vars may have truncated the payload). Nothing was written so no data was lost.',
                    $receivedCount,
                    $expectedFieldCount
                ));
        }

        // Build a map of key → type from the registry so we can store type
        // metadata correctly and validate rich-text HTML size etc.
        $typeByKey = [];
        foreach ($pageConfig['sections'] ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                $typeByKey[$field['key']] = $field['type'] ?? 'text';
            }
        }

        $saved = 0;
        $skipped = [];

        try {
            DB::transaction(function () use ($values, $typeByKey, $pageConfig, $page, &$saved, &$skipped) {
                foreach ($values as $key => $localeValues) {
                    if (!isset($typeByKey[$key])) {
                        // Key isn't in the registry — surface it so admins
                        // can tell if a rename got out of sync.
                        $skipped[] = $key;
                        continue;
                    }
                    if (!is_array($localeValues)) {
                        // Malformed — ignore silently but count it.
                        $skipped[] = $key;
                        continue;
                    }

                    PageContent::updateOrCreate(
                        ['key' => $key],
                        [
                            'page' => $page,
                            'section' => $this->sectionForKey($pageConfig, $key),
                            'type' => $typeByKey[$key],
                            'value_en' => $localeValues['en'] ?? null,
                            'value_ar' => $localeValues['ar'] ?? null,
                            'value_it' => $localeValues['it'] ?? null,
                            'value_ku' => $localeValues['ku'] ?? null,
                        ]
                    );
                    $saved++;
                }
            });
        } catch (\Throwable $e) {
            Log::error('PageContent update transaction failed', [
                'page' => $page,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->appendSaveAudit($auditBase + [
                'result' => 'exception',
                'error_class' => get_class($e),
                'error_message' => $e->getMessage(),
                'saved_count' => $saved,
                'skipped_count' => count($skipped),
            ]);
            return redirect()
                ->route('admin.page-content.edit', ['page' => $page, '_t' => time()])
                ->with('error', 'Save failed: ' . $e->getMessage());
        }

        // Belt-and-suspenders: some cache drivers can miss the model-event
        // bust if a write happens inside a transaction on certain DBs. Bust
        // once more after commit so the public site is guaranteed fresh.
        PageContent::clearCache();

        // Stale-cache defense for shared hosting (Hostinger in particular):
        // any of Laravel's artisan-cached layers (config, routes, views) can
        // silently serve pre-change content if they were generated before
        // the latest deploy. Clearing them on every successful admin save
        // guarantees the next public page render reads from live source.
        // Cheap — these caches regenerate on first request.
        try {
            \Illuminate\Support\Facades\Cache::flush();
        } catch (\Throwable) { /* cache driver unavailable — skip */ }
        // Compiled-views cache is a directory of .php files; wipe it so
        // blade re-compiles with the fresh pcontent values.
        $viewsCache = base_path('storage/framework/views');
        if (is_dir($viewsCache)) {
            foreach (glob($viewsCache . '/*.php') ?: [] as $f) {
                @unlink($f);
            }
        }
        // OPcache holds compiled PHP bytecode, including the pcontent
        // resolver's generated closures. Reset so the next request reads
        // the current source.
        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }

        $message = "Content saved — {$saved} field" . ($saved === 1 ? '' : 's') . " written.";
        if (!empty($skipped)) {
            Log::info('PageContent update skipped unregistered keys', [
                'page' => $page,
                'skipped' => $skipped,
            ]);
            $message .= ' (' . count($skipped) . ' unknown key' . (count($skipped) === 1 ? '' : 's') . ' skipped — see server log.)';
        }

        // Capture the post-save DB state for watchlist keys so the
        // save-audit endpoint shows both "what was submitted" and "what
        // ended up in DB" side-by-side — pinpointing cache vs form vs DB
        // layer problems without further guessing.
        $watchlistAfter = [];
        $trunc = fn ($v) => $v === null ? null : mb_substr((string) $v, 0, 200);
        foreach (array_keys($watchlistData) as $wk) {
            $row = PageContent::where('key', $wk)->first();
            $watchlistAfter[$wk] = [
                'db_after_en' => $trunc($row?->value_en),
                'db_after_ar' => $trunc($row?->value_ar),
                'db_after_it' => $trunc($row?->value_it),
                'db_after_ku' => $trunc($row?->value_ku),
            ];
        }

        $this->appendSaveAudit($auditBase + [
            'result' => 'saved',
            'saved_count' => $saved,
            'skipped_count' => count($skipped),
            'skipped_sample' => array_slice($skipped, 0, 6),
            'flash_message' => $message,
            'watchlist_after' => $watchlistAfter,
        ]);

        // `_t` makes the redirect URL unique so LiteSpeed / browsers cannot
        // serve a stale pre-save HTML copy of this edit form after the POST.
        return redirect()
            ->route('admin.page-content.edit', ['page' => $page, '_t' => time()])
            ->with('success', $message);
    }

    /**
     * "Reset to default" — delete the DB override for one key, so the
     * public site falls back to the lang-file value.
     */
    public function resetField(string $page, Request $request): RedirectResponse
    {
        $key = $request->input('key');
        $row = PageContent::where('key', $key)->first();
        if ($row) {
            $row->delete(); // fires deleted() → busts cache
        }

        return back()->with('success', 'Field reset to default.');
    }

    // ─── Internals ─────────────────────────────────────────────

    /**
     * Audit log path. Stored under storage/app/ (writable on all hosts
     * including Hostinger shared). Bounded size — we keep at most 30 entries
     * so the file never balloons.
     */
    private const AUDIT_FILE = 'admin-save-audit.jsonl';
    private const AUDIT_MAX_ENTRIES = 30;

    /**
     * Append one JSON line describing a save attempt. Keeps the file bounded
     * by trimming oldest entries when over AUDIT_MAX_ENTRIES.
     * Best-effort — never throws, because a logging failure must not break
     * a successful save response.
     */
    private function appendSaveAudit(array $entry): void
    {
        try {
            $path = storage_path('app/' . self::AUDIT_FILE);
            if (!is_dir(dirname($path))) {
                @mkdir(dirname($path), 0775, true);
            }
            // Wipe stale pre-deploy entries: if the audit file is older than
            // the current deploy (public/index.php), any existing entries were
            // written before this code shipped and will confuse diagnosis.
            $deployMark = is_file(public_path('index.php')) ? filemtime(public_path('index.php')) : 0;
            $fileMtime = is_file($path) ? filemtime($path) : 0;
            $lines = (is_file($path) && $fileMtime >= $deployMark)
                ? (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [])
                : [];
            $lines[] = json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            if (count($lines) > self::AUDIT_MAX_ENTRIES) {
                $lines = array_slice($lines, -self::AUDIT_MAX_ENTRIES);
            }
            @file_put_contents($path, implode(PHP_EOL, $lines) . PHP_EOL, LOCK_EX);
        } catch (\Throwable) { /* swallow — never block a save over a log write */ }
    }

    /**
     * Read recent audit entries (latest first). Used by the public
     * healthcheck endpoint to expose server-side save state to a remote
     * operator without SSH access.
     */
    public static function recentAudits(int $limit = 10): array
    {
        $path = storage_path('app/' . self::AUDIT_FILE);
        if (!is_file($path)) return [];
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        $lines = array_slice($lines, -$limit);
        $out = [];
        foreach (array_reverse($lines) as $line) {
            $decoded = json_decode($line, true);
            if (is_array($decoded)) $out[] = $decoded;
        }
        return $out;
    }

    private function pageOrAbort(string $page): array
    {
        $config = config("editable_pages.{$page}");
        if (!$config) {
            abort(404, "Unknown page: {$page}");
        }
        return $config;
    }

    /**
     * Reverse lookup: which section does a field key belong to?
     * Used when saving to restore the section metadata on the row.
     */
    private function sectionForKey(array $pageConfig, string $key): ?string
    {
        foreach ($pageConfig['sections'] ?? [] as $sectionKey => $section) {
            foreach ($section['fields'] ?? [] as $field) {
                if ($field['key'] === $key) {
                    return $sectionKey;
                }
            }
        }
        return null;
    }

    /**
     * Pre-load the lang-file values for every field in a page across all
     * locales. Shown in the editor as "Reset to default" hints and as
     * placeholders when a locale value hasn't been edited yet.
     */
    private function langDefaultsForPage(array $pageConfig): array
    {
        $defaults = [];
        foreach ($pageConfig['sections'] ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                $key = $field['key'];
                foreach (['en', 'ar', 'it', 'ku'] as $locale) {
                    $defaults[$key][$locale] = $this->readLangValue($key, $locale);
                }
            }
        }
        return $defaults;
    }

    /**
     * Read a lang value using direct file inclusion + data_get() instead
     * of Lang::get(). Laravel's translator cannot resolve numeric array
     * indices in dot paths (e.g. 'home.stats.items.0.value' returns the
     * key itself). data_get() handles them correctly.
     */
    private function readLangValue(string $key, string $locale): ?string
    {
        $dotPos = strpos($key, '.');
        if ($dotPos === false) {
            return null;
        }
        $group = substr($key, 0, $dotPos);
        $path = substr($key, $dotPos + 1);

        static $fileCache = [];
        $cacheKey = "{$locale}/{$group}";
        if (!isset($fileCache[$cacheKey])) {
            $file = lang_path("{$locale}/{$group}.php");
            $fileCache[$cacheKey] = file_exists($file) ? include $file : [];
        }

        $value = data_get($fileCache[$cacheKey], $path);

        if ($value === null || is_array($value)) {
            return null;
        }

        return (string) $value;
    }
}
