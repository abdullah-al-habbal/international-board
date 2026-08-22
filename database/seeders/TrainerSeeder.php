<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CertifiedCenter;
use App\Models\Specialization;
use App\Models\Trainer;
use App\Models\TrainerRole;
use Illuminate\Database\Seeder;

class TrainerSeeder extends Seeder
{
    public function run(): void
    {
        $centers = CertifiedCenter::all();
        $specializations = Specialization::all();
        $roles = TrainerRole::all();

        $trainers = Trainer::factory(5)->create();

        foreach ($trainers->take(2) as $i => $trainer) {
            $trainer->update(['center_id' => $centers->get($i)->id]);
        }

        foreach ($trainers as $i => $trainer) {
            $randomSpecs = $specializations->random(rand(1, min(4, $specializations->count())));
            $trainer->specializations()->attach($randomSpecs->pluck('id'));

            // Roles are optional: cycle the seeded roles but leave the last
            // trainer unassigned so the nullable path stays represented.
            if ($roles->isNotEmpty() && $i < $trainers->count() - 1) {
                $trainer->update(['trainer_role_id' => $roles[$i % $roles->count()]->id]);
            }
        }
    }
}
