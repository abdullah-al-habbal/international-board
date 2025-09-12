<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CertifiedCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'certified_center_id' => CertifiedCenter::factory(),
            'certificate_type' => fake()->randomElement(['training_certificate', 'accreditation_certificate']),
            'trainee_name' => fake()->name(),
            'accredited_serial_number' => fake()->unique()->regexify('[A-Z]{3}[0-9]{7}'),
            'document_code' => fake()->regexify('[A-Z]{2}[0-9]{4}'),
            'document_type' => fake()->randomElement(['Certificate', 'Diploma', 'License']),
            'accreditation_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'trainer_name' => fake()->name(),
            'nationality' => fake()->country(),
            'notes' => fake()->boolean(40) ? fake()->paragraph() : null,
        ];
    }
}
