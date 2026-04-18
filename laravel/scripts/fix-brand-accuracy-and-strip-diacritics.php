<?php

/**
 * One-off content cleanup pass.
 *
 *   1. Corrects factual mistakes in the Brand records
 *      (verified against each brand's official website, April 2026):
 *        • LUBE — "over 600,000 kitchens/year" → "over 65,000"
 *        • CANTORI — origin Forlì → Camerano; founding 1948 → 1976
 *        • CREO Kitchens — founding 1993 → 2014 (LUBE launched CREO in 2014)
 *        • FAER Ambienti — origin Brianza → Treia, Macerata; description
 *          "heart of Brianza" → "as part of Gruppo Lube in the Marche region"
 *
 *   2. Strips Arabic vocalization marks (tashkīl / shadda / damma / fatha
 *      etc., Unicode U+064B–U+0652 and U+0670) from every Arabic text
 *      field across brands, employees, services, projects, and
 *      page_contents. These marks look noisy in modern UI copy.
 *
 * Run:
 *   cd delos-website && php artisan tinker --execute="require 'scripts/fix-brand-accuracy-and-strip-diacritics.php';"
 */

use App\Models\Brand;
use App\Models\Employee;
use App\Models\PageContent;
use App\Models\Project;
use App\Models\Service;

/** Remove Arabic vocalization marks while keeping letters intact. */
$stripDiacritics = fn (?string $s) => $s === null
    ? null
    : preg_replace('/[\x{064B}-\x{0652}\x{0670}]/u', '', $s);

// ─── 1. Brand factual corrections ────────────────────────────────

$brandFixes = [
    'lube' => [
        // Production volume claim adjusted to match Wikipedia / official PR.
        'description_en' => [
            'from' => 'over 600,000 kitchens per year',
            'to'   => 'over 65,000 kitchens per year',
        ],
        'description_ar' => [
            'from' => 'أكثر من 600,000 مطبخ سنويًا',
            'to'   => 'أكثر من 65,000 مطبخ سنويا',
        ],
        'description_it' => [
            'from' => 'oltre 600.000 cucine all\'anno',
            'to'   => 'oltre 65.000 cucine all\'anno',
        ],
        // Correct the outward-facing URL — lubecucine.it does not resolve;
        // the official domain is cucinelube.it with an EN path available.
        'set' => [
            'url' => 'https://www.cucinelube.it/en/',
        ],
    ],
    'cantori' => [
        // Cantori is in Camerano (Ancona), Marche region — not Forlì, Emilia-Romagna.
        // Sante Cantori founded the company in 1976; Cantori Spa formalised in 1986.
        'set' => [
            'origin_en' => 'Camerano, Marche — Italy',
            'origin_ar' => 'كاميرانو، ماركي — إيطاليا',
            'origin_it' => 'Camerano, Marche — Italia',
            'since'     => 'Est. 1976',
        ],
    ],
    'creo-kitchens' => [
        // CREO Kitchens is the modern-affordable line launched by Gruppo Lube in 2014.
        'set' => [
            'since' => 'Est. 2014',
        ],
    ],
    'faer' => [
        // FAER Ambienti is located at Zona Artigianale Capoluogo, Treia (Macerata),
        // same industrial zone as LUBE — it's a Gruppo Lube atelier, not a Brianza workshop.
        // Founded by Gruppo Industriale Lube in 1995.
        'set' => [
            'origin_en' => 'Treia, Macerata — Italy',
            'origin_ar' => 'تريا، ماتشيراتا — إيطاليا',
            'origin_it' => 'Treia, Macerata — Italia',
            'since'     => 'Est. 1995',
        ],
        'description_en' => [
            'from' => 'crafted in the heart of Brianza — Italy\'s furniture-making region',
            'to'   => 'crafted by Gruppo Lube\'s Marche atelier',
        ],
        'description_ar' => [
            'from' => 'في قلب بريانتسا — منطقة صناعة الأثاث في إيطاليا',
            'to'   => 'في أتيليه مجموعة لوبي بمنطقة ماركي',
        ],
        'description_it' => [
            'from' => 'nel cuore della Brianza — la regione italiana dell\'arredamento',
            'to'   => 'dall\'atelier del Gruppo Lube nelle Marche',
        ],
    ],
];

echo '─── BRAND FACTS ─────────────────────────────' . PHP_EOL;
foreach ($brandFixes as $slug => $ops) {
    $b = Brand::where('slug', $slug)->first();
    if (!$b) { echo "  ! skipped (not found): {$slug}" . PHP_EOL; continue; }

    foreach ($ops as $field => $op) {
        if ($field === 'set') {
            foreach ($op as $k => $v) { $b->{$k} = $v; }
        } else {
            $current = $b->{$field} ?? '';
            if ($current && str_contains($current, $op['from'])) {
                $b->{$field} = str_replace($op['from'], $op['to'], $current);
            }
        }
    }
    $b->save();
    echo "  ✓ updated {$slug}" . PHP_EOL;
}

// ─── 2. Strip Arabic diacritics across all Arabic fields ─────────

$targets = [
    Brand::class        => ['name_ar', 'category_ar', 'origin_ar', 'description_ar'],
    Employee::class     => ['name_ar', 'role_ar', 'achievement_ar'],
    Service::class      => ['name_ar', 'description_ar'],
    Project::class      => ['title_ar', 'type_label_ar'],
    PageContent::class  => ['value_ar'],
];

echo PHP_EOL . '─── ARABIC DIACRITIC STRIP ──────────────────' . PHP_EOL;
$totalChanged = 0;
foreach ($targets as $class => $cols) {
    $short = class_basename($class);
    $table = (new $class)->getTable();
    // Only touch rows where at least one target column actually contains
    // a diacritic — avoids a pointless write for every row.
    $changed = 0;
    $class::query()->get()->each(function ($row) use ($cols, $stripDiacritics, &$changed) {
        $dirty = false;
        foreach ($cols as $col) {
            if (!isset($row->{$col})) continue;
            $before = $row->{$col};
            $after  = $stripDiacritics($before);
            if ($after !== null && $after !== $before) {
                $row->{$col} = $after;
                $dirty = true;
            }
        }
        if ($dirty) { $row->save(); $changed++; }
    });
    echo "  {$short} ({$table}): {$changed} row(s) cleaned" . PHP_EOL;
    $totalChanged += $changed;
}

echo PHP_EOL . "Done. {$totalChanged} rows updated across all tables." . PHP_EOL;
