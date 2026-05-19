<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

/**
 * Seeds the branches table from lang/{en,ar,it}/branches.php so nothing
 * is lost when switching the public /branches page to DB-driven content.
 * Adds real-world lat/lng for each showroom city — these drive both the
 * pin position on the Iraq SVG map AND the Get Directions URL.
 *
 * Coordinates source: OpenStreetMap city-centroid queries (accurate to
 * ~500m — good enough for a national-scale map pin; admins can refine
 * to the exact showroom location in the edit form).
 *
 * Idempotent via firstOrCreate on city_key.
 */
class BranchesFromLangSeeder extends Seeder
{
    public function run(): void
    {
        // Production guard: once an admin has populated this table — even
        // with a single row — never re-inject seed rows. firstOrCreate is
        // only safe against updates, not against deletes; an admin-deleted
        // record would resurrect on every deploy without this guard. The
        // lang file rows() are kept as the baseline for fresh installs.
        if (Branch::query()->exists()) {
            return;
        }

        foreach (static::rows() as $index => $row) {
            $row['sort_order'] = ($index + 1) * 10;
            $row['active'] = true;
            Branch::firstOrCreate(
                ['city_key' => $row['city_key']],
                $row
            );
        }
    }

    /**
     * Canonical branch rows — single source of truth for every seeder that
     * needs this data (including the one-shot ResetArabicFromLangSeeder
     * that reverts Arabic admin edits back to this formal MSA baseline).
     */
    public static function rows(): array
    {
        return [
            [
                'city_key' => 'erbil',
                'slug' => 'erbil',
                'name_en' => 'Erbil',
                'name_ar' => 'أربيل',
                'name_it' => 'Erbil',
                'address_en' => 'Gulan Street, Opposite the Chamber of Commerce',
                'address_ar' => 'شارع كولان، مقابل غرفة التجارة',
                'address_it' => 'Gulan Street, di fronte alla Camera di Commercio',
                'hours_en' => 'Sat – Thu: 10:00 – 20:00',
                'hours_ar' => 'السبت – الخميس: 10:00 – 20:00',
                'hours_it' => 'Sab – Gio: 10:00 – 20:00',
                'established_en' => 'Est. 2020',
                'established_ar' => 'تأسّس 2020',
                'established_it' => 'Dal 2020',
                'phone' => '0750 200 1003',
                // Erbil, Iraq — city centre (OpenStreetMap)
                'latitude' => 36.1911,
                'longitude' => 44.0094,
                'is_flagship' => true,
            ],
            [
                'city_key' => 'kirkuk',
                'slug' => 'kirkuk',
                'name_en' => 'Kirkuk',
                'name_ar' => 'كركوك',
                'name_it' => 'Kirkuk',
                'address_en' => 'Kirkuk Showroom — contact for address',
                'address_ar' => 'معرض كركوك — يُرجى التواصل للاستفسار عن العنوان',
                'address_it' => 'Showroom di Kirkuk — contattaci per l\'indirizzo',
                'hours_en' => 'Sat – Thu: 10:00 – 20:00',
                'hours_ar' => 'السبت – الخميس: 10:00 – 20:00',
                'hours_it' => 'Sab – Gio: 10:00 – 20:00',
                'established_en' => 'Est. 2021',
                'established_ar' => 'تأسّس 2021',
                'established_it' => 'Dal 2021',
                'phone' => null,
                'latitude' => 35.4681,
                'longitude' => 44.3922,
                'is_flagship' => false,
            ],
            [
                'city_key' => 'sulaymaniyah',
                'slug' => 'sulaymaniyah',
                'name_en' => 'Sulaymaniyah',
                'name_ar' => 'السليمانية',
                'name_it' => 'Sulaymaniyah',
                'address_en' => 'Sulaymaniyah Showroom — contact for address',
                'address_ar' => 'معرض السليمانية — يُرجى التواصل للاستفسار عن العنوان',
                'address_it' => 'Showroom di Sulaymaniyah — contattaci per l\'indirizzo',
                'hours_en' => 'Sat – Thu: 10:00 – 20:00',
                'hours_ar' => 'السبت – الخميس: 10:00 – 20:00',
                'hours_it' => 'Sab – Gio: 10:00 – 20:00',
                'established_en' => 'Est. 2022',
                'established_ar' => 'تأسّس 2022',
                'established_it' => 'Dal 2022',
                'phone' => null,
                'latitude' => 35.5617,
                'longitude' => 45.4309,
                'is_flagship' => false,
            ],
            [
                'city_key' => 'baghdad',
                'slug' => 'baghdad',
                'name_en' => 'Baghdad',
                'name_ar' => 'بغداد',
                'name_it' => 'Baghdad',
                'address_en' => 'Baghdad Showroom — contact for address',
                'address_ar' => 'معرض بغداد — يُرجى التواصل للاستفسار عن العنوان',
                'address_it' => 'Showroom di Baghdad — contattaci per l\'indirizzo',
                'hours_en' => 'Sat – Thu: 10:00 – 20:00',
                'hours_ar' => 'السبت – الخميس: 10:00 – 20:00',
                'hours_it' => 'Sab – Gio: 10:00 – 20:00',
                'established_en' => 'Est. 2024',
                'established_ar' => 'تأسّس 2024',
                'established_it' => 'Dal 2024',
                'phone' => null,
                'latitude' => 33.3152,
                'longitude' => 44.3661,
                'is_flagship' => false,
            ],
        ];
    }
}
