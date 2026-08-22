<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TrainerRole;
use Illuminate\Database\Seeder;

class TrainerRoleSeeder extends Seeder
{
    private const ROLES = [
        ['en' => 'Sales Manager', 'ar' => 'مدير مبيعات'],
        ['en' => 'Senior Trainer', 'ar' => 'مدرب أول'],
        ['en' => 'Training Coordinator', 'ar' => 'منسق تدريب'],
        ['en' => 'Regional Manager', 'ar' => 'مدير إقليمي'],
    ];

    public function run(): void
    {
        foreach (self::ROLES as $role) {
            TrainerRole::firstOrCreate([
                'name->en' => $role['en'],
            ], [
                'name' => $role,
            ]);
        }
    }
}
