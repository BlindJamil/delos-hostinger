<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
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

        // Load every DB row for this page keyed by lang-key for O(1) lookup
        // in the blade template.
        $rows = PageContent::where('page', $page)
            ->get()
            ->keyBy('key');

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
     *
     * Each key that arrives in the payload is either updated (existing row)
     * or created (new row). Keys absent from the payload are untouched so
     * a partial save doesn't wipe other sections.
     */
    public function update(Request $request, string $page): RedirectResponse
    {
        $pageConfig = $this->pageOrAbort($page);
        $values = $request->input('values', []);

        // Build a map of key → type from the registry so we can store type
        // metadata correctly and validate rich-text HTML size etc.
        $typeByKey = [];
        foreach ($pageConfig['sections'] ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                $typeByKey[$field['key']] = $field['type'] ?? 'text';
            }
        }

        foreach ($values as $key => $localeValues) {
            if (!isset($typeByKey[$key])) {
                // Ignore unknown keys — someone probably tampered with the form.
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
                ]
            );
        }

        return redirect()
            ->route('admin.page-content.edit', $page)
            ->with('success', 'Content saved.');
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
                foreach (['en', 'ar', 'it'] as $locale) {
                    $value = Lang::get($key, [], $locale);
                    $defaults[$key][$locale] = (is_array($value) || $value === $key) ? null : (string) $value;
                }
            }
        }
        return $defaults;
    }
}
