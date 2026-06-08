<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Trainer;
use App\Models\TrainerDocumentType;
use Illuminate\Database\Seeder;

class TrainerDocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $trainers = Trainer::all();
        TrainerDocumentType::factory(5)->recycle($trainers)->create();
    }
}
