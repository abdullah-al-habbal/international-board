<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\Trainer;
use Illuminate\Database\Seeder;

class TrainerSeeder extends Seeder
{
    public function run(): void
    {
        $names = Certification::query()
            ->whereNotNull('trainer_name')
            ->where('trainer_name', '!=', '')
            ->pluck('trainer_name')
            ->unique();

        foreach ($names as $name) {
            Trainer::firstOrCreate(['name' => trim($name)]);
        }
    }
}
