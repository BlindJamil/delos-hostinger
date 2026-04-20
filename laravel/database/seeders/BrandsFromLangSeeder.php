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
                'description_en' => "LUBE is Italy's most awarded kitchen manufacturer — a family-owned company producing over 600,000 kitchens per year. With LUBE, Delos delivers the gold standard in Italian kitchen design: perfect ergonomics, premium materials, and endless customization.",
                'description_ar' => 'LUBE هي المصنّع الإيطالي الأكثر تتويجًا بالجوائز في مجال المطابخ — شركة عائلية تنتج أكثر من 600,000 مطبخ سنويًا. مع LUBE، تقدّم ديلوس المعيار الذهبي في تصميم المطابخ الإيطالية: أرغونوميا مثالية، موادّ فاخرة، وتخصيص لا حدود له.',
                'description_it' => "LUBE è il produttore italiano di cucine più premiato — un'azienda a conduzione familiare che produce oltre 600.000 cucine all'anno. Con LUBE, Delos offre il massimo standard nel design delle cucine italiane: ergonomia perfetta, materiali premium e infinita personalizzazione.",
                'specialties_en' => ['Fitted kitchens', 'Open-plan solutions', 'Island kitchens', 'Custom finishes'],
                'specialties_ar' => ['مطابخ مُجهَّزة', 'حلول المطابخ المفتوحة', 'مطابخ بجزيرة مركزية', 'تشطيبات مخصّصة'],
                'specialties_it' => ['Cucine componibili', 'Soluzioni open space', 'Cucine con isola', 'Finiture su misura'],
                'image' => 'lube-kitchen-3.jpg',
                'url' => 'https://www.lubecucine.it',
            ],
            [
                'slug' => 'frigerio',
                'name' => 'Frigerio',
                'category_en' => 'Classic Italian Furniture',
                'category_ar' => 'الأثاث الإيطالي الكلاسيكي',
                'category_it' => 'Arredamento classico italiano',
                'origin_en' => 'Brianza, Lombardy — Italy',
                'origin_ar' => 'بريانتسا، لومبارديا — إيطاليا',
                'origin_it' => 'Brianza, Lombardia — Italia',
                'since' => 'Est. 1955',
                'description_en' => "Frigerio represents the finest tradition of Brianza craftsmanship — Italy's historic furniture-making heartland. Known for timeless design and meticulous handcrafting, Frigerio pieces are investments in beauty that endure for generations.",
                'description_ar' => 'يُجسّد Frigerio أرقى تقاليد الحِرفة في منطقة بريانتسا — قلب تصنيع الأثاث التاريخي في إيطاليا. ومعروفة بتصميماتها الخالدة وصناعتها اليدوية الدقيقة، تُعدّ قطع Frigerio استثمارًا في الجمال يدوم لأجيال.',
                'description_it' => 'Frigerio rappresenta la migliore tradizione artigiana della Brianza — il cuore storico della produzione italiana di mobili. Noto per il design senza tempo e la lavorazione a mano meticolosa, gli arredi Frigerio sono investimenti di bellezza che durano per generazioni.',
                'specialties_en' => ['Living room collections', 'Dining furniture', 'Classic Italian design', 'Upholstery mastery'],
                'specialties_ar' => ['مجموعات غرف الجلوس', 'أثاث غرف الطعام', 'تصميم إيطالي كلاسيكي', 'إتقان التنجيد'],
                'specialties_it' => ['Collezioni per soggiorno', 'Arredi per sala da pranzo', 'Design italiano classico', "Maestria nell'imbottitura"],
                'image' => 'frigerio-living.jpg',
                'url' => 'https://www.frigeriosalotti.it',
            ],
            [
                'slug' => 'vittoria-frigerio',
                'name' => 'Vittoria Frigerio',
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
                'specialties_en' => ['Prestige bedroom collections', 'Heritage living suites', 'Bespoke finishes', 'Handcrafted details'],
                'specialties_ar' => ['مجموعات غرف النوم الفاخرة', 'أطقم غرف الجلوس التراثية', 'تشطيبات حسب الطلب', 'تفاصيل يدوية الصنع'],
                'specialties_it' => ['Collezioni per camera da letto prestige', 'Sale da pranzo heritage', 'Finiture su misura', 'Dettagli realizzati a mano'],
                'image' => 'collection-vittoria.jpg',
                'url' => 'https://www.vittoriafrigerio.it',
            ],
            [
                'slug' => 'cantori',
                'name' => 'CANTORI',
                'category_en' => 'Artisan Italian Creations',
                'category_ar' => 'إبداعات إيطالية حرفية',
                'category_it' => 'Creazioni italiane artigianali',
                'origin_en' => 'Forlì, Emilia-Romagna — Italy',
                'origin_ar' => 'فورلي، إميليا-رومانيا — إيطاليا',
                'origin_it' => 'Forlì, Emilia-Romagna — Italia',
                'since' => 'Est. 1948',
                'description_en' => 'CANTORI is synonymous with Italian artisan excellence. Each CANTORI piece is conceived as a work of art — using traditional crafting techniques combined with contemporary design sensibility. Their collections bring a unique, soulful quality to every interior.',
                'description_ar' => 'CANTORI مرادف للتميّز الحرفي الإيطالي. كلّ قطعة من CANTORI تُصوَّر بوصفها عملًا فنيًّا — باستخدام تقنيات الصناعة التقليدية الممزوجة بحسّ التصميم المعاصر. مجموعاتها تضفي جودة فريدة وذات روح على كلّ فضاء داخلي.',
                'description_it' => "CANTORI è sinonimo dell'eccellenza artigiana italiana. Ogni pezzo CANTORI è concepito come un'opera d'arte — utilizzando tecniche di lavorazione tradizionali unite a una sensibilità di design contemporanea. Le sue collezioni portano una qualità unica e intensa a ogni interno.",
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
                'origin_en' => 'Veneto Region — Italy',
                'origin_ar' => 'إقليم ڤينيتو — إيطاليا',
                'origin_it' => 'Veneto — Italia',
                'since' => 'Premium Flooring',
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
                'since' => 'Est. 1993',
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
                'specialties_en' => ['Living environments', 'Dining furniture', 'Bedroom collections', 'Custom interiors'],
                'specialties_ar' => ['فضاءات المعيشة', 'أثاث غرف الطعام', 'مجموعات غرف النوم', 'تصاميم داخلية مخصّصة'],
                'specialties_it' => ['Ambienti living', 'Arredi per sala da pranzo', 'Collezioni camera da letto', 'Interni su misura'],
                'image' => null,
                'url' => 'https://www.faer.it/en/',
            ],
        ];
    }
}
