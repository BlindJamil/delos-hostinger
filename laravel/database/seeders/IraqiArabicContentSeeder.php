<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\PageContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Iraqi-flavored Arabic copy refresh for Delos International.
 *
 * Applies the user's hand-written Iraqi-dialect-professional Arabic to
 * specific page_contents.value_ar entries + the LUBE brand row. All
 * strings here are EXACTLY what the site owner wrote — no Claude-generated
 * translation.
 *
 * Idempotent: safe to re-run. Each updateOrCreate() overwrites with the
 * same canonical value. If an admin has manually edited a key after the
 * last seed run, re-running will revert that edit — surface this to the
 * admin in the UI before they click Apply.
 *
 * Source of truth for both CLI runs (`php artisan db:seed --class=IraqiArabicContentSeeder`)
 * and the admin-panel one-click Apply button at /dashboard/apply-iraqi-copy.
 */
class IraqiArabicContentSeeder extends Seeder
{
    /**
     * Page-content key → page/section/type/value_ar. These all land in the
     * page_contents table.
     *
     * Section metadata is authoritative from config/editable_pages.php —
     * stored here so updateOrCreate can populate a fresh row correctly if
     * one doesn't exist yet.
     */
    public const PAGE_CONTENT_UPDATES = [
        // ─── Home page ─────────────────────────────────────────────
        'home.about.body' => [
            'page' => 'home', 'section' => 'about', 'type' => 'textarea',
            'value_ar' => 'شركة ديلوس انترناشيونال هي شركة متميزة مقرها بالعراق، مختصة بتقديم أرقى التصاميم الإيطالية الفاخرة للبيوت بكل المنطقة. ديلوس تمثل مجموعة حصرية من الماركات الإيطالية الراقية، وتوفر تجربة حياة متكاملة عنوانها الأناقة، الإتقان، والدقة',
        ],
        'home.about.quote' => [
            'page' => 'home', 'section' => 'about', 'type' => 'textarea',
            'value_ar' => 'اسم "ديلوس" مستوحى من جزيرة يونانية تعتبر رمز تاريخي للحضارة والحكمة. أما شعارنا (الأسد)، فهو يمثل القوة والحماية والثقة.',
        ],
        'home.collection.overline' => [
            'page' => 'home', 'section' => 'collection', 'type' => 'text',
            'value_ar' => 'أبرز المعروضات',
        ],
        'home.collection.heading' => [
            'page' => 'home', 'section' => 'collection', 'type' => 'text',
            'value_ar' => 'كولكشن جديد',
        ],
        'home.employees.overline' => [
            'page' => 'home', 'section' => 'employees', 'type' => 'text',
            'value_ar' => 'أفضل موظفي الشهر',
        ],
        'home.employees.sub' => [
            'page' => 'home', 'section' => 'employees', 'type' => 'textarea',
            'value_ar' => 'تقدير لجهود المبدعين اللي يتفانون بشغلهم ورا كل مشروع متميز لشركة ديلوس خلال السنة',
        ],
        'home.stats.overline' => [
            'page' => 'home', 'section' => 'stats', 'type' => 'text',
            'value_ar' => 'إنجازاتنا بالأرقام',
        ],
        'home.stats.heading' => [
            'page' => 'home', 'section' => 'stats', 'type' => 'text',
            'value_ar' => 'إرث ديلوس بالأرقام',
        ],
        'home.stats.items.0.label' => [
            'page' => 'home', 'section' => 'stats', 'type' => 'text',
            'value_ar' => 'مشاريع منجزة',
        ],
        'home.stats.items.1.label' => [
            'page' => 'home', 'section' => 'stats', 'type' => 'text',
            'value_ar' => 'وكالات إيطالية حصرية',
        ],
        'home.stats.items.2.label' => [
            'page' => 'home', 'section' => 'stats', 'type' => 'text',
            'value_ar' => 'فروعنا بالعراق',
        ],
        'home.stats.items.3.label' => [
            'page' => 'home', 'section' => 'stats', 'type' => 'text',
            'value_ar' => 'سنين من الإبداع',
        ],
        'home.brands.overline' => [
            'page' => 'home', 'section' => 'brands_intro', 'type' => 'text',
            'value_ar' => 'شراكات حصرية',
        ],
        'home.brands.heading' => [
            'page' => 'home', 'section' => 'brands_intro', 'type' => 'text',
            'value_ar' => 'شركاؤنا من الماركات الإيطالية',
        ],
        'home.brands.sub' => [
            'page' => 'home', 'section' => 'brands_intro', 'type' => 'textarea',
            'value_ar' => 'ديلوس تتعاون مع كبرى الماركات الإيطالية الرائدة بمجالات المطابخ، الأثاث، الخزائن، والأرضيات. هدفنا هو نقل التصميم الإيطالي الأصلي، والخامات الفاخرة، والإتقان والحرفية العالية للسوق العراقي.',
        ],
        'home.cta.overline' => [
            'page' => 'home', 'section' => 'cta', 'type' => 'text',
            'value_ar' => 'استلم مفتاح فخامة بيتك',
        ],
        'home.cta.heading' => [
            'page' => 'home', 'section' => 'cta', 'type' => 'text',
            'value_ar' => 'ابدأ رحلة التميز مع التصاميم الإيطالية اليوم.',
        ],

        // ─── Brands page ───────────────────────────────────────────
        'brands.hero.overline' => [
            'page' => 'brands', 'section' => 'hero', 'type' => 'text',
            'value_ar' => 'وكلاؤنا الإيطاليين',
        ],
        'brands.hero.heading_1' => [
            'page' => 'brands', 'section' => 'hero', 'type' => 'text',
            'value_ar' => 'خمس دور تصميم..',
        ],
        'brands.hero.heading_2' => [
            'page' => 'brands', 'section' => 'hero', 'type' => 'text',
            'value_ar' => 'ومعيار واحد للتميز الإيطالي.',
        ],
        'brands.hero.sub' => [
            'page' => 'brands', 'section' => 'hero', 'type' => 'textarea',
            'value_ar' => 'ديلوس هي الوكيل الحصري لأرقى وأعرق الماركات الإيطالية المختصة بالفخامة',
        ],
        'brands.intro.overline' => [
            'page' => 'brands', 'section' => 'intro', 'type' => 'text',
            'value_ar' => 'وكالاتنا الحصرية',
        ],
        'brands.intro.heading_1' => [
            'page' => 'brands', 'section' => 'intro', 'type' => 'text',
            'value_ar' => 'ما نختار غير الأفضل من إيطاليا.',
        ],
        'brands.intro.body' => [
            'page' => 'brands', 'section' => 'intro', 'type' => 'textarea',
            'value_ar' => 'ديلوس إنترناشيونال هي الوكيل الحصري لـ ٧ من أرقى وأعرق دور التصميم الداخلي في إيطاليا. كل براند اختاريناه بعناية بناءً على تاريخه، دقة شغله، وتماشيه وية معايير الفخامة اللي ما نقبل نساوم عليها. ومن خلال هاي الشراكات الاستراتيجية، نوفر التصميم الإيطالي الأصلي، أجود الخامات، وأعلى مستويات الحرفية العالمية للبيوت العراقية مباشرةً.',
        ],
        'brands.intro.quote' => [
            'page' => 'brands', 'section' => 'intro', 'type' => 'text',
            'value_ar' => 'روح الفخامة الإيطالية.. بقلب بيوتنا العراقية.',
        ],

        // ─── Common CTA labels ─────────────────────────────────────
        'common.ctas.explore_partners' => [
            'page' => 'common', 'section' => 'ctas', 'type' => 'text',
            'value_ar' => 'اكتشفوا شركائنا',
        ],
        'common.ctas.book_consultation' => [
            'page' => 'common', 'section' => 'ctas', 'type' => 'text',
            'value_ar' => 'احجز استشارة الآن',
        ],
    ];

    /**
     * LUBE brand row update (brands table). Keyed by slug.
     */
    public const BRAND_UPDATES = [
        'lube' => [
            'category_ar' => 'إتقان المطابخ الإيطالية',
            'origin_ar' => 'تريا، ماشيراتا — إيطاليا',
            'description_ar' => 'ماركة لوبي (LUBE) هي أكثر شركة مطابخ حاصلة على جوائز تميز في إيطاليا، وهي شركة عائلية عريقة تنتج أكثر من 600 ألف مطبخ بالسنة. ومن خلال تعاوننا وية لوبي، ديلوس توفرلكم أرقى المعايير العالمية بتصميم المطابخ الإيطالية: اللي تجمع بين راحة الاستخدام المثالية، أجود المواد الأولية، وخيارات تصميم وتعديل ما تخلص حتى تناسب ذوقكم الخاص.',
        ],
    ];

    /**
     * CLI entry point: `php artisan db:seed --class=IraqiArabicContentSeeder`.
     * Returns the result array so the admin-panel controller can call this
     * directly and show the same per-row pass/fail grid.
     */
    public function run(): array
    {
        return self::apply();
    }

    /**
     * Pure-function apply — used by both the CLI run() above and the
     * admin-panel ApplyIraqiCopyController::apply(). Keeps data + logic
     * co-located so there's one source of truth.
     *
     * @return array{page_content_results: array<int,array<string,mixed>>, brand_results: array<int,array<string,mixed>>, snapshot_path: ?string, pass: int, fail: int}
     */
    public static function apply(): array
    {
        // 1. Snapshot current values before any mutation so an accidental
        //    bad apply can be reverted by hand.
        $snapshotPath = self::writeSnapshot();

        $pageContentResults = [];
        $brandResults = [];
        $pass = 0;
        $fail = 0;

        DB::transaction(function () use (&$pageContentResults, &$brandResults, &$pass, &$fail) {
            // Page-content writes
            foreach (self::PAGE_CONTENT_UPDATES as $key => $row) {
                try {
                    PageContent::updateOrCreate(
                        ['key' => $key],
                        [
                            'page' => $row['page'],
                            'section' => $row['section'],
                            'type' => $row['type'],
                            'value_ar' => $row['value_ar'],
                        ]
                    );
                    $pageContentResults[] = ['key' => $key, 'status' => 'pass', 'reason' => null];
                    $pass++;
                } catch (\Throwable $e) {
                    $pageContentResults[] = ['key' => $key, 'status' => 'fail', 'reason' => $e->getMessage()];
                    $fail++;
                }
            }

            // Brand row writes
            foreach (self::BRAND_UPDATES as $slug => $cols) {
                try {
                    $affected = Brand::where('slug', $slug)->update($cols);
                    $brandResults[] = [
                        'slug' => $slug,
                        'status' => $affected ? 'pass' : 'fail',
                        'reason' => $affected ? null : "no row with slug={$slug}",
                    ];
                    $affected ? $pass++ : $fail++;
                } catch (\Throwable $e) {
                    $brandResults[] = ['slug' => $slug, 'status' => 'fail', 'reason' => $e->getMessage()];
                    $fail++;
                }
            }
        });

        // 2. Bust all caches so the public /ar pages re-read from DB
        //    on the next request. Same pattern that the working
        //    PageContentController uses after every save.
        try {
            PageContent::clearCache();
        } catch (\Throwable) { /* cache driver may be offline */ }
        try {
            Cache::flush();
        } catch (\Throwable) { /* */ }
        $viewsCache = base_path('storage/framework/views');
        if (is_dir($viewsCache)) {
            foreach (glob($viewsCache . '/*.php') ?: [] as $f) { @unlink($f); }
        }
        if (function_exists('opcache_reset')) { @opcache_reset(); }

        return [
            'page_content_results' => $pageContentResults,
            'brand_results' => $brandResults,
            'snapshot_path' => $snapshotPath,
            'pass' => $pass,
            'fail' => $fail,
            'ran_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Capture current DB values for every target key so an admin can
     * revert manually if the apply result is unexpected.
     */
    private static function writeSnapshot(): ?string
    {
        try {
            $snapshot = [
                'at' => now()->format('c'),
                'page_content' => [],
                'brands' => [],
            ];
            foreach (array_keys(self::PAGE_CONTENT_UPDATES) as $key) {
                $row = PageContent::where('key', $key)->first();
                $snapshot['page_content'][$key] = $row
                    ? ['value_en' => $row->value_en, 'value_ar' => $row->value_ar, 'value_it' => $row->value_it]
                    : null;
            }
            foreach (array_keys(self::BRAND_UPDATES) as $slug) {
                $b = Brand::where('slug', $slug)->first();
                $snapshot['brands'][$slug] = $b
                    ? ['category_ar' => $b->category_ar, 'origin_ar' => $b->origin_ar, 'description_ar' => $b->description_ar, 'since' => $b->since]
                    : null;
            }
            $dir = storage_path('app');
            if (!is_dir($dir)) @mkdir($dir, 0775, true);
            $path = $dir . '/iraqi-copy-snapshot-' . date('Ymd-His') . '.json';
            @file_put_contents($path, json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return $path;
        } catch (\Throwable) {
            return null;
        }
    }
}
