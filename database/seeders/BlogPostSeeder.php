<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'slug' => 'launching-new-certification-standards',
                'title' => [
                    'en' => 'Launching New Certification Standards',
                    'ar' => 'إطلاق معايير اعتماد جديدة',
                ],
                'excerpt' => [
                    'en' => 'We are excited to announce the launch of our updated certification standards for 2026.',
                    'ar' => 'يسعدنا الإعلان عن إطلاق معايير الاعتماد المحدثة لعام 2026.',
                ],
                'content' => [
                    'en' => '<p>Our new standards focus on digital transformation and industry-specific skills. These changes aim to enhance the value of our certifications globally.</p>',
                    'ar' => '<p>تركز معاييرنا الجديدة على التحول الرقمي والمهارات الخاصة بالصناعة. تهدف هذه التغييرات إلى تعزيز قيمة شهاداتنا عالمياً.</p>',
                ],
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'slug' => 'top-10-trainers-of-2025',
                'title' => [
                    'en' => 'Top 10 Trainers of 2025',
                    'ar' => 'أفضل 10 مدربين لعام 2025',
                ],
                'excerpt' => [
                    'en' => 'Recognizing excellence in training and dedication to student success.',
                    'ar' => 'تكريم التميز في التدريب والتفاني في نجاح الطلاب.',
                ],
                'content' => [
                    'en' => '<p>Congratulations to our top 10 trainers who have shown exceptional performance this year.</p>',
                    'ar' => '<p>تهانينا لأفضل 10 مدربين أظهروا أداءً استثنائياً هذا العام.</p>',
                ],
                'is_published' => true,
                'published_at' => now()->subDays(5),
            ],
            [
                'slug' => 'expanding-to-new-regions',
                'title' => [
                    'en' => 'Expanding to New Regions',
                    'ar' => 'التوسع في مناطق جديدة',
                ],
                'excerpt' => [
                    'en' => 'The board is opening new accreditation offices in the Gulf region.',
                    'ar' => 'يفتح المجلس مكاتب اعتماد جديدة في منطقة الخليج.',
                ],
                'content' => [
                    'en' => '<p>Our expansion plan includes new offices in Riyadh and Dubai to better serve our centers.</p>',
                    'ar' => '<p>تتضمن خطة التوسع لدينا مكاتب جديدة في الرياض ودبي لخدمة مراكزنا بشكل أفضل.</p>',
                ],
                'is_published' => true,
                'published_at' => now()->subDays(10),
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::updateOrCreate(['slug' => $post['slug']], $post);
        }
    }
}
