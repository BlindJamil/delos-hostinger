<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\PageContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Iraqi-flavored Arabic copy refresh for the full Delos International site.
 *
 * Covers all ~300 editable page-content keys (home, about, services,
 * projects, brands, branches, contact, SEO, common/layout) plus the 7
 * brand rows' Arabic columns.
 *
 * Voice register: MSA backbone with selective Iraqi-dialect softening —
 * anchored by the user's own sample translations for luxury luxury
 * branding appropriate for an Iraqi audience. Quality is best-effort,
 * not native-quality; review via the admin preview page and tweak
 * individual fields in the page-content editor as needed.
 *
 * Idempotent: safe to re-run. Each updateOrCreate() overwrites with the
 * same canonical value. If an admin has manually edited a key after the
 * last seed run, re-running will revert that edit — surface this to the
 * admin in the UI before they click Apply.
 */
class IraqiArabicContentSeeder extends Seeder
{
    /**
     * Every editable page-content key → Iraqi-flavored Arabic value.
     * Values that should remain unchanged across locales (numeric stats,
     * suffixes, years, phone numbers, image filenames, brand names)
     * intentionally write the same string they already have so the
     * updateOrCreate row shape is complete.
     */
    public const PAGE_CONTENT_UPDATES = [

        // ═══════════════════════════════════════════════════════════
        //  HOME
        // ═══════════════════════════════════════════════════════════

        'home.hero.scroll_label' => ['page'=>'home','section'=>'hero','type'=>'text',
            'value_ar'=>'مرّر'],
        'home.hero.slides.0.alt' => ['page'=>'home','section'=>'hero','type'=>'text',
            'value_ar'=>'CANTORI للعيش بالهواء الطلق — تصميم شرفة فاخر'],
        'home.hero.slides.1.alt' => ['page'=>'home','section'=>'hero','type'=>'text',
            'value_ar'=>'مطبخ LUBE Agnese الكلاسيكي — فخامة إيطالية'],
        'home.hero.slides.2.alt' => ['page'=>'home','section'=>'hero','type'=>'text',
            'value_ar'=>'صالة Vittoria Frigerio الفاخرة — أناقة إيطالية'],
        'home.hero.slides.3.alt' => ['page'=>'home','section'=>'hero','type'=>'text',
            'value_ar'=>'مجموعة CANTORI للأثاث الإيطالي الفاخر'],
        'home.hero.slides.4.alt' => ['page'=>'home','section'=>'hero','type'=>'text',
            'value_ar'=>'تصميم مطبخ LUBE الإيطالي العصري'],

        'home.about.overline' => ['page'=>'home','section'=>'about','type'=>'text',
            'value_ar'=>'عن ديلوس · تأسّس 2020'],
        'home.about.heading_1' => ['page'=>'home','section'=>'about','type'=>'text',
            'value_ar'=>'هنا تلتقي الحرفة الإيطالية'],
        'home.about.heading_2' => ['page'=>'home','section'=>'about','type'=>'text',
            'value_ar'=>'وية'],
        'home.about.heading_accent' => ['page'=>'home','section'=>'about','type'=>'text',
            'value_ar'=>'بيوتنا العراقية.'],
        'home.about.body' => ['page'=>'home','section'=>'about','type'=>'textarea',
            'value_ar'=>'شركة ديلوس انترناشيونال هي شركة متميزة مقرها بالعراق، مختصة بتقديم أرقى التصاميم الإيطالية الفاخرة للبيوت بكل المنطقة. ديلوس تمثل مجموعة حصرية من الماركات الإيطالية الراقية، وتوفر تجربة حياة متكاملة عنوانها الأناقة، الإتقان، والدقة'],
        'home.about.quote' => ['page'=>'home','section'=>'about','type'=>'textarea',
            'value_ar'=>'اسم "ديلوس" مستوحى من جزيرة يونانية تعتبر رمز تاريخي للحضارة والحكمة. أما شعارنا (الأسد)، فهو يمثل القوة والحماية والثقة.'],

        'home.video.overline' => ['page'=>'home','section'=>'video','type'=>'text',
            'value_ar'=>'تعرّف على ديلوس'],
        'home.video.watch' => ['page'=>'home','section'=>'video','type'=>'text',
            'value_ar'=>'شوف قصّتنا'],

        'home.collection.overline' => ['page'=>'home','section'=>'collection','type'=>'text',
            'value_ar'=>'أبرز المعروضات'],
        'home.collection.heading' => ['page'=>'home','section'=>'collection','type'=>'text',
            'value_ar'=>'كولكشن جديد'],

        'home.collection.items.villa_classica.num' => ['page'=>'home','section'=>'collection','type'=>'text','value_ar'=>'01'],
        'home.collection.items.villa_classica.brand' => ['page'=>'home','section'=>'collection','type'=>'text','value_ar'=>'LUBE Kitchens'],
        'home.collection.items.villa_classica.title' => ['page'=>'home','section'=>'collection','type'=>'text',
            'value_ar'=>'مطبخ ڤيلّا كلاسيكا'],
        'home.collection.items.villa_classica.desc' => ['page'=>'home','section'=>'collection','type'=>'textarea',
            'value_ar'=>'تحفة من الحرفة الإيطالية، تجمع بين الأناقة الكلاسيكية وعملية الاستخدام اللي تناسب حياة اليوم.'],

        'home.collection.items.nero_contemporary.num' => ['page'=>'home','section'=>'collection','type'=>'text','value_ar'=>'02'],
        'home.collection.items.nero_contemporary.brand' => ['page'=>'home','section'=>'collection','type'=>'text','value_ar'=>'CREO Kitchens'],
        'home.collection.items.nero_contemporary.title' => ['page'=>'home','section'=>'collection','type'=>'text',
            'value_ar'=>'نيرو المعاصر'],
        'home.collection.items.nero_contemporary.desc' => ['page'=>'home','section'=>'collection','type'=>'textarea',
            'value_ar'=>'بساطة أنيقة تلتقي بالدقة الإيطالية بهاي المجموعة من المطابخ المعاصرة.'],

        'home.collection.items.eleganza_living.num' => ['page'=>'home','section'=>'collection','type'=>'text','value_ar'=>'03'],
        'home.collection.items.eleganza_living.brand' => ['page'=>'home','section'=>'collection','type'=>'text','value_ar'=>'Frigerio'],
        'home.collection.items.eleganza_living.title' => ['page'=>'home','section'=>'collection','type'=>'text',
            'value_ar'=>'أناقة المعيشة'],
        'home.collection.items.eleganza_living.desc' => ['page'=>'home','section'=>'collection','type'=>'textarea',
            'value_ar'=>'أثاث إيطالي راقي يعطي كل زاوية بالبيت لمسة أناقة ما تنتهي على مر السنين.'],

        'home.collection.items.mediterranean_living.num' => ['page'=>'home','section'=>'collection','type'=>'text','value_ar'=>'04'],
        'home.collection.items.mediterranean_living.brand' => ['page'=>'home','section'=>'collection','type'=>'text','value_ar'=>'CANTORI'],
        'home.collection.items.mediterranean_living.title' => ['page'=>'home','section'=>'collection','type'=>'text',
            'value_ar'=>'المعيشة المتوسّطية'],
        'home.collection.items.mediterranean_living.desc' => ['page'=>'home','section'=>'collection','type'=>'textarea',
            'value_ar'=>'أناقة خارجية خالدة، مستوحاة من دفء ساحل البحر المتوسّط.'],

        'home.employees.overline' => ['page'=>'home','section'=>'employees','type'=>'text',
            'value_ar'=>'أفضل موظفي السنة'],
        'home.employees.heading' => ['page'=>'home','section'=>'employees','type'=>'text',
            'value_ar'=>'فريقنا هو سرّ التميّز'],
        'home.employees.sub' => ['page'=>'home','section'=>'employees','type'=>'textarea',
            'value_ar'=>'تقدير لجهود المبدعين اللي يتفانون بشغلهم ورا كل مشروع متميز لشركة ديلوس خلال السنة'],

        'home.stats.overline' => ['page'=>'home','section'=>'stats','type'=>'text',
            'value_ar'=>'إنجازاتنا بالأرقام'],
        'home.stats.heading' => ['page'=>'home','section'=>'stats','type'=>'text',
            'value_ar'=>'إرث ديلوس بالأرقام'],
        'home.stats.items.0.value' => ['page'=>'home','section'=>'stats','type'=>'text','value_ar'=>'500'],
        'home.stats.items.0.suffix' => ['page'=>'home','section'=>'stats','type'=>'text','value_ar'=>'+'],
        'home.stats.items.0.label' => ['page'=>'home','section'=>'stats','type'=>'text',
            'value_ar'=>'مشاريع منجزة'],
        'home.stats.items.1.value' => ['page'=>'home','section'=>'stats','type'=>'text','value_ar'=>'5'],
        'home.stats.items.1.suffix' => ['page'=>'home','section'=>'stats','type'=>'text','value_ar'=>''],
        'home.stats.items.1.label' => ['page'=>'home','section'=>'stats','type'=>'text',
            'value_ar'=>'وكالات إيطالية حصرية'],
        'home.stats.items.2.value' => ['page'=>'home','section'=>'stats','type'=>'text','value_ar'=>'4'],
        'home.stats.items.2.suffix' => ['page'=>'home','section'=>'stats','type'=>'text','value_ar'=>''],
        'home.stats.items.2.label' => ['page'=>'home','section'=>'stats','type'=>'text',
            'value_ar'=>'فروعنا بالعراق'],
        'home.stats.items.3.value' => ['page'=>'home','section'=>'stats','type'=>'text','value_ar'=>'6'],
        'home.stats.items.3.suffix' => ['page'=>'home','section'=>'stats','type'=>'text','value_ar'=>''],
        'home.stats.items.3.label' => ['page'=>'home','section'=>'stats','type'=>'text',
            'value_ar'=>'سنين من الإبداع'],

        'home.brands.overline' => ['page'=>'home','section'=>'brands_intro','type'=>'text',
            'value_ar'=>'شراكات حصرية'],
        'home.brands.heading' => ['page'=>'home','section'=>'brands_intro','type'=>'text',
            'value_ar'=>'وكلاؤنا من الماركات الإيطالية'],
        'home.brands.sub' => ['page'=>'home','section'=>'brands_intro','type'=>'textarea',
            'value_ar'=>'ديلوس تتعاون مع كبرى الماركات الإيطالية الرائدة بمجالات المطابخ، الأثاث، الخزائن، والأرضيات. هدفنا هو نقل التصميم الإيطالي الأصلي، والخامات الفاخرة، والإتقان والحرفية العالية للسوق العراقي.'],

        'home.cta.overline' => ['page'=>'home','section'=>'cta','type'=>'text',
            'value_ar'=>'استلم مفتاح فخامة بيتك'],
        'home.cta.heading' => ['page'=>'home','section'=>'cta','type'=>'text',
            'value_ar'=>'ابدأ رحلة التميز مع التصاميم الإيطالية اليوم.'],

        // ═══════════════════════════════════════════════════════════
        //  ABOUT
        // ═══════════════════════════════════════════════════════════

        'about.hero.overline' => ['page'=>'about','section'=>'hero','type'=>'text',
            'value_ar'=>'تأسّست 2020 · أربيل، العراق'],
        'about.hero.heading_1' => ['page'=>'about','section'=>'hero','type'=>'text',
            'value_ar'=>'نجيب الفخامة'],
        'about.hero.heading_2' => ['page'=>'about','section'=>'hero','type'=>'text',
            'value_ar'=>'الإيطالية لكل'],
        'about.hero.heading_accent' => ['page'=>'about','section'=>'hero','type'=>'text',
            'value_ar'=>'بيت عراقي.'],
        'about.hero.sub' => ['page'=>'about','section'=>'hero','type'=>'textarea',
            'value_ar'=>'الوجهة العراقية الموثوقة بالتصاميم الداخلية الإيطالية الأصيلة — من الفكرة حتى الإنجاز الكامل.'],
        'about.hero.stats.0.value' => ['page'=>'about','section'=>'hero','type'=>'text','value_ar'=>'500+'],
        'about.hero.stats.0.label' => ['page'=>'about','section'=>'hero','type'=>'text','value_ar'=>'مشاريع منجزة'],
        'about.hero.stats.1.value' => ['page'=>'about','section'=>'hero','type'=>'text','value_ar'=>'4'],
        'about.hero.stats.1.label' => ['page'=>'about','section'=>'hero','type'=>'text','value_ar'=>'فروعنا بالعراق'],
        'about.hero.stats.2.value' => ['page'=>'about','section'=>'hero','type'=>'text','value_ar'=>'5'],
        'about.hero.stats.2.label' => ['page'=>'about','section'=>'hero','type'=>'text','value_ar'=>'وكلاء إيطاليون'],

        'about.story.year_badge' => ['page'=>'about','section'=>'story','type'=>'text','value_ar'=>'2020'],
        'about.story.year_label' => ['page'=>'about','section'=>'story','type'=>'text',
            'value_ar'=>'تأسّست بأربيل'],
        'about.story.overline' => ['page'=>'about','section'=>'story','type'=>'text','value_ar'=>'قصّتنا'],
        'about.story.heading_1' => ['page'=>'about','section'=>'story','type'=>'text',
            'value_ar'=>'من هنا انطلق'],
        'about.story.heading_2' => ['page'=>'about','section'=>'story','type'=>'text',
            'value_ar'=>'شغف الحرفة الإيطالية'],
        'about.story.heading_accent' => ['page'=>'about','section'=>'story','type'=>'text',
            'value_ar'=>'وصار حقيقة.'],
        'about.story.paragraph_1' => ['page'=>'about','section'=>'story','type'=>'textarea',
            'value_ar'=>'قصّتنا ابتدت سنة 2020 بأربيل، لمن رؤية تقديم التصاميم الإيطالية الفاخرة والأصيلة للبيوت العراقية صارت حقيقة على الأرض. اللي بدأ بمعرض واحد بشارع الستين متر، اليوم صار اسمه ديلوس إنترناشيونال — الاسم الأكثر ثقة بحلول التصميم الداخلي الإيطالي الفاخر بالعراق.'],
        'about.story.paragraph_2' => ['page'=>'about','section'=>'story','type'=>'textarea',
            'value_ar'=>'من أول يوم، ركّزنا على اللي يهم فعلاً: حرفية إيطالية أصيلة، خامات فاخرة من مصادر موثوقة، وخدمة متكاملة تصحب عملاءنا من الاستشارة الأولى لحد التركيب النهائي — بدون أي مجال لأي صدفة.'],
        'about.story.paragraph_3' => ['page'=>'about','section'=>'story','type'=>'textarea',
            'value_ar'=>'اليوم، وبأربع فروع بأربيل وكركوك والسليمانية وبغداد، وشراكات حصرية وية أعرق الماركات الإيطالية، ديلوس إنترناشيونال صارت الوجهة الأولى للعراق بعالم الحياة الإيطالية الفاخرة.'],

        'about.direction.overline' => ['page'=>'about','section'=>'direction','type'=>'text','value_ar'=>'مسارنا'],
        'about.direction.heading_1' => ['page'=>'about','section'=>'direction','type'=>'text',
            'value_ar'=>'الرؤية والرسالة'],
        'about.direction.heading_2' => ['page'=>'about','section'=>'direction','type'=>'text','value_ar'=>'والهدف'],
        'about.direction.items.vision.label' => ['page'=>'about','section'=>'direction','type'=>'text','value_ar'=>'الرؤية'],
        'about.direction.items.vision.title' => ['page'=>'about','section'=>'direction','type'=>'text',
            'value_ar'=>'الريادة بالسوق العراقي'],
        'about.direction.items.vision.body' => ['page'=>'about','section'=>'direction','type'=>'textarea',
            'value_ar'=>'هدفنا نصير القائد اللي ما يتنازَع عليه بمجال المطابخ والأثاث بكل أنحاء العراق — حيث كل بيت عراقي يعرف اسم ديلوس ويثق بيه.'],
        'about.direction.items.mission.label' => ['page'=>'about','section'=>'direction','type'=>'text','value_ar'=>'الرسالة'],
        'about.direction.items.mission.title' => ['page'=>'about','section'=>'direction','type'=>'text',
            'value_ar'=>'الثقة والتميّز'],
        'about.direction.items.mission.body' => ['page'=>'about','section'=>'direction','type'=>'textarea',
            'value_ar'=>'كل منتج من ديلوس نبنيه على الثقة والتميّز — بتقديم خامات إيطالية عالية الجودة من مصادر موثوقة، وبدقة تصنيع ما تقبل التنازل.'],
        'about.direction.items.goal.label' => ['page'=>'about','section'=>'direction','type'=>'text','value_ar'=>'الهدف'],
        'about.direction.items.goal.title' => ['page'=>'about','section'=>'direction','type'=>'text',
            'value_ar'=>'مصمَّم الكم'],
        'about.direction.items.goal.body' => ['page'=>'about','section'=>'direction','type'=>'textarea',
            'value_ar'=>'نقدّم حلول مخصّصة ومُهندَسة بدقة تلبّي احتياجات كل عميل — لنحوّل كل مساحة لتعبير حيّ عن الفخامة الإيطالية.'],

        'about.philosophy.overline' => ['page'=>'about','section'=>'philosophy','type'=>'text','value_ar'=>'فلسفتنا'],
        'about.philosophy.heading_1' => ['page'=>'about','section'=>'philosophy','type'=>'text',
            'value_ar'=>'كل مشروع'],
        'about.philosophy.heading_2' => ['page'=>'about','section'=>'philosophy','type'=>'text',
            'value_ar'=>'يبدأ من'],
        'about.philosophy.heading_accent' => ['page'=>'about','section'=>'philosophy','type'=>'text',
            'value_ar'=>'فكرة وحدة.'],
        'about.philosophy.paragraph_1' => ['page'=>'about','section'=>'philosophy','type'=>'textarea',
            'value_ar'=>'كل مشروع بديلوس يبدأ من فكرة وحدة: نصنع مساحة تبيّن مذهلة، وتخليك ترتاح تمامًا، وتأدّي وظيفتها بسلاسة بحياتك اليومية. إحنا نهتم كلش باختيار الخامات، والتصميم المكاني، والدقة الوظيفية — حتى نضمن إنو كل تنفيذ يرتقي بأسلوب حياتك.'],
        'about.philosophy.paragraph_2' => ['page'=>'about','section'=>'philosophy','type'=>'textarea',
            'value_ar'=>'فلسفتنا تمزج بين جماليات الفخامة الإيطالية ومتطلّبات الحياة العراقية العصرية. مساحتك لازم تلهمك كل يوم، وتحتضن عائلتك، وتدوم لأجيال — مو مجرد زينة.'],

        'about.materials.overline' => ['page'=>'about','section'=>'materials','type'=>'text',
            'value_ar'=>'من مصادر إيطالية'],
        'about.materials.heading_1' => ['page'=>'about','section'=>'materials','type'=>'text',
            'value_ar'=>'خامات اختاريناها'],
        'about.materials.heading_2' => ['page'=>'about','section'=>'materials','type'=>'text','value_ar'=>'لأجل'],
        'about.materials.heading_accent_1' => ['page'=>'about','section'=>'materials','type'=>'text','value_ar'=>'قوّتها'],
        'about.materials.heading_accent_2' => ['page'=>'about','section'=>'materials','type'=>'text','value_ar'=>'وشخصيّتها،'],
        'about.materials.heading_3' => ['page'=>'about','section'=>'materials','type'=>'text','value_ar'=>'ولأجل'],
        'about.materials.heading_4' => ['page'=>'about','section'=>'materials','type'=>'text','value_ar'=>'دفئها الطبيعي.'],
        'about.materials.body' => ['page'=>'about','section'=>'materials','type'=>'textarea',
            'value_ar'=>'كل إبداعات ديلوس تبدأ من جمال إيطاليا الهادئ. إحنا نختار بعناية أفخر أنواع الخشب والرخام والأقمشة — مو بس لجمالها، وإنما لقوتها وروحها.'],

        'about.pillars.overline' => ['page'=>'about','section'=>'pillars','type'=>'text','value_ar'=>'اللي يميّزنا'],
        'about.pillars.heading' => ['page'=>'about','section'=>'pillars','type'=>'text',
            'value_ar'=>'الفرق وية ديلوس'],
        'about.pillars.items.authentic.num' => ['page'=>'about','section'=>'pillars','type'=>'text','value_ar'=>'01'],
        'about.pillars.items.authentic.title' => ['page'=>'about','section'=>'pillars','type'=>'text',
            'value_ar'=>'تصميم إيطالي أصيل'],
        'about.pillars.items.authentic.desc' => ['page'=>'about','section'=>'pillars','type'=>'textarea',
            'value_ar'=>'من أفضل المصنّعين بإيطاليا حصرياً — بدون تقليد وبدون تنازل.'],
        'about.pillars.items.turnkey.num' => ['page'=>'about','section'=>'pillars','type'=>'text','value_ar'=>'02'],
        'about.pillars.items.turnkey.title' => ['page'=>'about','section'=>'pillars','type'=>'text',
            'value_ar'=>'تنفيذ مشاريع متكامل'],
        'about.pillars.items.turnkey.desc' => ['page'=>'about','section'=>'pillars','type'=>'textarea',
            'value_ar'=>'الفكرة، والتصميم ثلاثي الأبعاد، والتسليم، والتركيب الكامل — فريق واحد، وصفر قلق.'],
        'about.pillars.items.premium.num' => ['page'=>'about','section'=>'pillars','type'=>'text','value_ar'=>'03'],
        'about.pillars.items.premium.title' => ['page'=>'about','section'=>'pillars','type'=>'text',
            'value_ar'=>'حرفية فاخرة'],
        'about.pillars.items.premium.desc' => ['page'=>'about','section'=>'pillars','type'=>'textarea',
            'value_ar'=>'تصنيع إيطالي متخصّص، تفاصيل راقية، وخامات تدوم على مر الزمن.'],
        'about.pillars.items.trusted.num' => ['page'=>'about','section'=>'pillars','type'=>'text','value_ar'=>'04'],
        'about.pillars.items.trusted.title' => ['page'=>'about','section'=>'pillars','type'=>'text',
            'value_ar'=>'موثوقة من 2020'],
        'about.pillars.items.trusted.desc' => ['page'=>'about','section'=>'pillars','type'=>'textarea',
            'value_ar'=>'أربع فروع، وأكثر من 500 مشروع، وآلاف العوائل العراقية اللي اختارت تثق بالأسد.'],

        'about.quote.overline' => ['page'=>'about','section'=>'quote','type'=>'text','value_ar'=>'التزامنا'],
        'about.quote.text_before_accent' => ['page'=>'about','section'=>'quote','type'=>'text',
            'value_ar'=>'"فخامة إيطالية'],
        'about.quote.text_accent' => ['page'=>'about','section'=>'quote','type'=>'text',
            'value_ar'=>'تعيش وياك'],
        'about.quote.text_after_accent' => ['page'=>'about','section'=>'quote','type'=>'textarea',
            'value_ar'=>'— تصميم يتكيّف، أثاث يُلهم، ومساحات تحكي قصّتك."'],
        'about.quote.signature' => ['page'=>'about','section'=>'quote','type'=>'text',
            'value_ar'=>'DELOS — ثق بالأسد'],

        'about.about_stats.items.0.value' => ['page'=>'about','section'=>'about_stats','type'=>'text','value_ar'=>'500'],
        'about.about_stats.items.0.suffix' => ['page'=>'about','section'=>'about_stats','type'=>'text','value_ar'=>'+'],
        'about.about_stats.items.0.label' => ['page'=>'about','section'=>'about_stats','type'=>'text',
            'value_ar'=>'مشاريع مُسلّمة'],
        'about.about_stats.items.1.value' => ['page'=>'about','section'=>'about_stats','type'=>'text','value_ar'=>'4'],
        'about.about_stats.items.1.suffix' => ['page'=>'about','section'=>'about_stats','type'=>'text','value_ar'=>''],
        'about.about_stats.items.1.label' => ['page'=>'about','section'=>'about_stats','type'=>'text',
            'value_ar'=>'فروع بالعراق'],
        'about.about_stats.items.2.value' => ['page'=>'about','section'=>'about_stats','type'=>'text','value_ar'=>'5'],
        'about.about_stats.items.2.suffix' => ['page'=>'about','section'=>'about_stats','type'=>'text','value_ar'=>''],
        'about.about_stats.items.2.label' => ['page'=>'about','section'=>'about_stats','type'=>'text',
            'value_ar'=>'وكلاء إيطاليون'],
        'about.about_stats.items.3.value' => ['page'=>'about','section'=>'about_stats','type'=>'text','value_ar'=>'2020'],
        'about.about_stats.items.3.suffix' => ['page'=>'about','section'=>'about_stats','type'=>'text','value_ar'=>''],
        'about.about_stats.items.3.label' => ['page'=>'about','section'=>'about_stats','type'=>'text',
            'value_ar'=>'تأسّست بأربيل'],

        'about.workflow.overline' => ['page'=>'about','section'=>'workflow','type'=>'text','value_ar'=>'شلون نشتغل'],
        'about.workflow.heading_1' => ['page'=>'about','section'=>'workflow','type'=>'text','value_ar'=>'من الفكرة'],
        'about.workflow.heading_2' => ['page'=>'about','section'=>'workflow','type'=>'text','value_ar'=>'لحد الإنجاز.'],
        'about.workflow.steps.consultation.step' => ['page'=>'about','section'=>'workflow','type'=>'text','value_ar'=>'01'],
        'about.workflow.steps.consultation.title' => ['page'=>'about','section'=>'workflow','type'=>'text',
            'value_ar'=>'الاستشارة'],
        'about.workflow.steps.consultation.desc' => ['page'=>'about','section'=>'workflow','type'=>'textarea',
            'value_ar'=>'خبراؤنا يزوروا موقعك، يفهمون رؤيتك، ويسوون القياسات الدقيقة.'],
        'about.workflow.steps.design.step' => ['page'=>'about','section'=>'workflow','type'=>'text','value_ar'=>'02'],
        'about.workflow.steps.design.title' => ['page'=>'about','section'=>'workflow','type'=>'text',
            'value_ar'=>'التصميم ثلاثي الأبعاد'],
        'about.workflow.steps.design.desc' => ['page'=>'about','section'=>'workflow','type'=>'textarea',
            'value_ar'=>'مصمّمونا يحضّرون تصوّر ثلاثي الأبعاد مخصَّص الكم، يتوافق تمامًا وية نمط حياتك ومساحتك.'],
        'about.workflow.steps.sourcing.step' => ['page'=>'about','section'=>'workflow','type'=>'text','value_ar'=>'03'],
        'about.workflow.steps.sourcing.title' => ['page'=>'about','section'=>'workflow','type'=>'text',
            'value_ar'=>'الاستيراد من إيطاليا'],
        'about.workflow.steps.sourcing.desc' => ['page'=>'about','section'=>'workflow','type'=>'textarea',
            'value_ar'=>'اختيارك يوصل مباشرة من أفضل المصنّعين بإيطاليا وية ضمانات جودة كاملة.'],
        'about.workflow.steps.installation.step' => ['page'=>'about','section'=>'workflow','type'=>'text','value_ar'=>'04'],
        'about.workflow.steps.installation.title' => ['page'=>'about','section'=>'workflow','type'=>'text','value_ar'=>'التركيب'],
        'about.workflow.steps.installation.desc' => ['page'=>'about','section'=>'workflow','type'=>'textarea',
            'value_ar'=>'فنيّونا الخبراء ينقلون ويركّبون بدقة متناهية — حتى نضمن نتائج استثنائية.'],

        'about.branches.overline' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'حضورنا بالعراق'],
        'about.branches.heading_1' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'أربع مدن.'],
        'about.branches.heading_2' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'معيار واحد'],
        'about.branches.heading_3' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'من'],
        'about.branches.heading_accent' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'التميّز.'],
        'about.branches.items.erbil.city' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'أربيل'],
        'about.branches.items.erbil.year' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'2020'],
        'about.branches.items.erbil.address' => ['page'=>'about','section'=>'branches','type'=>'text',
            'value_ar'=>'شارع الستين متر، يم مستشفى سوران'],
        'about.branches.items.erbil.phone' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'0750 100 1701'],
        'about.branches.items.kirkuk.city' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'كركوك'],
        'about.branches.items.kirkuk.year' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'2021'],
        'about.branches.items.kirkuk.address' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'معرض كركوك'],
        'about.branches.items.kirkuk.phone' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'للتواصل يرجى الاتصال'],
        'about.branches.items.sulaymaniyah.city' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'السليمانية'],
        'about.branches.items.sulaymaniyah.year' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'2022'],
        'about.branches.items.sulaymaniyah.address' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'معرض السليمانية'],
        'about.branches.items.sulaymaniyah.phone' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'للتواصل يرجى الاتصال'],
        'about.branches.items.baghdad.city' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'بغداد'],
        'about.branches.items.baghdad.year' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'2024'],
        'about.branches.items.baghdad.address' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'معرض بغداد'],
        'about.branches.items.baghdad.phone' => ['page'=>'about','section'=>'branches','type'=>'text','value_ar'=>'للتواصل يرجى الاتصال'],

        // ═══════════════════════════════════════════════════════════
        //  SERVICES
        // ═══════════════════════════════════════════════════════════

        'services.hero.overline' => ['page'=>'services','section'=>'hero','type'=>'text','value_ar'=>'اللي نقدّمه'],
        'services.hero.heading_1_before' => ['page'=>'services','section'=>'hero','type'=>'text',
            'value_ar'=>'فخامة إيطالية،'],
        'services.hero.heading_1_accent' => ['page'=>'services','section'=>'hero','type'=>'text',
            'value_ar'=>'مصاغة'],
        'services.hero.heading_2' => ['page'=>'services','section'=>'hero','type'=>'text',
            'value_ar'=>'لكل غرفة.'],

        'services.intro.overline' => ['page'=>'services','section'=>'intro','type'=>'text','value_ar'=>'اللي نقدّمه'],
        'services.intro.heading_1' => ['page'=>'services','section'=>'intro','type'=>'text',
            'value_ar'=>'ست حرف إيطالية.'],
        'services.intro.heading_accent' => ['page'=>'services','section'=>'intro','type'=>'text',
            'value_ar'=>'معيار واحد من الفخامة.'],

        'services.item_cta' => ['page'=>'services','section'=>'items','type'=>'text','value_ar'=>'احجز استشارة'],

        'services.cta.overline' => ['page'=>'services','section'=>'cta','type'=>'text','value_ar'=>'ابدأ اليوم'],
        'services.cta.heading_1' => ['page'=>'services','section'=>'cta','type'=>'text',
            'value_ar'=>'جاهز تحوّل'],
        'services.cta.heading_accent' => ['page'=>'services','section'=>'cta','type'=>'text','value_ar'=>'مساحتك؟'],
        'services.cta.button' => ['page'=>'services','section'=>'cta','type'=>'text',
            'value_ar'=>'احجز استشارة مجّانية'],

        // ═══════════════════════════════════════════════════════════
        //  PROJECTS
        // ═══════════════════════════════════════════════════════════

        'projects.hero.counter' => ['page'=>'projects','section'=>'hero','type'=>'text',
            'value_ar'=>'أكثر من 500 مشروع منجز'],
        'projects.filters.all' => ['page'=>'projects','section'=>'filters','type'=>'text','value_ar'=>'الكل'],

        'projects.stats.0.value' => ['page'=>'projects','section'=>'stats','type'=>'text','value_ar'=>'500+'],
        'projects.stats.0.label' => ['page'=>'projects','section'=>'stats','type'=>'text',
            'value_ar'=>'مشاريع منجزة'],
        'projects.stats.1.value' => ['page'=>'projects','section'=>'stats','type'=>'text','value_ar'=>'4'],
        'projects.stats.1.label' => ['page'=>'projects','section'=>'stats','type'=>'text',
            'value_ar'=>'مدن بالعراق'],
        'projects.stats.2.value' => ['page'=>'projects','section'=>'stats','type'=>'text','value_ar'=>'100%'],
        'projects.stats.2.label' => ['page'=>'projects','section'=>'stats','type'=>'text',
            'value_ar'=>'من إيطاليا'],
        'projects.stats.3.value' => ['page'=>'projects','section'=>'stats','type'=>'text','value_ar'=>'5'],
        'projects.stats.3.label' => ['page'=>'projects','section'=>'stats','type'=>'text',
            'value_ar'=>'وكلاء تجاريون'],

        'projects.cta.heading_1' => ['page'=>'projects','section'=>'cta','type'=>'text',
            'value_ar'=>'مشروعك ممكن يصير'],
        'projects.cta.heading_accent' => ['page'=>'projects','section'=>'cta','type'=>'text',
            'value_ar'=>'تحفتنا الجاية.'],

        // ═══════════════════════════════════════════════════════════
        //  BRANDS
        // ═══════════════════════════════════════════════════════════

        'brands.hero.overline' => ['page'=>'brands','section'=>'hero','type'=>'text',
            'value_ar'=>'وكلاؤنا الإيطاليين'],
        'brands.hero.heading_1' => ['page'=>'brands','section'=>'hero','type'=>'text',
            'value_ar'=>'خمس دور تصميم..'],
        'brands.hero.heading_2' => ['page'=>'brands','section'=>'hero','type'=>'text',
            'value_ar'=>'ومعيار واحد'],
        'brands.hero.heading_accent' => ['page'=>'brands','section'=>'hero','type'=>'text',
            'value_ar'=>'للتميّز الإيطالي.'],
        'brands.hero.sub' => ['page'=>'brands','section'=>'hero','type'=>'textarea',
            'value_ar'=>'ديلوس هي الوكيل الحصري لأرقى وأعرق الماركات الإيطالية المختصة بالفخامة'],

        'brands.intro.overline' => ['page'=>'brands','section'=>'intro','type'=>'text',
            'value_ar'=>'وكالاتنا الحصرية'],
        'brands.intro.heading_1' => ['page'=>'brands','section'=>'intro','type'=>'text',
            'value_ar'=>'ما نختار غير الأفضل من إيطاليا.'],
        'brands.intro.heading_2' => ['page'=>'brands','section'=>'intro','type'=>'text','value_ar'=>''],
        'brands.intro.heading_accent' => ['page'=>'brands','section'=>'intro','type'=>'text','value_ar'=>''],
        'brands.intro.body' => ['page'=>'brands','section'=>'intro','type'=>'textarea',
            'value_ar'=>'ديلوس إنترناشيونال هي الوكيل الحصري لـ ٧ من أرقى وأعرق دور التصميم الداخلي في إيطاليا. كل براند اختاريناه بعناية بناءً على تاريخه، دقة شغله، وتماشيه وية معايير الفخامة اللي ما نقبل نساوم عليها. ومن خلال هاي الشراكات الاستراتيجية، نوفر التصميم الإيطالي الأصلي، أجود الخامات، وأعلى مستويات الحرفية العالمية للبيوت العراقية مباشرةً.'],
        'brands.intro.quote' => ['page'=>'brands','section'=>'intro','type'=>'text',
            'value_ar'=>'روح الفخامة الإيطالية.. بقلب بيوتنا العراقية.'],

        'brands.cta.heading_1' => ['page'=>'brands','section'=>'cta','type'=>'text',
            'value_ar'=>'اختبر الفخامة الإيطالية'],
        'brands.cta.heading_accent' => ['page'=>'brands','section'=>'cta','type'=>'text',
            'value_ar'=>'عن قرب.'],

        // ═══════════════════════════════════════════════════════════
        //  BRANCHES
        // ═══════════════════════════════════════════════════════════

        'branches.hero.overline' => ['page'=>'branches','section'=>'hero','type'=>'text',
            'value_ar'=>'حضورنا بالعراق'],
        'branches.hero.heading_1' => ['page'=>'branches','section'=>'hero','type'=>'text',
            'value_ar'=>'أربع مدن.'],
        'branches.hero.heading_accent' => ['page'=>'branches','section'=>'hero','type'=>'text',
            'value_ar'=>'معيار واحد من التميّز.'],
        'branches.directions_cta' => ['page'=>'branches','section'=>'hero','type'=>'text',
            'value_ar'=>'شوف الاتجاهات'],
        'branches.whatsapp_cta' => ['page'=>'branches','section'=>'hero','type'=>'text',
            'value_ar'=>'واتساب'],
        'branches.photo_pending' => ['page'=>'branches','section'=>'hero','type'=>'text',
            'value_ar'=>'صور المعرض جاية قريباً'],

        // ═══════════════════════════════════════════════════════════
        //  CONTACT
        // ═══════════════════════════════════════════════════════════

        'contact.hero.overline' => ['page'=>'contact','section'=>'hero','type'=>'text','value_ar'=>'تواصل معنا'],
        'contact.hero.heading_1' => ['page'=>'contact','section'=>'hero','type'=>'text',
            'value_ar'=>'تواصل معنا.'],
        'contact.hero.heading_accent' => ['page'=>'contact','section'=>'hero','type'=>'text',
            'value_ar'=>'إحنا موجودين الكم.'],

        'contact.intro.overline' => ['page'=>'contact','section'=>'intro','type'=>'text','value_ar'=>'احجز استشارة'],
        'contact.intro.heading_1' => ['page'=>'contact','section'=>'intro','type'=>'text',
            'value_ar'=>'ابدأ رحلة'],
        'contact.intro.heading_accent' => ['page'=>'contact','section'=>'intro','type'=>'text',
            'value_ar'=>'الفخامة الإيطالية'],
        'contact.intro.heading_2' => ['page'=>'contact','section'=>'intro','type'=>'text','value_ar'=>'مالتك.'],
        'contact.intro.body' => ['page'=>'contact','section'=>'intro','type'=>'textarea',
            'value_ar'=>'خبيرنا راح يتواصل وياك خلال 24 ساعة، حتى يرتّب الك استشارة منزلية مجّانية وتصميم مفهوم ثلاثي الأبعاد.'],

        'contact.form.method_legend' => ['page'=>'contact','section'=>'form','type'=>'text',
            'value_ar'=>'كيف تفضل التواصل معنا؟'],
        'contact.form.method_whatsapp' => ['page'=>'contact','section'=>'form','type'=>'text','value_ar'=>'واتساب'],
        'contact.form.method_email' => ['page'=>'contact','section'=>'form','type'=>'text','value_ar'=>'البريد الإلكتروني'],
        'contact.form.name' => ['page'=>'contact','section'=>'form','type'=>'text','value_ar'=>'الاسم الكامل'],
        'contact.form.name_placeholder' => ['page'=>'contact','section'=>'form','type'=>'text','value_ar'=>'اسمك الكامل'],
        'contact.form.email' => ['page'=>'contact','section'=>'form','type'=>'text','value_ar'=>'بريدك الإلكتروني'],
        'contact.form.email_placeholder' => ['page'=>'contact','section'=>'form','type'=>'text','value_ar'=>'you@example.com'],
        'contact.form.phone' => ['page'=>'contact','section'=>'form','type'=>'text','value_ar'=>'رقم هاتفك'],
        'contact.form.phone_placeholder' => ['page'=>'contact','section'=>'form','type'=>'text','value_ar'=>'+964 750 000 0000'],
        'contact.form.branch' => ['page'=>'contact','section'=>'form','type'=>'text','value_ar'=>'اختار الفرع'],
        'contact.form.branch_placeholder' => ['page'=>'contact','section'=>'form','type'=>'text',
            'value_ar'=>'اختار أقرب فرع الك'],
        'contact.form.service' => ['page'=>'contact','section'=>'form','type'=>'text',
            'value_ar'=>'بشو مهتم؟'],
        'contact.form.service_placeholder' => ['page'=>'contact','section'=>'form','type'=>'text',
            'value_ar'=>'اختياري — شنو الشي اللي تفكّر تصمّمه'],
        'contact.form.service_options.0' => ['page'=>'contact','section'=>'form','type'=>'text','value_ar'=>'مطبخ فاخر'],
        'contact.form.service_options.1' => ['page'=>'contact','section'=>'form','type'=>'text','value_ar'=>'غرفة جلوس'],
        'contact.form.service_options.2' => ['page'=>'contact','section'=>'form','type'=>'text','value_ar'=>'غرفة نوم'],
        'contact.form.service_options.3' => ['page'=>'contact','section'=>'form','type'=>'text','value_ar'=>'خزائن'],
        'contact.form.service_options.4' => ['page'=>'contact','section'=>'form','type'=>'text','value_ar'=>'أرضيات وجدران'],
        'contact.form.service_options.5' => ['page'=>'contact','section'=>'form','type'=>'text','value_ar'=>'مشروع متكامل'],
        'contact.form.message' => ['page'=>'contact','section'=>'form','type'=>'text','value_ar'=>'رسالتك'],
        'contact.form.message_placeholder' => ['page'=>'contact','section'=>'form','type'=>'text',
            'value_ar'=>'احكيلنا شوية عن مشروعك...'],
        'contact.form.submit_whatsapp' => ['page'=>'contact','section'=>'form','type'=>'text',
            'value_ar'=>'افتح واتساب'],
        'contact.form.submit_email' => ['page'=>'contact','section'=>'form','type'=>'text',
            'value_ar'=>'إرسال عبر البريد'],
        'contact.form.branch_no_whatsapp' => ['page'=>'contact','section'=>'form','type'=>'textarea',
            'value_ar'=>'هاذ الفرع ما انضاف إله رقم واتساب بعد — يرجى اختيار البريد الإلكتروني أو فرع ثاني.'],
        'contact.form.branch_no_email' => ['page'=>'contact','section'=>'form','type'=>'textarea',
            'value_ar'=>'هاذ الفرع ما انضاف إله بريد إلكتروني بعد — يرجى اختيار واتساب أو فرع ثاني.'],

        // ═══════════════════════════════════════════════════════════
        //  SEO — kept formal MSA for searchability; minor warming only
        // ═══════════════════════════════════════════════════════════

        'seo.default.title' => ['page'=>'seo','section'=>'default','type'=>'text',
            'value_ar'=>'ديلوس إنترناشيونال — حلول الفخامة الإيطالية'],
        'seo.default.description' => ['page'=>'seo','section'=>'default','type'=>'textarea',
            'value_ar'=>'ديلوس إنترناشيونال تقدّم أرقى التصاميم الداخلية الإيطالية الفاخرة بالعراق: مطابخ، أثاث، خزائن، وحلول متكاملة بأربيل وكركوك والسليمانية وبغداد.'],
        'seo.home.title' => ['page'=>'seo','section'=>'home','type'=>'text',
            'value_ar'=>'ديلوس إنترناشيونال — حلول الفخامة الإيطالية بالعراق'],
        'seo.home.description' => ['page'=>'seo','section'=>'home','type'=>'textarea',
            'value_ar'=>'ديلوس إنترناشيونال تقدّم أرقى التصاميم الداخلية الإيطالية الفاخرة بالعراق: مطابخ، أثاث، خزائن، وحلول متكاملة بأربيل وكركوك والسليمانية وبغداد.'],
        'seo.about.title' => ['page'=>'seo','section'=>'about','type'=>'text',
            'value_ar'=>'عن ديلوس إنترناشيونال — الفخامة الإيطالية للتصميم الداخلي بالعراق'],
        'seo.about.description' => ['page'=>'seo','section'=>'about','type'=>'textarea',
            'value_ar'=>'تأسّست ديلوس إنترناشيونال بأربيل سنة 2020 حتى تقدّم الفخامة الإيطالية الأصيلة لبيوت العراق. أربع فروع، خمس وكلاء إيطاليين، وأكثر من 500 مشروع منجز.'],
        'seo.services.title' => ['page'=>'seo','section'=>'services','type'=>'text',
            'value_ar'=>'خدماتنا — ديلوس إنترناشيونال للفخامة الإيطالية بالعراق'],
        'seo.services.description' => ['page'=>'seo','section'=>'services','type'=>'textarea',
            'value_ar'=>'ديلوس إنترناشيونال تقدّم المطابخ الإيطالية، غرف الملابس، غرف الغسيل، الأثاث، الأرضيات، والحلول الداخلية المتكاملة بكل أنحاء العراق.'],
        'seo.brands.title' => ['page'=>'seo','section'=>'brands','type'=>'text',
            'value_ar'=>'الوكلاء الإيطاليون — ديلوس إنترناشيونال العراق'],
        'seo.brands.description' => ['page'=>'seo','section'=>'brands','type'=>'textarea',
            'value_ar'=>'ديلوس إنترناشيونال وكيلنا الحصري لأرقى الماركات الإيطالية: LUBE، Frigerio، Vittoria Frigerio، CANTORI، وSKEMA. جودة إيطالية أصيلة بالعراق.'],
        'seo.projects.title' => ['page'=>'seo','section'=>'projects','type'=>'text',
            'value_ar'=>'المشاريع — ديلوس إنترناشيونال للتصميم الداخلي الفاخر بالعراق'],
        'seo.projects.description' => ['page'=>'seo','section'=>'projects','type'=>'textarea',
            'value_ar'=>'شوف مشاريع ديلوس إنترناشيونال المنجزة للتصميم الداخلي الفاخر بأربيل وكركوك والسليمانية وبغداد. فخامة إيطالية ببيوت عراقية.'],
        'seo.branches.title' => ['page'=>'seo','section'=>'branches','type'=>'text',
            'value_ar'=>'فروعنا — معارض ديلوس إنترناشيونال بأنحاء العراق'],
        'seo.branches.description' => ['page'=>'seo','section'=>'branches','type'=>'textarea',
            'value_ar'=>'زوروا معارض ديلوس إنترناشيونال بأربيل وكركوك والسليمانية وبغداد، واختبروا الفخامة الإيطالية للتصميم الداخلي على الطبيعة.'],
        'seo.contact.title' => ['page'=>'seo','section'=>'contact','type'=>'text',
            'value_ar'=>'تواصل معنا — ديلوس إنترناشيونال'],
        'seo.contact.description' => ['page'=>'seo','section'=>'contact','type'=>'textarea',
            'value_ar'=>'احجز استشارة تصميمية مجّانية وية ديلوس إنترناشيونال. خبراؤنا يساعدوك تصمّم البيت الإيطالي الفاخر اللي تحلم بيه.'],

        // ═══════════════════════════════════════════════════════════
        //  LAYOUT (common — nav, marquee, footer, CTAs)
        // ═══════════════════════════════════════════════════════════

        'common.brand.name_primary' => ['page'=>'layout','section'=>'brand','type'=>'text','value_ar'=>'DELOS'],
        'common.brand.name_secondary' => ['page'=>'layout','section'=>'brand','type'=>'text','value_ar'=>'INTERNATIONAL'],

        'common.nav.home' => ['page'=>'layout','section'=>'nav','type'=>'text','value_ar'=>'الرئيسية'],
        'common.nav.brands' => ['page'=>'layout','section'=>'nav','type'=>'text','value_ar'=>'الوكالات'],
        'common.nav.services' => ['page'=>'layout','section'=>'nav','type'=>'text','value_ar'=>'خدماتنا'],
        'common.nav.projects' => ['page'=>'layout','section'=>'nav','type'=>'text','value_ar'=>'المشاريع'],
        'common.nav.about' => ['page'=>'layout','section'=>'nav','type'=>'text','value_ar'=>'عن ديلوس'],
        'common.nav.branches' => ['page'=>'layout','section'=>'nav','type'=>'text','value_ar'=>'الفروع'],
        'common.nav.contact' => ['page'=>'layout','section'=>'nav','type'=>'text','value_ar'=>'تواصل معنا'],
        'common.nav.menu_toggle' => ['page'=>'layout','section'=>'nav','type'=>'text','value_ar'=>'فتح قائمة التنقّل'],

        'common.marquee.tagline' => ['page'=>'layout','section'=>'marquee','type'=>'text',
            'value_ar'=>'حلول الفخامة الإيطالية'],
        'common.marquee.showrooms' => ['page'=>'layout','section'=>'marquee','type'=>'text',
            'value_ar'=>'أربع فروع بأنحاء العراق'],
        'common.marquee.lion' => ['page'=>'layout','section'=>'marquee','type'=>'text','value_ar'=>'ثق بالأسد'],
        'common.marquee.turnkey' => ['page'=>'layout','section'=>'marquee','type'=>'text',
            'value_ar'=>'من الفكرة لحد الإنجاز'],
        'common.marquee.cities' => ['page'=>'layout','section'=>'marquee','type'=>'text',
            'value_ar'=>'أربيل · كركوك · السليمانية · بغداد'],

        'common.footer.tagline_long' => ['page'=>'layout','section'=>'footer','type'=>'textarea',
            'value_ar'=>'حلول الفخامة الإيطالية. نجيب أرقى الحرف اليدوية الإيطالية لبيوت العراق من سنة 2020.'],
        'common.footer.lion_motto' => ['page'=>'layout','section'=>'footer','type'=>'text','value_ar'=>'ثق بالأسد'],
        'common.footer.nav_heading' => ['page'=>'layout','section'=>'footer','type'=>'text','value_ar'=>'التنقّل'],
        'common.footer.showrooms_heading' => ['page'=>'layout','section'=>'footer','type'=>'text','value_ar'=>'فروعنا'],
        'common.footer.showroom_erbil_soran.title' => ['page'=>'layout','section'=>'footer','type'=>'text',
            'value_ar'=>'أربيل — تأسّست 2020'],
        'common.footer.showroom_erbil_soran.address' => ['page'=>'layout','section'=>'footer','type'=>'text',
            'value_ar'=>'شارع الستين متر، يم مستشفى سوران'],
        'common.footer.showroom_erbil_soran.phone' => ['page'=>'layout','section'=>'footer','type'=>'text','value_ar'=>'0750 100 1701'],
        'common.footer.showroom_erbil_gulan.title' => ['page'=>'layout','section'=>'footer','type'=>'text',
            'value_ar'=>'أربيل — شارع كولان'],
        'common.footer.showroom_erbil_gulan.address' => ['page'=>'layout','section'=>'footer','type'=>'text',
            'value_ar'=>'مقابل غرفة التجارة'],
        'common.footer.showroom_erbil_gulan.phone' => ['page'=>'layout','section'=>'footer','type'=>'text','value_ar'=>'0750 200 1003'],
        'common.footer.other_cities' => ['page'=>'layout','section'=>'footer','type'=>'text',
            'value_ar'=>'كركوك · السليمانية · بغداد'],
        'common.footer.partners_heading' => ['page'=>'layout','section'=>'footer','type'=>'text',
            'value_ar'=>'وكلاؤنا الإيطاليين'],
        'common.footer.copyright' => ['page'=>'layout','section'=>'footer','type'=>'text',
            'value_ar'=>'ديلوس إنترناشيونال. جميع الحقوق محفوظة.'],
        'common.footer.bottom_tagline' => ['page'=>'layout','section'=>'footer','type'=>'text',
            'value_ar'=>'حلول الفخامة الإيطالية — العراق'],

        'common.ctas.view_all_projects' => ['page'=>'layout','section'=>'ctas','type'=>'text',
            'value_ar'=>'شوف جميع المشاريع'],
        'common.ctas.explore_partners' => ['page'=>'layout','section'=>'ctas','type'=>'text',
            'value_ar'=>'اكتشفوا وكلاءنا'],
        'common.ctas.book_consultation' => ['page'=>'layout','section'=>'ctas','type'=>'text',
            'value_ar'=>'احجز استشارة الآن'],
        'common.ctas.visit_showroom' => ['page'=>'layout','section'=>'ctas','type'=>'text',
            'value_ar'=>'زوروا معرضنا'],
        'common.ctas.visit_official_website' => ['page'=>'layout','section'=>'ctas','type'=>'text',
            'value_ar'=>'زيارة الموقع الرسمي'],
        'common.ctas.learn_more' => ['page'=>'layout','section'=>'ctas','type'=>'text','value_ar'=>'اقرأ المزيد'],
        'common.ctas.call_us' => ['page'=>'layout','section'=>'ctas','type'=>'text',
            'value_ar'=>'اتّصلوا بنا: 1701 100 0750'],
        'common.ctas.start_project' => ['page'=>'layout','section'=>'ctas','type'=>'text',
            'value_ar'=>'ابدأ مشروعك'],
        'common.ctas.find_nearest_showroom' => ['page'=>'layout','section'=>'ctas','type'=>'text',
            'value_ar'=>'شوف أقرب فرع الك'],
        'common.ctas.back_to_team' => ['page'=>'layout','section'=>'ctas','type'=>'text',
            'value_ar'=>'رجوع للفريق'],
        'common.ctas.work_with_team' => ['page'=>'layout','section'=>'ctas','type'=>'text',
            'value_ar'=>'اشتغل وية فريقنا'],
        'common.ctas.about_company' => ['page'=>'layout','section'=>'ctas','type'=>'text','value_ar'=>'عن الشركة'],
    ];

    /**
     * Brand row Arabic column updates (one entry per brand slug).
     * Columns updated: category_ar, origin_ar, description_ar.
     * `since` is an integer already set correctly in DB, not touched.
     */
    public const BRAND_UPDATES = [
        'lube' => [
            'category_ar' => 'إتقان المطابخ الإيطالية',
            'origin_ar' => 'تريا، ماشيراتا — إيطاليا',
            'description_ar' => 'ماركة لوبي (LUBE) هي أكثر شركة مطابخ حاصلة على جوائز تميز في إيطاليا، وهي شركة عائلية عريقة تنتج أكثر من 600 ألف مطبخ بالسنة. ومن خلال تعاوننا وية لوبي، ديلوس توفرلكم أرقى المعايير العالمية بتصميم المطابخ الإيطالية: اللي تجمع بين راحة الاستخدام المثالية، أجود المواد الأولية، وخيارات تصميم وتعديل ما تخلص حتى تناسب ذوقكم الخاص.',
        ],
        'frigerio' => [
            'category_ar' => 'الأثاث الإيطالي الكلاسيكي',
            'origin_ar' => 'بريانتسا، لومبارديا — إيطاليا',
            'description_ar' => 'ماركة فريجيريو (Frigerio) تجسّد أرقى تقاليد الحرفة بمنطقة بريانتسا — قلب تصنيع الأثاث التاريخي بإيطاليا. ومشهورة بتصاميمها الخالدة وصنعتها اليدوية الدقيقة، قطع فريجيريو هي استثمار بالجمال اللي يدوم لأجيال.',
        ],
        'vittoria-frigerio' => [
            'category_ar' => 'تصميم فاخر بطابع تراثي',
            'origin_ar' => 'بريانتسا، لومبارديا — إيطاليا',
            'description_ar' => 'ڤيتوريا فريجيريو (Vittoria Frigerio) هي الخط الرفيع ضمن عائلة فريجيريو، وتمثّل قمّة الفن الداخلي الإيطالي. كل قطعة تحكي قصة التراث والرقي والحرفة الاستثنائية، محفوظة لأكثر الأمكنة تميّزاً.',
        ],
        'cantori' => [
            'category_ar' => 'إبداعات إيطالية حرفية',
            'origin_ar' => 'فورلي، إميليا-رومانيا — إيطاليا',
            'description_ar' => 'كانتوري (CANTORI) مرادف للتميّز الحرفي الإيطالي. كل قطعة منها مصوَّرة بوصفها عملاً فنيّاً — باستخدام تقنيات الصناعة التقليدية الممزوجة بحسّ تصميم معاصر. مجموعاتها تضفي على كل فضاء داخلي جودة فريدة وروح مميّزة.',
        ],
        'skema' => [
            'category_ar' => 'أرضيات إيطالية فاخرة',
            'origin_ar' => 'إقليم ڤينيتو — إيطاليا',
            'description_ar' => 'سكيما (SKEMA) تجمع بين ثقافة التصميم الإيطالية والهندسة المتقدّمة، حتى تنتج أنظمة أرضيات تجمع بين الجمال اللافت والتميّز التقني. من الخشب الطبيعي لحد الأسطح المهندَسة المعاصرة، سكيما تصنع الأساس اللي تنبني عليه أجمل الفضاءات الداخلية.',
        ],
        'creo-kitchens' => [
            'category_ar' => 'مطابخ إيطالية عصرية',
            'origin_ar' => 'إيطاليا',
            'description_ar' => 'كريو كيتشنز (CREO Kitchens) هي الخط العصري الجديد ضمن عائلة LUBE، تقدّم مطابخ إيطالية بتصميم معاصر وجودة صناعة ممتازة. خيار مثالي للبيوت الحديثة اللي تبحث عن الأناقة الإيطالية بلمسة شبابية.',
        ],
        'faer' => [
            'category_ar' => 'أنظمة خزائن وغرف ملابس',
            'origin_ar' => 'إيطاليا',
            'description_ar' => 'فاير أمبينتي (FAER Ambienti) متخصّصة بتصنيع أنظمة الخزائن وغرف الملابس الإيطالية الفاخرة — بتفاصيل راقية، إضاءة داخلية مدمجة، وتنظيم مخصَّص يتحوّل معاه روتينك اليومي لتجربة فخامة هادئة.',
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
        $snapshotPath = self::writeSnapshot();

        $pageContentResults = [];
        $brandResults = [];
        $pass = 0;
        $fail = 0;

        DB::transaction(function () use (&$pageContentResults, &$brandResults, &$pass, &$fail) {
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

        // Cache busting — same layered approach used after every save in
        // PageContentController so public /ar pages pick up new values
        // on the next request.
        try { PageContent::clearCache(); } catch (\Throwable) {}
        try { Cache::flush(); } catch (\Throwable) {}
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
