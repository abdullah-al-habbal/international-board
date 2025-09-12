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
            'accreditation_period_start' => now()->subDays(rand(30, 365)),
            'accreditation_period_end' => now()->addDays(rand(365, 730)),
            'accreditation_number' => fake()->unique()->regexify('[A-Z]{2}[0-9]{6}'),
            'status' => fake()->randomElement(['active', 'inactive', 'pending', 'suspended']),
            'is_active' => fake()->boolean(80),
        ];
    }
}
