<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CertifiedCenter;
use App\Models\CertifiedCenterPaymentAgentPerson;
use Illuminate\Database\Seeder;

class CertifiedCenterPaymentAgentPersonSeeder extends Seeder
{
    public function run(): void
    {
        $centers = CertifiedCenter::all();
        CertifiedCenterPaymentAgentPerson::factory(5)->recycle($centers)->create();
    }
}
