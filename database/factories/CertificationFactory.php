<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CertifiedCenterDocumentType;
use App\Models\Country;
use App\Models\DocumentType;
use App\Models\Trainee;
use App\Models\TrainerDocumentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificationFactory extends Factory
{
    public function definition(): array
    {
        $documentableType = fake()->randomElement([
            DocumentType::class,
            TrainerDocumentType::class,
            CertifiedCenterDocumentType::class,
        ]);

        return [
            'creator_type' => User::class,
            'creator_id' => User::factory(),
            'trainee_id' => Trainee::factory(),
            'documentable_type' => $documentableType,
            'documentable_id' => $documentableType::factory(),
            'accredited_serial_number' => fake()->unique()->regexify('[A-Z]{3}[0-9]{7}'),
            'document_code' => fake()->regexify('[A-Z]{2}[0-9]{4}'),
            'accreditation_date' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'country_id' => Country::factory(),
            'notes' => fake()->boolean(40) ? fake()->paragraph() : null,
        ];
    }
}
