<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Seeds the services table from lang/{en,ar,it}/services.php items array so
 * the admin panel starts with the six Italian crafts currently on the public
 * services page. Idempotent via firstOrCreate keyed on slug.
 */
class ServicesFromLangSeeder extends Seeder
{
    public function run(): void
    {
        // Production guard: once an admin has populated this table — even
        // with a single row — never re-inject seed rows. firstOrCreate is
        // only safe against updates, not against deletes; an admin-deleted
        // record would resurrect on every deploy without this guard. The
        // lang file rows() are kept as the baseline for fresh installs.
        if (Service::query()->exists()) {
            return;
        }

        foreach (static::rows() as $index => $row) {
            $row['sort_order'] = ($index + 1) * 10;
            $row['active'] = true;
            Service::firstOrCreate(
                ['slug' => $row['slug']],
                $row
            );
        }
    }

    public static function rows(): array
    {
        return [
            [
                'slug' => 'italian-kitchens',
                'num' => '01',
                'name_en' => 'Italian Kitchens',
                'name_ar' => 'المطابخ الإيطالية',
                'name_it' => 'Cucine italiane',
                'description_en' => "Your kitchen is the heart of your home. We bring Italy's finest kitchen manufacturers directly to your door — precision-engineered, beautifully finished, and designed around your lifestyle. From timeless classic to ultra-contemporary, every Delos kitchen is a masterpiece of Italian craft.",
                'description_ar' => 'المطبخ هو قلب منزلك. نجلب إلى بابك أرقى المصنّعين الإيطاليين — بهندسة دقيقة وتشطيبات بديعة وتصميم يدور حول نمط حياتك. من الكلاسيكي الخالد إلى المعاصر المتطوّر، كلّ مطبخ من ديلوس تحفة من الحِرفة الإيطالية.',
                'description_it' => "La tua cucina è il cuore della casa. Portiamo direttamente a casa tua i migliori produttori italiani di cucine — con un'ingegneria precisa, finiture raffinate e un progetto costruito sul tuo stile di vita. Dal classico senza tempo all'ultra contemporaneo, ogni cucina Delos è un capolavoro di artigianalità italiana.",
                'features_en' => ['Custom Italian cabinetry', 'Premium hardware and finishes', 'Integrated appliance solutions', 'Ergonomic spatial design', '3D planning service'],
                'features_ar' => ['خزائن إيطالية مخصّصة', 'مقابض وتشطيبات فاخرة', 'حلول أجهزة مدمجة', 'تصميم مكاني مريح', 'خدمة تصميم ثلاثي الأبعاد'],
                'features_it' => ['Mobili italiani su misura', 'Ferramenta e finiture premium', 'Soluzioni di elettrodomestici integrati', 'Design spaziale ergonomico', 'Servizio di progettazione 3D'],
                'brand' => 'LUBE Kitchens',
                'image' => 'lube-kitchen-1.jpeg',
            ],
            [
                'slug' => 'dressing-rooms',
                'num' => '02',
                'name_en' => 'Dressing Rooms',
                'name_ar' => 'غرف الملابس',
                'name_it' => 'Cabine armadio',
                'description_en' => 'Italian dressing rooms are an art form. Our walk-in wardrobe systems are precision-designed with integrated lighting, custom interior organization, and flawless hardware — transforming your daily routine into an experience of quiet luxury.',
                'description_ar' => 'غرف الملابس الإيطالية فنٌّ بحدّ ذاته. أنظمة الخزائن الواسعة لدينا مصمَّمة بدقّة مع إضاءة مدمجة وتنظيم داخلي مخصَّص ومقابض خالية من العيوب — لتحوّل روتينك اليومي إلى تجربة فخامة هادئة.',
                'description_it' => "Le cabine armadio italiane sono una forma d'arte. I nostri sistemi walk-in sono progettati con precisione, con illuminazione integrata, organizzazione interna su misura e ferramenta impeccabile — per trasformare la tua routine quotidiana in un'esperienza di lusso silenzioso.",
                'features_en' => ['Walk-in wardrobe design', 'Hinged and sliding door systems', 'Custom interior organization', 'Integrated LED lighting', 'Mirror and glass options'],
                'features_ar' => ['تصميم خزائن ووك-إن', 'أنظمة أبواب مفصَّلة ومنزلقة', 'تنظيم داخلي مخصَّص', 'إضاءة LED مدمجة', 'خيارات المرايا والزجاج'],
                'features_it' => ['Progettazione walk-in', 'Ante battenti e scorrevoli', 'Organizzazione interna su misura', 'Illuminazione LED integrata', 'Opzioni con specchi e vetro'],
                'brand' => 'LUBE · FAER AMBIENTI',
                'image' => 'faer-dressing-room.jpg',
            ],
            [
                'slug' => 'laundry-rooms',
                'num' => '03',
                'name_en' => 'Laundry Rooms',
                'name_ar' => 'غرف الغسيل',
                'name_it' => 'Lavanderie',
                'description_en' => 'A well-designed laundry room should feel as refined as the rest of your home. Delos brings Italian-made cabinetry, premium finishes, and smart storage solutions to create laundry spaces that are both beautifully organized and effortlessly practical.',
                'description_ar' => 'غرفة الغسيل المصمَّمة جيّدًا يجب أن تشعر بالرقيّ ذاته الذي يشعر به باقي منزلك. تقدّم ديلوس خزائن إيطالية الصنع وتشطيبات فاخرة وحلول تخزين ذكية، لصياغة غرف غسيل منظّمة بجمال وعملية بيسر.',
                'description_it' => 'Una lavanderia ben progettata deve essere raffinata quanto il resto della casa. Delos propone mobili italiani, finiture premium e soluzioni di storage intelligenti per creare lavanderie organizzate con bellezza ed efficaci senza sforzo.',
                'features_en' => ['Custom cabinetry and storage', 'Integrated appliance housing', 'Premium worktops and sinks', 'Space-efficient layouts', 'Matching finishes throughout your home'],
                'features_ar' => ['خزائن وتخزين مخصّصة', 'حاويات للأجهزة مدمجة', 'أسطح وأحواض فاخرة', 'تخطيطات موفِّرة للمساحة', 'تشطيبات متناسقة مع باقي منزلك'],
                'features_it' => ['Mobili e storage su misura', 'Alloggiamento per elettrodomestici integrato', 'Piani di lavoro e lavelli premium', 'Layout salvaspazio', 'Finiture coordinate con il resto della casa'],
                'brand' => 'LUBE · Delos Custom',
                'image' => 'lube-laundry-zone.jpg',
            ],
            [
                'slug' => 'italian-furniture',
                'num' => '04',
                'name_en' => 'Italian Furniture',
                'name_ar' => 'الأثاث الإيطالي',
                'name_it' => 'Arredamento italiano',
                'description_en' => "From the living room to the bedroom, Delos delivers complete Italian furniture collections — sofas, dining suites, beds, and statement pieces that combine sculptural beauty with enduring comfort. Every piece is crafted by Italy's most celebrated furniture houses.",
                'description_ar' => 'من غرفة الجلوس إلى غرفة النوم، تقدّم ديلوس مجموعات أثاث إيطالية متكاملة — أرائك، أطقم طعام، أسرّة، وقطع مميّزة تجمع بين الجمال النحتي والراحة الدائمة. كلّ قطعة من صنع أعرق بيوت الأثاث في إيطاليا.',
                'description_it' => 'Dal soggiorno alla camera da letto, Delos offre collezioni complete di arredi italiani — divani, sale da pranzo, letti e pezzi iconici che uniscono bellezza scultorea e comfort duraturo. Ogni pezzo è realizzato dalle più celebri case di arredamento italiane.',
                'features_en' => ['Italian sofas and sectionals', 'Dining and coffee tables', 'Bedroom collections', 'Accent and statement pieces', 'Complete room styling'],
                'features_ar' => ['أرائك إيطالية وأطقم جلوس', 'طاولات طعام وقهوة', 'مجموعات غرف النوم', 'قطع مميّزة وبارزة', 'تنسيق كامل للغرف'],
                'features_it' => ['Divani e penisole italiani', 'Tavoli da pranzo e da caffè', 'Collezioni per camera da letto', "Pezzi d'accento e iconici", 'Styling completo degli ambienti'],
                'brand' => 'Frigerio · CANTORI',
                'image' => 'collection-vittoria.jpg',
            ],
            [
                'slug' => 'italian-parquet',
                'num' => '05',
                'name_en' => 'Italian Parquet',
                'name_ar' => 'الباركيه الإيطالي',
                'name_it' => 'Parquet italiano',
                'description_en' => "Flooring is the foundation of great design. SKEMA's premium Italian parquet and wooden flooring systems provide the perfect base for your Delos interior — rich natural wood grain, advanced engineering, and finishes that age beautifully for generations.",
                'description_ar' => 'الأرضية أساس كلّ تصميم عظيم. تقدّم أنظمة الباركيه والأرضيات الخشبية الإيطالية من SKEMA قاعدة مثالية لديكور ديلوس — حبيبات خشبية طبيعية غنيّة، وهندسة متقدّمة، وتشطيبات تتقادم بجمال عبر الأجيال.',
                'description_it' => 'Il pavimento è il fondamento di un grande progetto. I sistemi di parquet e pavimenti in legno premium di SKEMA offrono la base perfetta per il tuo interno Delos — venature naturali ricche, ingegneria avanzata e finiture che invecchiano con bellezza per generazioni.',
                'features_en' => ['Engineered Italian parquet', 'Natural hardwood options', 'Wide-plank and herringbone patterns', 'Prefinished and site-finished', 'Full installation service'],
                'features_ar' => ['باركيه إيطالي مهندَس', 'خيارات الخشب الطبيعي', 'ألواح عريضة وأنماط هيرنغبون', 'تشطيب جاهز ومُنجز في الموقع', 'خدمة تركيب متكاملة'],
                'features_it' => ['Parquet italiano ingegnerizzato', 'Opzioni in legno duro naturale', 'Doghe larghe e motivi a spina di pesce', 'Prefiniti e finiti in opera', 'Servizio di installazione completo'],
                'brand' => 'SKEMA',
                'image' => 'italian-materials.jpg',
            ],
            [
                'slug' => 'others',
                'num' => '06',
                'name_en' => 'Others',
                'name_ar' => 'خدمات أخرى',
                'name_it' => 'Altri servizi',
                'description_en' => "Beyond our core categories, Delos takes on anything you need to complete your space. From single bespoke pieces to complete home transformations, our team manages every element — consultation, 3D concept design, Italian sourcing, logistics, and flawless on-site installation — with the same precision as Italy's finest interior studios.",
                'description_ar' => 'إلى جانب خدماتنا الأساسية، تتولّى ديلوس كلّ ما تحتاجه لإكمال مساحتك. من قطعة واحدة مخصّصة إلى تحويل منزلي كامل، يدير فريقنا كلّ عنصر — من الاستشارة وتصميم الفكرة ثلاثية الأبعاد إلى الاستيراد من إيطاليا والخدمات اللوجستية والتركيب الدقيق في الموقع — بالدقّة ذاتها التي تتميّز بها أرقى الاستوديوهات الإيطالية.',
                'description_it' => "Oltre alle nostre categorie principali, Delos si occupa di tutto ciò che serve per completare il tuo spazio. Dal singolo pezzo su misura alla trasformazione completa della casa, il nostro team gestisce ogni aspetto — consulenza, concept design 3D, approvvigionamento italiano, logistica e un'installazione impeccabile in loco — con la stessa precisione dei migliori studi d'arredamento italiani.",
                'features_en' => ['On-site measurement and consultation', 'Personalized 3D design concepts', 'Italian sourcing and logistics', 'Professional installation team', 'Project management end-to-end'],
                'features_ar' => ['قياسات واستشارة في الموقع', 'تصاميم مفاهيم ثلاثية الأبعاد شخصية', 'استيراد وخدمات لوجستية من إيطاليا', 'فريق تركيب محترف', 'إدارة شاملة للمشروع من بدايته إلى نهايته'],
                'features_it' => ['Rilievi e consulenza in loco', 'Concept design 3D personalizzati', "Approvvigionamento e logistica dall'Italia", 'Team di installazione professionale', 'Project management completo'],
                'brand' => 'All Delos Partners',
                'image' => 'lube-project-mauritius.jpg',
            ],
        ];
    }
}
