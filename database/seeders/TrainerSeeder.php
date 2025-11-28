<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Trainer;
use Illuminate\Database\Seeder;

class TrainerSeeder extends Seeder
{
    public function run(): void
    {
        // Get some countries for assignment
        $libya = Country::where('code', 'LBY')->first();
        $saudi = Country::where('code', 'SAU')->first();
        $egypt = Country::where('code', 'EGY')->first();

        $trainers = [
            [
                'name' => 'محمد أحمد السنوسي',
                'email' => 'mohamed.senussi@example.com',
                'phone' => '+218912345678',
                'bio' => 'مدرب معتمد في القيادة والإدارة مع خبرة 15 عامًا',
                'country_id' => $libya?->id,
                'specializations' => ['Leadership', 'Management', 'Training'],
                'is_active' => true,
            ],
            [
                'name' => 'فاطمة علي الهوني',
                'email' => 'fatima.hawni@example.com',
                'phone' => '+218923456789',
                'bio' => 'خبيرة في التطوير المهني والموارد البشرية',
                'country_id' => $libya?->id,
                'specializations' => ['HR', 'Professional Development', 'Soft Skills'],
                'is_active' => true,
            ],
            [
                'name' => 'خالد عبدالعزيز المطيري',
                'email' => 'khaled.mutairi@example.com',
                'phone' => '+966501234567',
                'bio' => 'مستشار ومدرب في إدارة المشاريع والجودة',
                'country_id' => $saudi?->id,
                'specializations' => ['Project Management', 'Quality Management', 'Consulting'],
                'is_active' => true,
            ],
            [
                'name' => 'نورة محمد القحطاني',
                'email' => 'noura.qahtani@example.com',
                'phone' => '+966502345678',
                'bio' => 'مدربة معتمدة في مهارات الاتصال والعلاقات العامة',
                'country_id' => $saudi?->id,
                'specializations' => ['Communication', 'Public Relations', 'Soft Skills'],
                'is_active' => true,
            ],
            [
                'name' => 'أحمد حسن المصري',
                'email' => 'ahmed.masry@example.com',
                'phone' => '+201012345678',
                'bio' => 'خبير في التدريب التقني وتطوير البرمجيات',
                'country_id' => $egypt?->id,
                'specializations' => ['Technical Skills', 'Software Development', 'IT Training'],
                'is_active' => true,
            ],
            [
                'name' => 'سارة محمود عبدالله',
                'email' => 'sara.abdullah@example.com',
                'phone' => '+201023456789',
                'bio' => 'مدربة في التسويق الرقمي ووسائل التواصل الاجتماعي',
                'country_id' => $egypt?->id,
                'specializations' => ['Digital Marketing', 'Social Media', 'Communication'],
                'is_active' => true,
            ],
            [
                'name' => 'عمر سالم الكيلاني',
                'email' => 'omar.kilani@example.com',
                'phone' => '+218934567890',
                'bio' => 'مستشار أعمال ومدرب في ريادة الأعمال',
                'country_id' => $libya?->id,
                'specializations' => ['Entrepreneurship', 'Business Consulting', 'Leadership'],
                'is_active' => true,
            ],
            [
                'name' => 'ليلى يوسف النجار',
                'email' => 'layla.najjar@example.com',
                'phone' => '+218945678901',
                'bio' => 'مدربة في التنمية الشخصية والتخطيط الاستراتيجي',
                'country_id' => $libya?->id,
                'specializations' => ['Personal Development', 'Strategic Planning', 'Coaching'],
                'is_active' => true,
            ],
        ];

        foreach ($trainers as $trainerData) {
            Trainer::firstOrCreate(
                ['email' => $trainerData['email']],
                $trainerData
            );
        }

        // Create some additional random trainers
        if (Country::count() > 0) {
            Trainer::factory(12)->create();
        }
    }
}
