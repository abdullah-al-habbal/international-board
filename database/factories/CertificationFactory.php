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
            'source' => 'center',
            'certified_center_id' => CertifiedCenter::factory(),
            'trainee_id' => Trainee::factory(),
            'document_type_id' => \App\Models\DocumentType::factory(),
            'accredited_serial_number' => fake()->unique()->regexify('[A-Z]{3}[0-9]{7}'),
            'document_code' => fake()->regexify('[A-Z]{2}[0-9]{4}'),
            'accreditation_date' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'trainer_id' => Trainer::factory(),
            'country_id' => null,
            'notes' => fake()->boolean(40) ? fake()->paragraph() : null,
        ];
    }
}
