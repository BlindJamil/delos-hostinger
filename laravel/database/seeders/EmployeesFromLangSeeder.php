<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

/**
 * Seeds the employees table with the 3 original team members previously
 * hard-coded in lang/{en,ar,it}/home.php. Uses firstOrCreate keyed on
 * `name_en` so it is idempotent — re-running will not duplicate rows,
 * and will never overwrite data an admin has edited in the panel.
 *
 * Images point to bare filenames (employee-1.jpg etc.) that already live
 * in public/images/. The Employee::image_url accessor resolves these to
 * the correct public URL while also supporting admin-uploaded files.
 */
class EmployeesFromLangSeeder extends Seeder
{
    public function run(): void
    {
        $seed = [
            [
                'name_en' => 'Ahmed K.',
                'name_ar' => 'أحمد ك.',
                'name_it' => 'Ahmed K.',
                'role_en' => 'Lead Interior Designer',
                'role_ar' => 'كبير مصمّمي الديكور الداخلي',
                'role_it' => 'Interior Designer responsabile',
                'branch' => 'Erbil',
                'achievement_en' => '<p>Led the design of 12 luxury villa transformations across Erbil this quarter.</p>',
                'achievement_ar' => '<p>قاد تصميم 12 تحويلًا لڤيلات فاخرة في أربيل خلال هذا الربع.</p>',
                'achievement_it' => '<p>Ha guidato il design di 12 ville di lusso trasformate a Erbil in questo trimestre.</p>',
                'image' => 'employee-1.jpg',
                'sort_order' => 10,
                'active' => true,
            ],
            [
                'name_en' => 'Sara M.',
                'name_ar' => 'سارة م.',
                'name_it' => 'Sara M.',
                'role_en' => 'Senior Engineer',
                'role_ar' => 'مهندسة أولى',
                'role_it' => 'Ingegnere senior',
                'branch' => 'Sulaymaniyah',
                'achievement_en' => '<p>Revolutionized our 3D concept workflow, cutting design timelines by 40%.</p>',
                'achievement_ar' => '<p>أحدثت نقلة نوعية في سير عمل التصاميم ثلاثية الأبعاد، واختصرت الجداول الزمنية بنسبة 40%.</p>',
                'achievement_it' => '<p>Ha rivoluzionato il nostro flusso di progettazione 3D, riducendo i tempi del 40%.</p>',
                'image' => 'employee-2.jpg',
                'sort_order' => 20,
                'active' => true,
            ],
            [
                'name_en' => 'Omar R.',
                'name_ar' => 'عمر ر.',
                'name_it' => 'Omar R.',
                'role_en' => 'Project Manager',
                'role_ar' => 'مدير مشاريع',
                'role_it' => 'Project Manager',
                'branch' => 'Baghdad',
                'achievement_en' => '<p>Spearheaded the successful launch of our new Baghdad showroom.</p>',
                'achievement_ar' => '<p>قاد الإطلاق الناجح لمعرضنا الجديد في بغداد.</p>',
                'achievement_it' => '<p>Ha guidato il lancio di successo del nostro nuovo showroom a Baghdad.</p>',
                'image' => 'employee-3.jpg',
                'sort_order' => 30,
                'active' => true,
            ],
        ];

        foreach ($seed as $row) {
            Employee::firstOrCreate(
                ['name_en' => $row['name_en']],
                $row
            );
        }
    }
}
