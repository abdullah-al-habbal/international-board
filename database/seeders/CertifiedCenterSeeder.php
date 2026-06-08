<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CertifiedCenter;
use Illuminate\Database\Seeder;

class CertifiedCenterSeeder extends Seeder
{
    public function run(): void
    {
        CertifiedCenter::factory(5)->create();
    }
}
