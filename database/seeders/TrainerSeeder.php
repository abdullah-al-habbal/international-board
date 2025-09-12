<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Trainer;
use Illuminate\Database\Seeder;

class TrainerSeeder extends Seeder
{
    public function run(): void
    {
        $trainers = [
            [
                'name' => [
                    'en' => 'Dr. Ahmed Al-Mansouri',
                    'ar' => 'د. أحمد المنصوري'
                ],
                'bio' => [
                    'en' => 'Expert trainer with 15 years of experience in professional development.',
                    'ar' => 'مدرب خبير مع 15 عاماً من الخبرة في التطوير المهني.'
                ],
                'address' => [
                    'en' => 'Riyadh, Saudi Arabia',
                    'ar' => 'الرياض، المملكة العربية السعودية'
                ],
                'email' => 'ahmed.mansouri@example.com',
                'phone' => '+966-50-123-4567',
                'is_active' => true,
            ],
            [
                'name' => [
                    'en' => 'Ms. Fatima Al-Zahra',
                    'ar' => 'أ. فاطمة الزهراء'
                ],
                'bio' => [
                    'en' => 'Certified professional trainer specializing in leadership development.',
                    'ar' => 'مدربة محترفة معتمدة متخصصة في تطوير القيادة.'
                ],
                'address' => [
                    'en' => 'Jeddah, Saudi Arabia',
                    'ar' => 'جدة، المملكة العربية السعودية'
                ],
                'email' => 'fatima.zahra@example.com',
                'phone' => '+966-50-987-6543',
                'is_active' => true,
            ],
        ];

        $now = now();
        foreach ($trainers as &$trainer) {
            $trainer['created_at'] = $now;
            $trainer['updated_at'] = $now;
        }

        Trainer::insert($trainers);
    }
}
