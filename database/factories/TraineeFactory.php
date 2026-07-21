<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use App\Models\Trainee;
use Illuminate\Database\Eloquent\Factories\Factory;

class TraineeFactory extends Factory
{
    protected $model = Trainee::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'country_id' => Country::inRandomOrder()->first()?->id,
            'date_of_birth' => $this->faker->dateTimeBetween('-50 years', '-18 years'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'notes' => $this->faker->paragraph(),
        ];
    }

    public function complete(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'date_of_birth' => $this->faker->dateTimeBetween('-45 years', '-25 years'),
        ]);
    }

    public function minimal(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => null,
            'phone' => null,
            'date_of_birth' => null,
            'notes' => null,
        ]);
    }

    public function male(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'male',
        ]);
    }

    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'female',
        ]);
    }
}
