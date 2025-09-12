<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CertifiedCenter;
use Illuminate\Database\Seeder;

class CertifiedCenterSeeder extends Seeder
{
    public function run(): void
    {
        CertifiedCenter::factory()->create([
            'name' => 'Test Center',
            'email' => 'center@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
            'is_active' => true,
            'accreditation_period_start' => now()->subDays(30),
            'accreditation_period_end' => now()->addDays(365),
        ]);
    }
}
