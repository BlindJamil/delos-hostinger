<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * One-off fact correction for the `brands` table.
 *
 * BrandsFromLangSeeder uses firstOrCreate keyed on slug, which means the
 * canonical brand data in the seeder does NOT overwrite existing rows on
 * re-deploy. That left several factual errors stranded in production:
 *
 *   - LUBE: description claimed "over 600,000 kitchens/year" and "most
 *     awarded in Italy" — the actual figure from cucinelube.it is
 *     75,000/year; the "most awarded" claim is unverifiable. URL was
 *     pointing at `lubecucine.it` which 301s to the canonical
 *     `cucinelube.it`.
 *   - Frigerio: est. 1955 → actually 1938 per frigerio.com/en/company.
 *     URL at `frigeriosalotti.it` redirects to `frigerio.com`.
 *   - Vittoria Frigerio: positioned as "Heritage Collection" — it was
 *     launched in 2012 as Frigerio's contemporary luxury line per Salone
 *     del Mobile / Frigerio press.
 *   - CANTORI: "Forlì, Emilia-Romagna · Est. 1948" → actually
 *     "Camerano, Marche · Est. 1976" per cantori.it/en/company.
 *   - SKEMA: origin was just "Veneto Region", no founding year. Site
 *     confirms Ponte di Piave, Treviso (Veneto), founded 1992.
 *   - CREO Kitchens: "Est. 1993" is not the brand's launch — 1993 was
 *     LUBE's factory move. Replaced with "A Gruppo LUBE Brand".
 *   - FAER Ambienti: "Brianza — Italy" and "crafted in the heart of
 *     Brianza" — FAER is in Treia, Marche (same industrial complex as
 *     LUBE/CREO), a Gruppo LUBE brand specializing in bedrooms and
 *     wardrobes. Every Brianza reference removed.
 *
 * This migration pushes the corrected values into the live DB once.
 * Future admin edits through /admin/brands/{id}/edit are preserved —
 * the seeder's firstOrCreate stays unchanged.
 */
return new class extends Migration {
    public function up(): void
    {
        foreach ($this->corrections() as $slug => $fields) {
            DB::table('brands')->where('slug', $slug)->update($fields);
        }
    }

    public function down(): void
    {
        foreach ($this->rollbackValues() as $slug => $fields) {
            DB::table('brands')->where('slug', $slug)->update($fields);
        }
    }

    /**
     * Corrected field values — what each brand row SHOULD say after this
     * migration runs. Mirrors BrandsFromLangSeeder exactly so a fresh
     * install and a migrated old install end up in the same state.
     *
     * Specialty arrays use json_encode because the brands table stores
     * them as a cast JSON column (same as the seeder path, which relies
     * on Eloquent's array cast).
     */
    private function corrections(): array
    {
        return [
            'lube' => [
                'url' => 'https://www.cucinelube.it',
                'description_en' => "LUBE is one of Italy's leading kitchen manufacturers — a family-owned company producing 75,000 kitchens a year from its 150,000 m² Treia headquarters. With LUBE, Delos delivers the gold standard in Italian kitchen design: perfect ergonomics, premium materials, and endless customization.",
                'description_ar' => 'LUBE من أبرز مصنّعي المطابخ في إيطاليا — شركة عائلية تُنتج 75,000 مطبخ سنويًا من مقرّها في تريا الذي تبلغ مساحته 150,000 م². مع LUBE، تقدّم ديلوس المعيار الذهبي في تصميم المطابخ الإيطالية: أرغونوميا مثالية، موادّ فاخرة، وتخصيص لا حدود له.',
                'description_it' => "LUBE è tra i principali produttori italiani di cucine — un'azienda a conduzione familiare che realizza 75.000 cucine all'anno nella sua sede di Treia da 150.000 m². Con LUBE, Delos offre il massimo standard nel design delle cucine italiane: ergonomia perfetta, materiali premium e infinita personalizzazione.",
            ],
            'frigerio' => [
                'url' => 'https://www.frigerio.com',
                'origin_en' => 'Mariano Comense, Lombardy — Italy',
                'origin_ar' => 'ماريانو كومينسي، لومبارديا — إيطاليا',
                'origin_it' => 'Mariano Comense, Lombardia — Italia',
                'since' => 'Est. 1938',
            ],
            'vittoria-frigerio' => [
                'category_en' => 'Contemporary Luxury',
                'category_ar' => 'فخامة معاصرة',
                'category_it' => 'Lusso contemporaneo',
                'origin_en' => 'Mariano Comense, Lombardy — Italy',
                'origin_ar' => 'ماريانو كومينسي، لومبارديا — إيطاليا',
                'origin_it' => 'Mariano Comense, Lombardia — Italia',
                'since' => 'Since 2012',
                'description_en' => 'Launched in 2012 as the contemporary luxury line of Frigerio, Vittoria Frigerio reinterprets timeless Brianza craftsmanship through a modern lens — sumptuous yet simple silhouettes, precious materials, and a design language built for interiors that want to feel current and refined in equal measure.',
                'description_ar' => 'أُطلق خطّ Vittoria Frigerio عام 2012 بوصفه الخطّ الفاخر المعاصر لعائلة Frigerio، حيث يُعيد تفسير حِرفة بريانتسا الخالدة بلغة تصميم حديثة — خطوط فاخرة وبسيطة في آن، وموادّ نفيسة، وحِسّ تصميم مصمَّم للفضاءات التي تريد أن تبدو عصرية وراقية في الوقت نفسه.',
                'description_it' => "Nata nel 2012 come linea di lusso contemporaneo della famiglia Frigerio, Vittoria Frigerio reinterpreta la tradizione artigiana della Brianza con un linguaggio moderno — silhouette sontuose ma essenziali, materiali preziosi e un design pensato per interni che vogliono essere insieme attuali e raffinati.",
                'specialties_en' => json_encode(['Contemporary living suites', 'Upholstered collections', 'Bespoke finishes', 'Handcrafted details'], JSON_UNESCAPED_UNICODE),
                'specialties_ar' => json_encode(['أطقم غرف جلوس معاصرة', 'مجموعات منجَّدة', 'تشطيبات حسب الطلب', 'تفاصيل يدوية الصنع'], JSON_UNESCAPED_UNICODE),
                'specialties_it' => json_encode(['Ambienti living contemporanei', "Collezioni d'imbottito", 'Finiture su misura', 'Dettagli realizzati a mano'], JSON_UNESCAPED_UNICODE),
            ],
            'cantori' => [
                'origin_en' => 'Camerano, Marche — Italy',
                'origin_ar' => 'كاميرانو، ماركي — إيطاليا',
                'origin_it' => 'Camerano, Marche — Italia',
                'since' => 'Est. 1976',
                'description_en' => "Founded in 1976 by Sante Cantori in the craft heartland of Camerano, in Italy's Marche region, CANTORI is synonymous with Italian artisan excellence. Each piece is conceived as a work of art — traditional crafting techniques combined with contemporary design sensibility — bringing a unique, soulful quality to every interior.",
                'description_ar' => 'تأسّست CANTORI عام 1976 على يد سانتي كانتوري في قلب الحِرف التقليدية بمدينة كاميرانو في إقليم ماركي الإيطالي، وهي مرادف للتميّز الحرفي الإيطالي. كلّ قطعة تُصوَّر بوصفها عملًا فنيًّا — تقنيات صناعة تقليدية تمتزج بحسّ تصميم معاصر — لتمنح كلّ فضاء داخلي جودة فريدة وذات روح.',
                'description_it' => "Fondata nel 1976 da Sante Cantori nel cuore artigiano di Camerano, nelle Marche, CANTORI è sinonimo dell'eccellenza artigiana italiana. Ogni pezzo è concepito come un'opera d'arte — tecniche di lavorazione tradizionali unite a una sensibilità di design contemporanea — per portare una qualità unica e intensa a ogni interno.",
            ],
            'skema' => [
                'origin_en' => 'Ponte di Piave, Veneto — Italy',
                'origin_ar' => 'بونتي دي بيافي، ڤينيتو — إيطاليا',
                'origin_it' => 'Ponte di Piave, Veneto — Italia',
                'since' => 'Est. 1992',
            ],
            'creo-kitchens' => [
                'since' => 'A Gruppo LUBE Brand',
            ],
            'faer' => [
                'category_en' => 'Italian Bedroom & Wardrobe Specialists',
                'category_ar' => 'متخصّصون في غرف النوم وخزائن الملابس الإيطالية',
                'category_it' => 'Specialisti italiani in camere da letto e armadi',
                'origin_en' => 'Treia, Marche — Italy',
                'origin_ar' => 'تريا، ماركي — إيطاليا',
                'origin_it' => 'Treia, Marche — Italia',
                'since' => 'A Gruppo LUBE Brand',
                'description_en' => "FAER Ambienti is the bedroom and wardrobe specialist of Gruppo LUBE, crafted in the same Treia, Marche industrial complex as LUBE and CREO. FAER focuses on refined sleeping environments — wardrobes, walk-in closets, beds, and night collections — combining timeless silhouettes with materials chosen for their character and quality.",
                'description_ar' => 'FAER Ambienti هي الذراع المتخصّصة في غرف النوم وخزائن الملابس ضمن مجموعة Gruppo LUBE، وتُصنع في المجمّع الصناعي نفسه في تريا بإقليم ماركي إلى جانب LUBE وCREO. تركّز FAER على فضاءات النوم الراقية — خزائن ملابس، ودريسنغ، وأسِرّة، ومجموعات ليلية — جامعةً بين الخطوط الخالدة وموادّ مختارة بعناية لطابعها وجودتها.',
                'description_it' => "FAER Ambienti è lo specialista di camere da letto e armadi del Gruppo LUBE, prodotto nello stesso polo industriale di Treia, nelle Marche, che ospita anche LUBE e CREO. FAER è dedicato agli ambienti notte di alta gamma — armadi, cabine armadio, letti e collezioni notte — combinando silhouette senza tempo con materiali scelti per carattere e qualità.",
                'specialties_en' => json_encode(['Wardrobes', 'Walk-in closets', 'Bed collections', 'Night-time furnishings'], JSON_UNESCAPED_UNICODE),
                'specialties_ar' => json_encode(['خزائن ملابس', 'غرف دريسنغ', 'مجموعات أسرّة', 'قطع أثاث لغرف النوم'], JSON_UNESCAPED_UNICODE),
                'specialties_it' => json_encode(['Armadi', 'Cabine armadio', 'Collezioni letti', 'Zona notte'], JSON_UNESCAPED_UNICODE),
            ],
        ];
    }

    /**
     * Pre-migration values, for `down()`. These are the (incorrect) values
     * that were live before this migration ran — restoring them is only
     * useful if someone needs to roll back the release for an unrelated
     * reason and wants the DB exactly the way it was.
     */
    private function rollbackValues(): array
    {
        return [
            'lube' => [
                'url' => 'https://www.lubecucine.it',
                'description_en' => "LUBE is Italy's most awarded kitchen manufacturer — a family-owned company producing over 600,000 kitchens per year. With LUBE, Delos delivers the gold standard in Italian kitchen design: perfect ergonomics, premium materials, and endless customization.",
                'description_ar' => 'LUBE هي المصنّع الإيطالي الأكثر تتويجًا بالجوائز في مجال المطابخ — شركة عائلية تنتج أكثر من 600,000 مطبخ سنويًا. مع LUBE، تقدّم ديلوس المعيار الذهبي في تصميم المطابخ الإيطالية: أرغونوميا مثالية، موادّ فاخرة، وتخصيص لا حدود له.',
                'description_it' => "LUBE è il produttore italiano di cucine più premiato — un'azienda a conduzione familiare che produce oltre 600.000 cucine all'anno. Con LUBE, Delos offre il massimo standard nel design delle cucine italiane: ergonomia perfetta, materiali premium e infinita personalizzazione.",
            ],
            'frigerio' => [
                'url' => 'https://www.frigeriosalotti.it',
                'origin_en' => 'Brianza, Lombardy — Italy',
                'origin_ar' => 'بريانتسا، لومبارديا — إيطاليا',
                'origin_it' => 'Brianza, Lombardia — Italia',
                'since' => 'Est. 1955',
            ],
            'vittoria-frigerio' => [
                'category_en' => 'Heritage Luxury Design',
                'category_ar' => 'تصميم فاخر بطابع تراثي',
                'category_it' => 'Design di lusso heritage',
                'origin_en' => 'Brianza, Lombardy — Italy',
                'origin_ar' => 'بريانتسا، لومبارديا — إيطاليا',
                'origin_it' => 'Brianza, Lombardia — Italia',
                'since' => 'Heritage Collection',
                'description_en' => 'The prestige line within the Frigerio family, Vittoria Frigerio represents the absolute pinnacle of Italian interior artistry. Each piece is a statement of heritage, refinement, and extraordinary craftsmanship reserved for the most discerning spaces.',
                'description_ar' => 'الخط الرفيع ضمن عائلة Frigerio، يمثّل Vittoria Frigerio قمّة الفنّ الداخلي الإيطالي. كلّ قطعة بيان عن التراث والرقيّ والحِرفة الاستثنائية، محفوظة لأكثر الأمكنة تميّزًا.',
                'description_it' => "La linea prestige all'interno della famiglia Frigerio, Vittoria Frigerio rappresenta l'apice assoluto dell'arte italiana degli interni. Ogni pezzo è una dichiarazione di storia, raffinatezza e artigianalità straordinaria, riservata agli ambienti più esigenti.",
                'specialties_en' => json_encode(['Prestige bedroom collections', 'Heritage living suites', 'Bespoke finishes', 'Handcrafted details'], JSON_UNESCAPED_UNICODE),
                'specialties_ar' => json_encode(['مجموعات غرف النوم الفاخرة', 'أطقم غرف الجلوس التراثية', 'تشطيبات حسب الطلب', 'تفاصيل يدوية الصنع'], JSON_UNESCAPED_UNICODE),
                'specialties_it' => json_encode(['Collezioni per camera da letto prestige', 'Sale da pranzo heritage', 'Finiture su misura', 'Dettagli realizzati a mano'], JSON_UNESCAPED_UNICODE),
            ],
            'cantori' => [
                'origin_en' => 'Forlì, Emilia-Romagna — Italy',
                'origin_ar' => 'فورلي، إميليا-رومانيا — إيطاليا',
                'origin_it' => 'Forlì, Emilia-Romagna — Italia',
                'since' => 'Est. 1948',
                'description_en' => 'CANTORI is synonymous with Italian artisan excellence. Each CANTORI piece is conceived as a work of art — using traditional crafting techniques combined with contemporary design sensibility. Their collections bring a unique, soulful quality to every interior.',
                'description_ar' => 'CANTORI مرادف للتميّز الحرفي الإيطالي. كلّ قطعة من CANTORI تُصوَّر بوصفها عملًا فنيًّا — باستخدام تقنيات الصناعة التقليدية الممزوجة بحسّ التصميم المعاصر. مجموعاتها تضفي جودة فريدة وذات روح على كلّ فضاء داخلي.',
                'description_it' => "CANTORI è sinonimo dell'eccellenza artigiana italiana. Ogni pezzo CANTORI è concepito come un'opera d'arte — utilizzando tecniche di lavorazione tradizionali unite a una sensibilità di design contemporanea. Le sue collezioni portano una qualità unica e intensa a ogni interno.",
            ],
            'skema' => [
                'origin_en' => 'Veneto Region — Italy',
                'origin_ar' => 'إقليم ڤينيتو — إيطاليا',
                'origin_it' => 'Veneto — Italia',
                'since' => 'Premium Flooring',
            ],
            'creo-kitchens' => [
                'since' => 'Est. 1993',
            ],
            'faer' => [
                'category_en' => 'Italian Furniture Atelier',
                'category_ar' => 'أتيليه أثاث إيطالي',
                'category_it' => "Atelier italiano d'arredamento",
                'origin_en' => 'Brianza — Italy',
                'origin_ar' => 'بريانتسا — إيطاليا',
                'origin_it' => 'Brianza — Italia',
                'since' => 'Italian Atelier',
                'description_en' => "FAER Ambienti is an Italian furniture atelier specializing in refined living and dining environments. Each FAER piece is crafted in the heart of Brianza — Italy's furniture-making region — combining timeless silhouettes with materials chosen for their character and quality.",
                'description_ar' => 'FAER Ambienti أتيليه إيطالي متخصّص في إبداع فضاءات معيشة وطعام راقية. تُصنع كلّ قطعة من FAER في قلب بريانتسا — منطقة صناعة الأثاث في إيطاليا — حيث تجتمع الخطوط الخالدة مع موادّ مختارة بعناية لطابعها وجودتها.',
                'description_it' => "FAER Ambienti è un atelier italiano d'arredamento specializzato in raffinati ambienti living e dining. Ogni pezzo FAER è realizzato nel cuore della Brianza — la regione italiana dell'arredamento — combinando silhouette senza tempo con materiali scelti per carattere e qualità.",
                'specialties_en' => json_encode(['Living environments', 'Dining furniture', 'Bedroom collections', 'Custom interiors'], JSON_UNESCAPED_UNICODE),
                'specialties_ar' => json_encode(['فضاءات المعيشة', 'أثاث غرف الطعام', 'مجموعات غرف النوم', 'تصاميم داخلية مخصّصة'], JSON_UNESCAPED_UNICODE),
                'specialties_it' => json_encode(['Ambienti living', 'Arredi per sala da pranzo', 'Collezioni camera da letto', 'Interni su misura'], JSON_UNESCAPED_UNICODE),
            ],
        ];
    }
};
