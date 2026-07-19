<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    public function run(): void
    {
        $specializations = [
            ['name' => ['en' => 'Training', 'ar' => 'تدريب']],
            ['name' => ['en' => 'Consulting', 'ar' => 'استشارات']],
            ['name' => ['en' => 'Leadership', 'ar' => 'قيادة']],
            ['name' => ['en' => 'Management', 'ar' => 'إدارة']],
            ['name' => ['en' => 'Communication', 'ar' => 'اتصال']],
            ['name' => ['en' => 'Technical Skills', 'ar' => 'مهارات تقنية']],
            ['name' => ['en' => 'Soft Skills', 'ar' => 'مهارات ناعمة']],
            ['name' => ['en' => 'Project Management', 'ar' => 'إدارة المشاريع']],
            ['name' => ['en' => 'Digital Marketing', 'ar' => 'التسويق الرقمي']],
            ['name' => ['en' => 'Human Resources', 'ar' => 'الموارد البشرية']],
            ['name' => ['en' => 'Quality Management', 'ar' => 'إدارة الجودة']],
            ['name' => ['en' => 'Entrepreneurship', 'ar' => 'ريادة الأعمال']],
        ];

        foreach ($specializations as $specialization) {
            Specialization::create($specialization);
        }
    }
}
