<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use App\Models\Trainer;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

#[UseModel(Trainer::class)]
class TrainerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'bio' => $this->faker->optional()->paragraph(),
            'avatar' => '',
            'address' => $this->faker->optional()->address(),
            'country_id' => Country::inRandomOrder()->first()?->id,
            'password' => 'password',
        ];
    }

    public function withoutContact(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => null,
            'phone' => null,
        ]);
    }
}
