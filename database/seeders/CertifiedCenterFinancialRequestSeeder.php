<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CertifiedCenter;
use App\Models\CertifiedCenterFinancialRequest;
use App\Models\CertifiedCenterPaymentAgentPerson;
use Illuminate\Database\Seeder;

class CertifiedCenterFinancialRequestSeeder extends Seeder
{
    public function run(): void
    {
        $centers = CertifiedCenter::all();
        $agents = CertifiedCenterPaymentAgentPerson::all();
        CertifiedCenterFinancialRequest::factory(5)->recycle($centers)->recycle($agents)->create();
    }
}
