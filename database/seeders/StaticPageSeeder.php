<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\StaticPage;
use Illuminate\Database\Seeder;

class StaticPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'about-us',
                'title' => [
                    'en' => 'About Us',
                    'ar' => 'من نحن',
                ],
                'content' => [
                    'en' => 'We are a leading certification board committed to excellence in training and accreditation.',
                    'ar' => 'نحن مجلس اعتماد رائد ملتزم بالتميز في التدريب والاعتماد.',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'our-services',
                'title' => [
                    'en' => 'Our Services',
                    'ar' => 'خدماتنا',
                ],
                'content' => [
                    'en' => 'We provide comprehensive certification and training services.',
                    'ar' => 'نقدم خدمات شاملة للاعتماد والتدريب.',
                ],
                'is_active' => true,
            ],
            [
                'slug' => 'contact-us',
                'title' => [
                    'en' => 'Contact Us',
                    'ar' => 'اتصل بنا',
                ],
                'content' => [
                    'en' => 'Get in touch with us for any inquiries.',
                    'ar' => 'تواصل معنا لأي استفسارات.',
                ],
                'is_active' => true,
            ],
        ];

        $now = now();
        foreach ($pages as &$page) {
            $page['created_at'] = $now;
            $page['updated_at'] = $now;
        }

        StaticPage::upsert(
            $pages,
            ['slug'],
            ['title', 'content', 'is_active', 'updated_at']
        );
    }
}
