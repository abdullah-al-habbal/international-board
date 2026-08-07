<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AgentPerson;
use App\Models\CertifiedCenter;
use App\Models\FinancialRequest;
use App\Models\Trainer;
use Illuminate\Database\Seeder;

class FinancialRequestSeeder extends Seeder
{
    public function run(): void
    {
        $centers = CertifiedCenter::all();
        $trainers = Trainer::all();
        $agents = AgentPerson::all();

        FinancialRequest::factory(5)->recycle($agents)
            ->state(fn () => ['requestable_type' => CertifiedCenter::class, 'requestable_id' => $centers->random()->getKey()])
            ->create();
        FinancialRequest::factory(5)->recycle($agents)
            ->state(fn () => ['requestable_type' => Trainer::class, 'requestable_id' => $trainers->random()->getKey()])
            ->create();
    }
}
