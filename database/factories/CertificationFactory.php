<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CertifiedCenter;
use App\Models\Trainee;
use App\Models\Trainer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'certified_center_id' => CertifiedCenter::factory(),
            'certificate_type' => fake()->randomElement(['training_certificate', 'accreditation_certificate']),
            'trainee_id' => Trainee::factory(),
            'accredited_serial_number' => fake()->unique()->regexify('[A-Z]{3}[0-9]{7}'),
            'document_code' => fake()->regexify('[A-Z]{2}[0-9]{4}'),
            'accreditation_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'trainer_id' => Trainer::factory(),
            'notes' => fake()->boolean(40) ? fake()->paragraph() : null,
        ];
    }
}
