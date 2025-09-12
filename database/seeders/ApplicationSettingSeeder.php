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
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/certificationboard', 'type' => 'url', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com/certboard', 'type' => 'url', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'linkedin_url', 'value' => 'https://linkedin.com/company/certboard', 'type' => 'url', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'max_upload_size', 'value' => '10', 'type' => 'number', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'maintenance_mode', 'value' => 'false', 'type' => 'boolean', 'created_at' => $now, 'updated_at' => $now],
        ];

        ApplicationSetting::upsert(
            $settings,
            ['key'],
            ['value', 'type', 'updated_at']
        );
    }
}
