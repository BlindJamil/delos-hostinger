<?php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Lang;

/**
 * Populates `page_contents` from the registry in config/editable_pages.php.
 *
 * For each declared field:
 *   - reads the current EN/AR/IT lang-file values
 *   - creates the row if it doesn't exist
 *   - backfills empty locale values if the row exists but has blanks
 *   - keeps page/section/type metadata in sync with the registry
 *   - NEVER overwrites admin-edited values
 *
 * Safe to run repeatedly — on each deploy, empty fields get filled from
 * lang files, admin edits are preserved, and metadata stays current.
 */
class PageContentFromLangSeeder extends Seeder
{
    public function run(): void
    {
        $registry = config('editable_pages', []);
        $locales = ['en', 'ar', 'it'];
        $created = 0;
        $backfilled = 0;
        $current = 0;

        foreach ($registry as $pageKey => $page) {
            foreach ($page['sections'] ?? [] as $sectionKey => $section) {
                foreach ($section['fields'] ?? [] as $sortIndex => $field) {
                    $langKey = $field['key'];

                    $row = PageContent::firstOrNew(['key' => $langKey]);
                    $isNew = !$row->exists;

                    // Always sync metadata so registry changes propagate
                    // (e.g. a field moved from one page/section to another).
                    $row->page = $pageKey;
                    $row->section = $sectionKey;
                    $row->sort_order = $sortIndex;
                    $row->type = $field['type'] ?? 'text';

                    // Backfill each locale: only write when the DB value
                    // is currently empty. This preserves admin edits while
                    // filling in blanks that slipped through due to stale
                    // config cache or missing lang resolution on first deploy.
                    $didBackfill = false;
                    foreach ($locales as $locale) {
                        $col = "value_{$locale}";
                        if ($row->$col === null || $row->$col === '') {
                            $langValue = $this->readLangValue($langKey, $locale);
                            if ($langValue !== null) {
                                $row->$col = $langValue;
                                $didBackfill = true;
                            }
                        }
                    }

                    $row->save();

                    if ($isNew) {
                        $created++;
                    } elseif ($didBackfill) {
                        $backfilled++;
                    } else {
                        $current++;
                    }
                }
            }
        }

        $this->command?->info("PageContent seeded: {$created} created, {$backfilled} backfilled, {$current} already current.");
    }

    /**
     * Read a nested lang value via dot-notation key + locale override.
     */
    private function readLangValue(string $key, string $locale): ?string
    {
        $value = Lang::get($key, [], $locale);

        if ($value === $key) {
            return null;
        }

        if (is_array($value)) {
            return null;
        }

        return (string) $value;
    }
}
