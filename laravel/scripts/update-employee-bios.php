<?php

/**
 * One-off: set longer 4–5 line bios for the three seeded employees
 * in all three supported locales (en, ar, it).
 *
 * Run: php artisan tinker < scripts/update-employee-bios.php
 * (or: php scripts/update-employee-bios.php from a boot-strapped context)
 */

use App\Models\Employee;

$bios = [
    'Ahmed K.' => [
        'en' => "<p>Ahmed has led Delos's residential design studio in Erbil since 2021, shaping the Italian luxury identity behind our most ambitious projects. This quarter he delivered twelve villa transformations across Kurdistan, each grounded in the same obsession for proportion, material honesty, and natural light. Trained in Milan, Ahmed brings an editorial sensibility to every floor plan — the kind of quiet confidence that lets a room breathe. Clients describe working with him as less a renovation and more a collaboration with an author.</p>",
        'ar' => "<p>يقود أحمد استوديو التصميم الداخلي السكني لدى ديلوس في أربيل منذ عام 2021، ويشكّل هوية الفخامة الإيطالية خلف أكثر مشاريعنا طموحًا. في هذا الربع أنجز اثنتي عشرة فيلا فاخرة في كردستان، تجمعها جميعًا رؤية واحدة: دقة التناسب، وصدق الخامة، وانسياب الضوء الطبيعي. تدرّب أحمد في ميلانو، ويحمل حساسية تحريرية إلى كل مخطط — ذلك الحضور الهادئ الذي يترك للمساحة أن تتنفس. يصف عملاؤنا العمل معه بأنه ليس تجديدًا، بل تعاونًا مع مؤلف.</p>",
        'it' => "<p>Ahmed guida lo studio di interior design residenziale di Delos a Erbil dal 2021, plasmando l'identità del lusso italiano che anima i nostri progetti più ambiziosi. In questo trimestre ha consegnato dodici trasformazioni di ville di lusso in tutto il Kurdistan, ciascuna costruita sulla stessa ossessione per proporzioni, onestà dei materiali e luce naturale. Formatosi a Milano, porta una sensibilità editoriale in ogni planimetria — quella sicurezza silenziosa che lascia respirare ogni stanza. I clienti descrivono il lavoro con lui non come una ristrutturazione, ma come una collaborazione con un autore.</p>",
    ],
    'Sara M.' => [
        'en' => "<p>Sara leads Delos's engineering and 3D visualization studio from Sulaymaniyah, where she rewrote our entire concept-to-approval workflow this year. By bringing real-time rendering into the very first client meeting, she cut our design timelines by forty percent without compromising an inch of technical rigor. A structural engineer by training, Sara is fluent in both the architectural and the technical languages of a project — so nothing gets lost between vision and build. Every plan that leaves her desk has already been stress-tested three different ways.</p>",
        'ar' => "<p>تقود سارة استوديو الهندسة والتصور ثلاثي الأبعاد لدى ديلوس من السليمانية، حيث أعادت كتابة سير العمل من الفكرة حتى الاعتماد هذا العام. وبإدخال العرض الحي ثلاثي الأبعاد في أول اجتماع مع العميل، اختصرت جداولنا التصميمية بنسبة أربعين بالمئة دون التنازل عن أي قدر من الدقة الهندسية. سارة مهندسة إنشائية بالتكوين، وتجيد لغة العمارة ولغة الهندسة معًا، فلا يضيع شيء بين الرؤية والتنفيذ. كل مخطط يخرج من يديها يكون قد اختُبر بثلاث طرق مختلفة.</p>",
        'it' => "<p>Sara dirige lo studio di ingegneria e visualizzazione 3D di Delos da Sulaymaniyah, dove quest'anno ha riscritto l'intero flusso di lavoro dal concept all'approvazione. Portando il rendering in tempo reale già nel primo incontro con il cliente, ha ridotto i nostri tempi di progettazione del quaranta percento senza compromettere un millimetro di rigore tecnico. Ingegnere strutturale di formazione, Sara parla fluentemente sia il linguaggio architettonico sia quello tecnico di un progetto — così nulla si perde tra visione e cantiere. Ogni piano che esce dalla sua scrivania è già stato verificato in tre modi diversi.</p>",
    ],
    'Omar R.' => [
        'en' => "<p>Omar spearheaded the opening of our Baghdad showroom this quarter, translating the full Delos standard into a new city without losing a single detail. As Project Manager he oversees logistics, client communication, and site coordination across all four branches — the invisible work that makes everything else look effortless. Before joining Delos he ran complex hospitality fit-outs across the Gulf, which is where he learned that a project manager's real job is to remove the obstacles nobody else sees. His calm under pressure has quietly become one of the firm's most reliable assets.</p>",
        'ar' => "<p>قاد عمر افتتاح معرضنا الجديد في بغداد هذا الربع، وترجم معايير ديلوس الكاملة إلى مدينة جديدة دون أن تفوته تفصيلة واحدة. وبوصفه مدير مشاريع، يشرف على اللوجستيات والتواصل مع العملاء وتنسيق المواقع عبر فروعنا الأربعة — ذلك العمل غير المرئي الذي يجعل كل شيء آخر يبدو سلسًا. قبل انضمامه إلى ديلوس، أدار مشاريع ضيافة معقدة في دول الخليج، وهناك تعلّم أن المهمة الحقيقية لمدير المشروع هي إزالة العقبات التي لا يراها الآخرون. هدوؤه تحت الضغط أصبح بهدوء أحد أكثر ركائز الفريق موثوقية.</p>",
        'it' => "<p>Omar ha guidato l'apertura del nostro nuovo showroom di Baghdad in questo trimestre, traducendo l'intero standard Delos in una nuova città senza perdere un solo dettaglio. Come Project Manager supervisiona logistica, comunicazione con i clienti e coordinamento dei cantieri in tutte e quattro le sedi — il lavoro invisibile che fa sembrare facile tutto il resto. Prima di Delos ha gestito complesse ristrutturazioni nell'ospitalità del Golfo, ed è lì che ha imparato che il vero compito di un project manager è rimuovere gli ostacoli che nessun altro vede. La sua calma sotto pressione è diventata uno dei punti di forza più silenziosi dello studio.</p>",
    ],
];

foreach ($bios as $name => $localized) {
    $emp = Employee::query()->where('name_en', $name)->first();
    if (!$emp) {
        echo "SKIP: {$name} not found\n";
        continue;
    }
    $emp->achievement_en = $localized['en'];
    $emp->achievement_ar = $localized['ar'];
    $emp->achievement_it = $localized['it'];
    $emp->save();
    echo "UPDATED: {$name} (id={$emp->id})\n";
}

echo "Done.\n";
