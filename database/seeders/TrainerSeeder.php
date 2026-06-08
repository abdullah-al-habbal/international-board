<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CertifiedCenter;
use App\Models\Trainer;
use Illuminate\Database\Seeder;

class TrainerSeeder extends Seeder
{
    public function run(): void
    {
        $centers = CertifiedCenter::all();

        $trainers = Trainer::factory(5)->create();

        foreach ($trainers->take(2) as $i => $trainer) {
            $trainer->update(['center_id' => $centers->get($i)->id]);
        }
    }
}
