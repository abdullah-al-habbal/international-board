<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SettingType;
use App\Models\ApplicationSetting;
use Illuminate\Database\Seeder;

class ApplicationSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = config('applicationSettingSeederDataConfig', []);

        foreach ($settings as $data) {
            ApplicationSetting::updateOrCreate(
                ['key' => $data['key']],
                [
                    'value' => $data['value'],
                    'type' => SettingType::from($data['type']),
                ]
            );
        }
    }
}
