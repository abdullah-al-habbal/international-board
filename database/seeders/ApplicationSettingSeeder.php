<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ApplicationSetting;
use Illuminate\Database\Seeder;

class ApplicationSettingSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $settings = [
            ['key' => 'site_name', 'value' => 'Certification Board', 'type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'site_email', 'value' => 'admin@certificationboard.com', 'type' => 'email', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'site_phone', 'value' => '+966-11-123-4567', 'type' => 'phone', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'site_logo_primary', 'value' => 'assets/website/images/logo.png', 'type' => 'url', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'site_logo_white', 'value' => 'assets/website/images/logo-white.png', 'type' => 'url', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/certificationboard', 'type' => 'url', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com/certboard', 'type' => 'url', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'linkedin_url', 'value' => 'https://linkedin.com/company/certboard', 'type' => 'url', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'max_upload_size', 'value' => '10', 'type' => 'number', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'maintenance_mode', 'value' => 'false', 'type' => 'boolean', 'created_at' => $now, 'updated_at' => $now],
            [
                'key' => 'home_testimonials',
                'value' => json_encode([
                    [
                        'name' => 'Ahmed Al-Farsi',
                        'role' => 'Certified Trainer',
                        'text' => 'The certification process was incredibly smooth and professional. Highly recommended!',
                        'avatar' => 'assets/website/images/about/member.jpg'
                    ],
                    [
                        'name' => 'Sarah Johnson',
                        'role' => 'Center Manager',
                        'text' => 'We have been an accredited center for 3 years, and the support from the board is top-notch.',
                        'avatar' => 'assets/website/images/about/member.jpg'
                    ],
                    [
                        'name' => 'Mohammed Al-Saeed',
                        'role' => 'Professional Trainee',
                        'text' => 'Verified my certificate in seconds. Extremely reliable platform.',
                        'avatar' => 'assets/website/images/about/member.jpg'
                    ]
                ]),
                'type' => 'json',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'key' => 'trainer_evaluation_text',
                'value' => '<p>Trainer evaluation is based on several criteria including experience, methodology, and student feedback. Please contact us for more details on how to get evaluated.</p>',
                'type' => 'html',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ];

        ApplicationSetting::upsert(
            $settings,
            ['key'],
            ['value', 'type', 'updated_at']
        );
    }
}
