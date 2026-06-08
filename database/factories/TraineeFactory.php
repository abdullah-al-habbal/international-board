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
            'nationality' => $this->faker->country(),
            'country_id' => Country::inRandomOrder()->first()?->id,
            'date_of_birth' => $this->faker->dateTimeBetween('-50 years', '-18 years'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'occupation' => $this->faker->jobTitle(),
            'organization' => $this->faker->company(),
            'address' => $this->faker->address(),
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_phone' => $this->faker->phoneNumber(),
            'medical_info' => $this->faker->paragraph(),
            'notes' => $this->faker->paragraph(),
        ];
    }

    /**
     * Trainee with complete profile.
     */
    public function complete(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'date_of_birth' => $this->faker->dateTimeBetween('-45 years', '-25 years'),
            'occupation' => $this->faker->jobTitle(),
            'organization' => $this->faker->company(),
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_phone' => $this->faker->phoneNumber(),
        ]);
    }

    /**
     * Trainee without contact info.
     */
    public function minimal(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => null,
            'phone' => null,
            'date_of_birth' => null,
            'occupation' => null,
            'organization' => null,
            'emergency_contact_name' => null,
            'emergency_contact_phone' => null,
            'medical_info' => null,
            'notes' => null,
        ]);
    }

    /**
     * Male trainee.
     */
    public function male(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'male',
        ]);
    }

    /**
     * Female trainee.
     */
    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'female',
        ]);
    }
}
