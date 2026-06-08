<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Trainer;
use App\Models\TrainerAccreditationRequest;
use Illuminate\Database\Seeder;

class TrainerAccreditationRequestSeeder extends Seeder
{
    public function run(): void
    {
        $trainers = Trainer::all();

        foreach ($trainers as $trainer) {
            TrainerAccreditationRequest::factory()
                ->recycle($trainers)
                ->create(['trainer_id' => $trainer->id]);
        }
    }
}
