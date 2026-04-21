<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

/**
 * Seeds the brands table from lang/{en,ar,it}/brands.php detail array so the
 * admin panel starts with the same 5 partner brands currently shown on the
 * public brands page. Idempotent via firstOrCreate keyed on slug.
 */
class BrandsFromLangSeeder extends Seeder
{
    public function run(): void
    {
        foreach (static::rows() as $index => $row) {
            $row['sort_order'] = ($index + 1) * 10;
            $row['active'] = true;
            Brand::firstOrCreate(
                ['slug' => $row['slug']],
                $row
            );
        }
    }

    public static function rows(): array
    {
        return [
            [
                'slug' => 'lube',
                'name' => 'LUBE',
                'category_en' => 'Italian Kitchen Excellence',
                'category_ar' => 'تميّز المطابخ الإيطالية',
                'category_it' => 'Eccellenza italiana in cucina',
                'origin_en' => 'Treia, Macerata — Italy',
                'origin_ar' => 'تريا، ماتشيراتا — إيطاليا',
                'origin_it' => 'Treia, Macerata — Italia',
                'since' => 'Est. 1967',
                'description_en' => "LUBE is one of Italy's leading kitchen manufacturers — a family-owned company producing 75,000 kitchens a year from its 150,000 m² Treia headquarters. With LUBE, Delos delivers the gold standard in Italian kitchen design: perfect ergonomics, premium materials, and endless customization.",
                'description_ar' => 'LUBE من أبرز مصنّعي المطابخ في إيطاليا — شركة عائلية تُنتج 75,000 مطبخ سنويًا من مقرّها في تريا الذي تبلغ مساحته 150,000 م². مع LUBE، تقدّم ديلوس المعيار الذهبي في تصميم المطابخ الإيطالية: أرغونوميا مثالية، موادّ فاخرة، وتخصيص لا حدود له.',
                'description_it' => "LUBE è tra i principali produttori italiani di cucine — un'azienda a conduzione familiare che realizza 75.000 cucine all'anno nella sua sede di Treia da 150.000 m². Con LUBE, Delos offre il massimo standard nel design delle cucine italiane: ergonomia perfetta, materiali premium e infinita personalizzazione.",
                'specialties_en' => ['Fitted kitchens', 'Open-plan solutions', 'Island kitchens', 'Custom finishes'],
                'specialties_ar' => ['مطابخ مُجهَّزة', 'حلول المطابخ المفتوحة', 'مطابخ بجزيرة مركزية', 'تشطيبات مخصّصة'],
                'specialties_it' => ['Cucine componibili', 'Soluzioni open space', 'Cucine con isola', 'Finiture su misura'],
                'image' => 'lube-kitchen-3.jpg',
                'url' => 'https://www.cucinelube.it',
            ],
            [
                'slug' => 'frigerio',
                'name' => 'Frigerio',
                'category_en' => 'Classic Italian Furniture',
                'category_ar' => 'الأثاث الإيطالي الكلاسيكي',
                'category_it' => 'Arredamento classico italiano',
                'origin_en' => 'Mariano Comense, Lombardy — Italy',
                'origin_ar' => 'ماريانو كومينسي، لومبارديا — إيطاليا',
                'origin_it' => 'Mariano Comense, Lombardia — Italia',
                'since' => 'Est. 1938',
                'description_en' => "Frigerio represents the finest tradition of Brianza craftsmanship — Italy's historic furniture-making heartland. Known for timeless design and meticulous handcrafting, Frigerio pieces are investments in beauty that endure for generations.",
                'description_ar' => 'يُجسّد Frigerio أرقى تقاليد الحِرفة في منطقة بريانتسا — قلب تصنيع الأثاث التاريخي في إيطاليا. ومعروفة بتصميماتها الخالدة وصناعتها اليدوية الدقيقة، تُعدّ قطع Frigerio استثمارًا في الجمال يدوم لأجيال.',
                'description_it' => 'Frigerio rappresenta la migliore tradizione artigiana della Brianza — il cuore storico della produzione italiana di mobili. Noto per il design senza tempo e la lavorazione a mano meticolosa, gli arredi Frigerio sono investimenti di bellezza che durano per generazioni.',
                'specialties_en' => ['Living room collections', 'Dining furniture', 'Classic Italian design', 'Upholstery mastery'],
                'specialties_ar' => ['مجموعات غرف الجلوس', 'أثاث غرف الطعام', 'تصميم إيطالي كلاسيكي', 'إتقان التنجيد'],
                'specialties_it' => ['Collezioni per soggiorno', 'Arredi per sala da pranzo', 'Design italiano classico', "Maestria nell'imbottitura"],
                'image' => 'frigerio-living.jpg',
                'url' => 'https://www.frigerio.com',
            ],
            [
                'slug' => 'vittoria-frigerio',
                'name' => 'Vittoria Frigerio',
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
                'specialties_en' => ['Contemporary living suites', 'Upholstered collections', 'Bespoke finishes', 'Handcrafted details'],
                'specialties_ar' => ['أطقم غرف جلوس معاصرة', 'مجموعات منجَّدة', 'تشطيبات حسب الطلب', 'تفاصيل يدوية الصنع'],
                'specialties_it' => ['Ambienti living contemporanei', "Collezioni d'imbottito", 'Finiture su misura', 'Dettagli realizzati a mano'],
                'image' => 'collection-vittoria.jpg',
                'url' => 'https://www.vittoriafrigerio.it',
            ],
            [
                'slug' => 'cantori',
                'name' => 'CANTORI',
                'category_en' => 'Artisan Italian Creations',
                'category_ar' => 'إبداعات إيطالية حرفية',
                'category_it' => 'Creazioni italiane artigianali',
                'origin_en' => 'Camerano, Marche — Italy',
                'origin_ar' => 'كاميرانو، ماركي — إيطاليا',
                'origin_it' => 'Camerano, Marche — Italia',
                'since' => 'Est. 1976',
                'description_en' => "Founded in 1976 by Sante Cantori in the craft heartland of Camerano, in Italy's Marche region, CANTORI is synonymous with Italian artisan excellence. Each piece is conceived as a work of art — traditional crafting techniques combined with contemporary design sensibility — bringing a unique, soulful quality to every interior.",
                'description_ar' => 'تأسّست CANTORI عام 1976 على يد سانتي كانتوري في قلب الحِرف التقليدية بمدينة كاميرانو في إقليم ماركي الإيطالي، وهي مرادف للتميّز الحرفي الإيطالي. كلّ قطعة تُصوَّر بوصفها عملًا فنيًّا — تقنيات صناعة تقليدية تمتزج بحسّ تصميم معاصر — لتمنح كلّ فضاء داخلي جودة فريدة وذات روح.',
                'description_it' => "Fondata nel 1976 da Sante Cantori nel cuore artigiano di Camerano, nelle Marche, CANTORI è sinonimo dell'eccellenza artigiana italiana. Ogni pezzo è concepito come un'opera d'arte — tecniche di lavorazione tradizionali unite a una sensibilità di design contemporanea — per portare una qualità unica e intensa a ogni interno.",
                'specialties_en' => ['Accent furniture', 'Statement pieces', 'Metal and glass artistry', 'Custom commissions'],
                'specialties_ar' => ['أثاث مميّز', 'قطع إعلانية', 'فنّ المعدن والزجاج', 'طلبات خاصّة'],
                'specialties_it' => ["Arredi d'accento", 'Pezzi iconici', 'Arte del metallo e del vetro', 'Commissioni su misura'],
                'image' => 'cantori-1.jpg',
                'url' => 'https://www.cantori.it',
            ],
            [
                'slug' => 'skema',
                'name' => 'SKEMA',
                'category_en' => 'Premium Italian Flooring',
                'category_ar' => 'أرضيات إيطالية فاخرة',
                'category_it' => 'Pavimentazioni italiane premium',
                'origin_en' => 'Ponte di Piave, Veneto — Italy',
                'origin_ar' => 'بونتي دي بيافي، ڤينيتو — إيطاليا',
                'origin_it' => 'Ponte di Piave, Veneto — Italia',
                'since' => 'Est. 1992',
                'description_en' => 'SKEMA combines Italian design culture with advanced engineering to produce flooring systems of remarkable beauty and technical excellence. From natural wood to contemporary engineered surfaces, SKEMA creates the foundation upon which great interiors are built.',
                'description_ar' => 'تجمع SKEMA بين ثقافة التصميم الإيطالية والهندسة المتقدّمة لإنتاج أنظمة أرضيات تتميّز بجمال لافت وتميّز تقني. من الخشب الطبيعي إلى الأسطح المهندَسة المعاصرة، تصنع SKEMA الأساس الذي تُبنى عليه أجمل الفضاءات الداخلية.',
                'description_it' => "SKEMA unisce la cultura italiana del design all'ingegneria avanzata per produrre sistemi di pavimentazione di straordinaria bellezza ed eccellenza tecnica. Dal legno naturale alle superfici ingegnerizzate contemporanee, SKEMA crea le fondamenta su cui si costruiscono i grandi interni.",
                'specialties_en' => ['Engineered wood flooring', 'Natural hardwood', 'Contemporary surfaces', 'Wall panel systems'],
                'specialties_ar' => ['أرضيات خشبية مهندَسة', 'خشب طبيعي صلب', 'أسطح معاصرة', 'أنظمة ألواح جدارية'],
                'specialties_it' => ['Pavimenti in legno ingegnerizzato', 'Legno duro naturale', 'Superfici contemporanee', 'Sistemi di pannellatura a parete'],
                'image' => 'italian-materials.jpg',
                'url' => 'https://www.skema.eu',
            ],
            [
                'slug' => 'creo-kitchens',
                'name' => 'CREO Kitchens',
                'category_en' => 'Modern Italian Kitchens',
                'category_ar' => 'مطابخ إيطالية عصرية',
                'category_it' => 'Cucine italiane moderne',
                'origin_en' => 'Treia, Macerata — Italy',
                'origin_ar' => 'تريا، ماتشيراتا — إيطاليا',
                'origin_it' => 'Treia, Macerata — Italia',
                'since' => 'A Gruppo LUBE Brand',
                'description_en' => "CREO Kitchens delivers contemporary Italian kitchen design with sharp lines, smart storage, and quality finishes — accessible luxury without compromise. Part of the LUBE family, CREO brings the same Italian manufacturing excellence to fresh, modern aesthetics.",
                'description_ar' => 'تقدّم CREO Kitchens تصميم المطابخ الإيطالية المعاصرة بخطوط حادّة وتخزين ذكي وتشطيبات راقية — فخامة في المتناول دون أيّ تنازل. وكونها جزءًا من عائلة LUBE، تُوفّر CREO المستوى نفسه من التميّز التصنيعي الإيطالي في إطار جمالي عصري ومنعش.',
                'description_it' => "CREO Kitchens propone il design contemporaneo della cucina italiana con linee nette, soluzioni di contenimento intelligenti e finiture di qualità — un lusso accessibile senza compromessi. Parte della famiglia LUBE, CREO porta la stessa eccellenza manifatturiera italiana in un'estetica moderna e fresca.",
                'specialties_en' => ['Modern kitchens', 'Smart storage', 'Compact solutions', 'Contemporary finishes'],
                'specialties_ar' => ['مطابخ عصرية', 'تخزين ذكي', 'حلول مدمجة', 'تشطيبات معاصرة'],
                'specialties_it' => ['Cucine moderne', 'Soluzioni di contenimento smart', 'Soluzioni compatte', 'Finiture contemporanee'],
                'image' => null,
                'url' => 'https://www.creokitchens.it/en/',
            ],
            [
                'slug' => 'faer',
                'name' => 'FAER Ambienti',
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
                'specialties_en' => ['Wardrobes', 'Walk-in closets', 'Bed collections', 'Night-time furnishings'],
                'specialties_ar' => ['خزائن ملابس', 'غرف دريسنغ', 'مجموعات أسرّة', 'قطع أثاث لغرف النوم'],
                'specialties_it' => ['Armadi', 'Cabine armadio', 'Collezioni letti', 'Zona notte'],
                'image' => null,
                'url' => 'https://www.faer.it/en/',
            ],
        ];
    }
}
