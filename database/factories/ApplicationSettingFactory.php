<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ApplicationSetting;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

#[UseModel(ApplicationSetting::class)]
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
