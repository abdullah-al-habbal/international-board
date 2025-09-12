<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CertifiedCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccreditationRequestFactory extends Factory
{
    public function definition(): array
    {
        $requestedStart = fake()->dateTimeBetween('+1 month', '+3 months');
        $requestedEnd = fake()->dateTimeBetween($requestedStart, '+2 years');

        return [
            'certified_center_id' => CertifiedCenter::factory(),
            'requested_start_date' => $requestedStart,
            'requested_end_date' => $requestedEnd,
            'request_notes' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'admin_notes' => fake()->boolean(30) ? fake()->paragraph() : null,
            'reviewed_by' => null,
            'reviewed_at' => fake()->boolean(40) ? fake()->dateTimeBetween('-1 month', 'now') : null,
        ];
    }
}
