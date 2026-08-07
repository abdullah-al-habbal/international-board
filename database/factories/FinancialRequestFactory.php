<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AgentPerson;
use App\Models\CertifiedCenter;
use App\Models\Trainer;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialRequestFactory extends Factory
{
    public function definition(): array
    {
        $total = fake()->randomFloat(2, 100, 10000);

        return [
            'agent_person_id' => AgentPerson::factory(),
            'total_payment' => $total,
            'amount_paid' => fake()->randomFloat(2, 0, $total),
            'reason' => fake()->sentence(),
            'date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
        ];
    }

    public function forCenter(): static
    {
        return $this->for(CertifiedCenter::factory(), 'requestable');
    }

    public function forTrainer(): static
    {
        return $this->for(Trainer::factory(), 'requestable');
    }
}
