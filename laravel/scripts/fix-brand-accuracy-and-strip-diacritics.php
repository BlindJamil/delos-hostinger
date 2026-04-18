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
        // Specialties refined to reflect Cantori's actual breadth (beds, tables,
        // mirrors, lighting) while keeping the artistic signature.
        'set' => [
            'origin_en'      => 'Camerano, Marche — Italy',
            'origin_ar'      => 'كاميرانو، ماركي — إيطاليا',
            'origin_it'      => 'Camerano, Marche — Italia',
            'since'          => 'Est. 1976',
            'specialties_en' => ['Beds & seating', 'Tables & storage', 'Mirrors & lighting', 'Metal and glass artistry'],
            'specialties_ar' => ['أسرة ومقاعد', 'طاولات وخزائن', 'مرايا وإضاءة', 'فن المعدن والزجاج'],
            'specialties_it' => ['Letti e sedute', 'Tavoli e contenitori', 'Specchi e illuminazione', 'Arte del metallo e del vetro'],
        ],
    ],
    'faer' => [
        // FAER Ambienti is located at Zona Artigianale Capoluogo, Treia (Macerata),
        // same industrial zone as LUBE — it's a Gruppo Lube atelier, not a Brianza workshop.
        // Founded by Gruppo Industriale Lube in 1995. FAER's actual catalogue is
        // BEDROOM FURNITURE ONLY (wardrobes, beds, walk-in closets, night sets) —
        // the previous "living/dining" positioning was incorrect.
        'set' => [
            'origin_en'       => 'Treia, Macerata — Italy',
            'origin_ar'       => 'تريا، ماتشيراتا — إيطاليا',
            'origin_it'       => 'Treia, Macerata — Italia',
            'since'           => 'Est. 1995',
            'specialties_en'  => ['Wardrobes & walk-in closets', 'Beds & bedroom sets', 'Children\'s bedroom programmes', 'Bespoke night-area interiors'],
            'specialties_ar'  => ['خزائن وغرف تبديل ملابس', 'أسرة وأطقم غرف نوم', 'برامج غرف نوم الأطفال', 'تصاميم داخلية حسب الطلب لفضاء النوم'],
            'specialties_it'  => ['Armadi e cabine armadio', 'Letti e camere da letto complete', 'Programmi per camerette', 'Ambienti notte su misura'],
        ],
        'description_en' => [
            'from' => 'specializing in refined living and dining environments. Each FAER piece is crafted in the heart of Brianza — Italy\'s furniture-making region',
            'to'   => 'specializing in refined night-area environments — wardrobes, beds, and walk-in closets. Each FAER piece is crafted by Gruppo Lube\'s Marche atelier',
        ],
        'description_ar' => [
            'from' => 'متخصص في إبداع فضاءات معيشة وطعام راقية. تصنع كل قطعة من FAER في قلب بريانتسا — منطقة صناعة الأثاث في إيطاليا',
            'to'   => 'متخصص في فضاءات النوم الراقية — خزائن وأسرة وغرف تبديل ملابس. تصنع كل قطعة من FAER في أتيليه مجموعة لوبي بمنطقة ماركي',
        ],
        'description_it' => [
            'from' => 'specializzato in raffinati ambienti living e dining. Ogni pezzo FAER è realizzato nel cuore della Brianza — la regione italiana dell\'arredamento',
            'to'   => 'specializzato in raffinati ambienti notte — armadi, letti e cabine armadio. Ogni pezzo FAER è realizzato dall\'atelier del Gruppo Lube nelle Marche',
        ],
    ],
    'skema' => [
        // SKEMA's flagship is SPC rigid-polymer flooring + outdoor WPC decking.
        // Previous list missed both and used generic "Contemporary surfaces".
        'set' => [
            'specialties_en' => ['Wood parquet flooring', 'SPC technical floors', 'Outdoor WPC decking', 'Acoustic wall & ceiling systems'],
            'specialties_ar' => ['أرضيات باركيه خشبية', 'أرضيات SPC تقنية', 'ألواح خارجية من مركب WPC', 'أنظمة جدران وأسقف عازلة للصوت'],
            'specialties_it' => ['Pavimenti in parquet', 'Pavimenti tecnici SPC', 'Decking da esterno in WPC', 'Rivestimenti fonoassorbenti per pareti e soffitti'],
        ],
    ],
    'creo-kitchens' => [
        // CREO Kitchens is Gruppo Lube's modern-affordable line, launched 2014.
        // CREO makes BOTH modern AND classic kitchens, plus ColorLab colour
        // customization. Previous list was all "modern/contemporary" and
        // missed half their catalogue; and "Est. 1993" was wrong.
        'set' => [
            'since'          => 'Est. 2014',
            'specialties_en' => ['Modern kitchens', 'Classic kitchens', 'Custom colour finishes', 'Smart storage & compact layouts'],
            'specialties_ar' => ['مطابخ عصرية', 'مطابخ كلاسيكية', 'تشطيبات ألوان حسب الطلب', 'تخزين ذكي وحلول مدمجة'],
            'specialties_it' => ['Cucine moderne', 'Cucine classiche', 'Finiture colore personalizzate', 'Soluzioni compatte e smart'],
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
