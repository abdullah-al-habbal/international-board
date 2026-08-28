<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Trainer;
use App\Models\TrainerDocumentType;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

#[UseModel(TrainerDocumentType::class)]
class TrainerDocumentTypeFactory extends Factory
{
    public function definition(): array
    {
        $key = fake()->unique()->slug(2);

        return [
            'trainer_id' => Trainer::factory(),
            'key' => $key,
            'name' => ['en' => ucwords(str_replace('-', ' ', $key)), 'ar' => 'نوع '.fake()->word()],
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'admin_notes' => null,
            'reviewed_by_admin_id' => null,
        ];
    }
}
