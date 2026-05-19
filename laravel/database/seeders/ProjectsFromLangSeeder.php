<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Seeds the projects table from lang/{en,ar,it}/projects.php so nothing is
 * lost when switching the portfolio page to DB-driven content. Uses
 * firstOrCreate keyed on title_en to stay idempotent and never overwrite
 * what an admin has edited.
 *
 * The first 5 projects are flagged `featured = true` to populate the
 * home/projects hero slides (matching what was previously hard-coded).
 */
class ProjectsFromLangSeeder extends Seeder
{
    public function run(): void
    {
        // Production guard: once an admin has populated this table — even
        // with a single row — never re-inject seed rows. firstOrCreate is
        // only safe against updates, not against deletes; an admin-deleted
        // record would resurrect on every deploy without this guard. The
        // lang file rows() are kept as the baseline for fresh installs.
        if (Project::query()->exists()) {
            return;
        }

        foreach (static::rows() as $index => $row) {
            $row['sort_order'] = ($index + 1) * 10;
            $row['active'] = true;
            Project::firstOrCreate(
                ['title_en' => $row['title_en']],
                $row
            );
        }
    }

    public static function rows(): array
    {
        return [
            [
                'title_en' => 'Villa Moderna Kitchen',
                'title_ar' => 'مطبخ ڤيلّا موديرنا',
                'title_it' => 'Cucina Villa Moderna',
                'city' => 'Erbil',
                'type' => 'kitchens',
                'type_label_en' => 'Kitchens',
                'type_label_ar' => 'المطابخ',
                'type_label_it' => 'Cucine',
                'brand' => 'LUBE',
                'year' => 2024,
                'image' => 'collection-lube-classic.jpg',
                'featured' => true,
            ],
            [
                'title_en' => 'Penthouse Living Suite',
                'title_ar' => 'جناح جلوس في بنتهاوس',
                'title_it' => 'Suite soggiorno attico',
                'city' => 'Kirkuk',
                'type' => 'living room',
                'type_label_en' => 'Living Room',
                'type_label_ar' => 'غرف الجلوس',
                'type_label_it' => 'Soggiorno',
                'brand' => 'CANTORI',
                'year' => 2024,
                'image' => 'cantori-1.jpg',
                'featured' => true,
            ],
            [
                'title_en' => 'Presidential Bedroom',
                'title_ar' => 'غرفة نوم رئاسية',
                'title_it' => 'Camera da letto presidenziale',
                'city' => 'Baghdad',
                'type' => 'bedroom',
                'type_label_en' => 'Bedroom',
                'type_label_ar' => 'غرف النوم',
                'type_label_it' => 'Camera da letto',
                'brand' => 'Vittoria Frigerio',
                'year' => 2023,
                'image' => 'collection-vittoria.jpg',
                'featured' => true,
            ],
            [
                'title_en' => 'Luxury Turnkey Residence',
                'title_ar' => 'إقامة متكاملة فاخرة',
                'title_it' => 'Residenza di lusso chiavi in mano',
                'city' => 'Erbil',
                'type' => 'turnkey',
                'type_label_en' => 'Turnkey',
                'type_label_ar' => 'المشاريع المتكاملة',
                'type_label_it' => 'Chiavi in mano',
                'brand' => 'Delos',
                'year' => 2024,
                'image' => 'delos-erbil-showroom-6.jpg',
                'featured' => true,
            ],
            [
                'title_en' => 'Bespoke Open Kitchen',
                'title_ar' => 'مطبخ مفتوح حسب الطلب',
                'title_it' => 'Cucina a vista su misura',
                'city' => 'Erbil',
                'type' => 'kitchens',
                'type_label_en' => 'Kitchens',
                'type_label_ar' => 'المطابخ',
                'type_label_it' => 'Cucine',
                'brand' => 'LUBE',
                'year' => 2023,
                'image' => 'lube-kitchen-3.jpg',
                'featured' => true,
            ],
            [
                'title_en' => 'Grand Walk-In Wardrobe',
                'title_ar' => 'خزانة ووك-إن كبرى',
                'title_it' => 'Grande cabina armadio',
                'city' => 'Sulaymaniyah',
                'type' => 'wardrobes',
                'type_label_en' => 'Wardrobes',
                'type_label_ar' => 'الخزائن',
                'type_label_it' => 'Armadi',
                'brand' => null,
                'year' => 2024,
                'image' => 'delos-showroom-card.jpg',
                'featured' => false,
            ],
            [
                'title_en' => 'Contemporary Kitchen Island',
                'title_ar' => 'جزيرة مطبخ معاصرة',
                'title_it' => 'Cucina con isola contemporanea',
                'city' => 'Kirkuk',
                'type' => 'kitchens',
                'type_label_en' => 'Kitchens',
                'type_label_ar' => 'المطابخ',
                'type_label_it' => 'Cucine',
                'brand' => 'LUBE',
                'year' => 2023,
                'image' => 'lube-kitchen-3.jpg',
                'featured' => false,
            ],
            [
                'title_en' => 'Master Suite Bedroom',
                'title_ar' => 'غرفة نوم رئيسية',
                'title_it' => 'Camera da letto padronale',
                'city' => 'Baghdad',
                'type' => 'bedroom',
                'type_label_en' => 'Bedroom',
                'type_label_ar' => 'غرف النوم',
                'type_label_it' => 'Camera da letto',
                'brand' => null,
                'year' => 2024,
                'image' => 'about-philosophy.jpg',
                'featured' => false,
            ],
            [
                'title_en' => 'Italian Living Room Suite',
                'title_ar' => 'جناح جلوس إيطالي',
                'title_it' => 'Suite soggiorno italiana',
                'city' => 'Sulaymaniyah',
                'type' => 'living room',
                'type_label_en' => 'Living Room',
                'type_label_ar' => 'غرف الجلوس',
                'type_label_it' => 'Soggiorno',
                'brand' => 'Frigerio',
                'year' => 2023,
                'image' => 'frigerio-sofa-1.webp',
                'featured' => false,
            ],
        ];
    }
}
