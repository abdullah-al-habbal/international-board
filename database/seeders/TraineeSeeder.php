<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Trainee;
use Illuminate\Database\Seeder;

class TraineeSeeder extends Seeder
{
    public function run(): void
    {
        Trainee::factory(5)->create();
    }
}
