<?php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;

/**
 * Populates `page_contents` from the registry in config/editable_pages.php.
 *
 * For each declared field:
 *   - reads the current EN/AR/IT lang-file values
 *   - stores them as the initial DB values
 *
 * Idempotent via firstOrCreate on `key` — re-running never overwrites
 * admin edits. Safe to run repeatedly during development.
 *
 * After this runs once, admins visiting /admin/page-content see every
 * page pre-populated with its current copy, ready to edit.
 */
class PageContentFromLangSeeder extends Seeder
{
    public function run(): void
    {
        $registry = config('editable_pages', []);
        $locales = ['en', 'ar', 'it'];
        $created = 0;
        $skipped = 0;

        foreach ($registry as $pageKey => $page) {
            foreach ($page['sections'] ?? [] as $sectionKey => $section) {
                foreach ($section['fields'] ?? [] as $sortIndex => $field) {
                    $langKey = $field['key'];

                    // Read each locale's current lang-file value for this key.
                    $values = [];
                    foreach ($locales as $locale) {
                        $values["value_{$locale}"] = $this->readLangValue($langKey, $locale);
                    }

                    $existed = PageContent::where('key', $langKey)->exists();

                    PageContent::firstOrCreate(
                        ['key' => $langKey],
                        array_merge([
                            'page' => $pageKey,
                            'section' => $sectionKey,
                            'sort_order' => $sortIndex,
                            'type' => $field['type'] ?? 'text',
                        ], $values)
                    );

                    $existed ? $skipped++ : $created++;
                }
            }
        }

        // Cache was invalidated by each save() — good. Log the summary.
        $this->command?->info("PageContent seeded: {$created} created, {$skipped} already existed.");
    }

    /**
     * Read a nested lang value via dot-notation key + locale override.
     * Uses Laravel's translator so we get the same result as __() would,
     * including the lang-loader's group/file resolution.
     */
    private function readLangValue(string $key, string $locale): ?string
    {
        $value = Lang::get($key, [], $locale);

        // Lang::get returns the key itself when missing. Treat that as null
        // so we don't store the literal key as the "current value".
        if ($value === $key) {
            return null;
        }

        // For nested-array keys (shouldn't happen with our registry but be
        // defensive), return null rather than an array.
        if (is_array($value)) {
            return null;
        }

        return (string) $value;
    }
}
