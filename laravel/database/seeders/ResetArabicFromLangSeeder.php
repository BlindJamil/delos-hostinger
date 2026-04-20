<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\Employee;
use App\Models\PageContent;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

/**
 * One-shot corrective seeder.
 *
 * Background: a retired "Apply Iraqi Copy" admin feature overwrote Arabic
 * columns across every content table with colloquial Iraqi dialect copy.
 * The feature itself has been removed, but the dialect-flavoured strings
 * are persisted in the live database as admin edits, so the normal
 * lang→DB seeders (which are firstOrCreate-based) won't undo them.
 *
 * This seeder FORCE-UPDATES the *_ar columns across every content table
 * back to the formal MSA baseline that lives in each sibling seeder's
 * rows() method and in lang/ar/*.php. English and Italian values are
 * not touched — admin edits in those locales are preserved.
 *
 * The ‎"وكيل/وكلاء" (agent/representative) terminology is kept throughout;
 * nothing is switched to "شريك" (partner).
 *
 * Run manually only:
 *   php artisan db:seed --class=ResetArabicFromLangSeeder
 */
class ResetArabicFromLangSeeder extends Seeder
{
    public function run(): void
    {
        $this->resetBranches();
        $this->resetBrands();
        $this->resetServices();
        $this->resetProjects();
        $this->resetEmployees();
        $this->resetSiteSettings();
        $this->resetPageContents();
    }

    private function resetBranches(): void
    {
        $updated = 0;
        foreach (BranchesFromLangSeeder::rows() as $row) {
            $affected = Branch::where('city_key', $row['city_key'])->update([
                'name_ar' => $row['name_ar'],
                'address_ar' => $row['address_ar'],
                'hours_ar' => $row['hours_ar'],
                'established_ar' => $row['established_ar'],
            ]);
            if ($affected > 0) {
                $updated++;
            }
        }
        $this->command?->info("branches: reset Arabic on {$updated} rows");
    }

    private function resetBrands(): void
    {
        $updated = 0;
        foreach (BrandsFromLangSeeder::rows() as $row) {
            $affected = Brand::where('slug', $row['slug'])->update([
                'category_ar' => $row['category_ar'],
                'origin_ar' => $row['origin_ar'],
                'description_ar' => $row['description_ar'],
                // JSON column — cast by the model, but mass update() bypasses
                // casts so encode explicitly.
                'specialties_ar' => json_encode(
                    $row['specialties_ar'],
                    JSON_UNESCAPED_UNICODE
                ),
            ]);
            if ($affected > 0) {
                $updated++;
            }
        }
        $this->command?->info("brands: reset Arabic on {$updated} rows");
    }

    private function resetServices(): void
    {
        $updated = 0;
        foreach (ServicesFromLangSeeder::rows() as $row) {
            $affected = Service::where('slug', $row['slug'])->update([
                'name_ar' => $row['name_ar'],
                'description_ar' => $row['description_ar'],
                'features_ar' => json_encode(
                    $row['features_ar'],
                    JSON_UNESCAPED_UNICODE
                ),
            ]);
            if ($affected > 0) {
                $updated++;
            }
        }
        $this->command?->info("services: reset Arabic on {$updated} rows");
    }

    private function resetProjects(): void
    {
        $updated = 0;
        foreach (ProjectsFromLangSeeder::rows() as $row) {
            $affected = Project::where('title_en', $row['title_en'])->update([
                'title_ar' => $row['title_ar'],
                'type_label_ar' => $row['type_label_ar'],
            ]);
            if ($affected > 0) {
                $updated++;
            }
        }
        $this->command?->info("projects: reset Arabic on {$updated} rows");
    }

    private function resetEmployees(): void
    {
        $updated = 0;
        foreach (EmployeesFromLangSeeder::rows() as $row) {
            $affected = Employee::where('name_en', $row['name_en'])->update([
                'name_ar' => $row['name_ar'],
                'role_ar' => $row['role_ar'],
                'achievement_ar' => $row['achievement_ar'],
            ]);
            if ($affected > 0) {
                $updated++;
            }
        }
        $this->command?->info("employees: reset Arabic on {$updated} rows");
    }

    private function resetSiteSettings(): void
    {
        $updated = 0;
        foreach (SiteSettingsSeeder::rows() as $row) {
            // Only localized (text/textarea) settings carry value_ar; url/email/
            // phone types store a single value in value_en. Skip anything that
            // didn't ship with an Arabic seed value.
            if (!array_key_exists('value_ar', $row)) {
                continue;
            }
            $affected = SiteSetting::where('key', $row['key'])->update([
                'value_ar' => $row['value_ar'],
            ]);
            if ($affected > 0) {
                $updated++;
            }
        }
        $this->command?->info("site_settings: reset Arabic on {$updated} rows");
    }

    /**
     * PageContent rows are keyed by the dot-path used in lang/ar/*.php
     * (e.g. "home.about.heading_1"). Walk the editable_pages registry,
     * pull the formal Arabic value directly from the lang file, and
     * force-update value_ar only. Image/video fields are skipped — they
     * don't carry translatable text.
     */
    private function resetPageContents(): void
    {
        $registry = config('editable_pages', []);
        $langCache = [];
        $updated = 0;

        foreach ($registry as $page) {
            foreach ($page['sections'] ?? [] as $section) {
                foreach ($section['fields'] ?? [] as $field) {
                    $type = $field['type'] ?? 'text';
                    if (!in_array($type, ['text', 'textarea'], true)) {
                        continue;
                    }

                    $langKey = $field['key'];
                    $value = $this->readArLangValue($langKey, $langCache);
                    if ($value === null) {
                        continue;
                    }

                    $affected = PageContent::where('key', $langKey)->update([
                        'value_ar' => $value,
                    ]);
                    if ($affected > 0) {
                        $updated++;
                    }
                }
            }
        }

        $this->command?->info("page_contents: reset Arabic on {$updated} rows");
    }

    /**
     * Mirror PageContentFromLangSeeder::readLangValue(), but hard-coded to
     * the 'ar' locale since that's all we want to reset.
     */
    private function readArLangValue(string $key, array &$cache): ?string
    {
        $dotPos = strpos($key, '.');
        if ($dotPos === false) {
            return null;
        }
        $group = substr($key, 0, $dotPos);
        $path = substr($key, $dotPos + 1);

        if (!isset($cache[$group])) {
            $file = lang_path("ar/{$group}.php");
            $cache[$group] = file_exists($file) ? include $file : [];
        }

        $value = data_get($cache[$group], $path);

        if ($value === null || is_array($value)) {
            return null;
        }

        return (string) $value;
    }
}
