<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CertifiedCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

class CenterAccreditationRequestFactory extends Factory
{
    protected $model = \App\Models\CenterAccreditationRequest::class;

    public function definition(): array
    {
        return [
            'certified_center_id' => CertifiedCenter::factory(),
            'request_notes' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'accreditation_end_date' => fake()->dateTimeBetween('+1 year', '+2 years')->format('Y-m-d H:i:s'),
            'admin_notes' => fake()->boolean(30) ? fake()->paragraph() : null,
            'reviewed_by' => null,
            'reviewed_at' => fake()->boolean(40) ? fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d H:i:s') : null,
        ];
    }
}
