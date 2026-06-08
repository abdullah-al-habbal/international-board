<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CertifiedCenter;
use App\Models\DocumentType;
use Illuminate\Database\Eloquent\Factories\Factory;

class CenterTypeRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'certified_center_id' => CertifiedCenter::factory(),
            'document_type_id' => DocumentType::factory(),
            'requested_name' => fake()->word(),
            'requested_description' => fake()->sentence(),
            'type' => fake()->randomElement(['course', 'document_type']),
            'status' => 'pending',
            'rejection_message' => null,
        ];
    }
}
