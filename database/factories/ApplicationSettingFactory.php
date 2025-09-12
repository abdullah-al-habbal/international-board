<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationSettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(2),
            'value' => fake()->sentence(),
            'type' => fake()->randomElement(['text', 'email', 'url', 'phone', 'number']),
        ];
    }
}
