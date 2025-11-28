<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use App\Models\Trainer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trainer>
 */
class TrainerFactory extends Factory
{
    protected $model = Trainer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'bio' => $this->faker->optional()->paragraph(),
            'avatar' => $this->faker->optional()->imageUrl(200, 200, 'people'),
            'address' => $this->faker->optional()->address(),
            'country_id' => Country::inRandomOrder()->first()?->id,
            'specializations' => $this->faker->randomElements([
                'Training',
                'Consulting',
                'Leadership',
                'Management',
                'Communication',
                'Technical Skills',
                'Soft Skills',
                'Project Management',
            ], $this->faker->numberBetween(1, 4)),
            'is_active' => $this->faker->boolean(90), // 90% active
        ];
    }

    /**
     * Indicate that the trainer is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the trainer is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Trainer without contact info.
     */
    public function withoutContact(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => null,
            'phone' => null,
        ]);
    }
}
