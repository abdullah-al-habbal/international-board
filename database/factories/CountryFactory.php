<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->country(),
            'code' => strtoupper($this->faker->unique()->lexify('???')),
            'code_2' => strtoupper($this->faker->unique()->lexify('??')),
            'nationality' => $this->faker->optional()->word(),
            'is_active' => $this->faker->boolean(90), // 90% active
        ];
    }

    /**
     * Indicate that the country is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the country is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
