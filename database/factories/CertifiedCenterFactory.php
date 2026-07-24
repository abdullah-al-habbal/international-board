<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CertifiedCenterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'email' => fake()->unique()->companyEmail(),
            'password' => bcrypt('password'),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'manager_name' => fake()->name(),
            'notes' => fake()->paragraph(),
            'accreditation_period_start' => now()->subDays(rand(30, 365)),
            'accreditation_period_end' => now()->addDays(rand(365, 730)),
            'status' => fake()->randomElement(['active', 'inactive', 'pending', 'suspended']),
        ];
    }
}
