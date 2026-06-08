<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CertifiedCenterPaymentAgentPerson;
use App\Models\Trainer;
use App\Models\TrainerFinancialRequest;
use Illuminate\Database\Seeder;

class TrainerFinancialRequestSeeder extends Seeder
{
    public function run(): void
    {
        $trainers = Trainer::all();
        $agents = CertifiedCenterPaymentAgentPerson::all();
        TrainerFinancialRequest::factory(5)->recycle($trainers)->recycle($agents)->create();
    }
}
