<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CertifiedCenterPaymentAgentPerson;
use App\Models\Trainer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainerFinancialRequestFactory extends Factory
{
    public function definition(): array
    {
        $total = fake()->randomFloat(2, 100, 10000);

        return [
            'trainer_id' => Trainer::factory(),
            'agent_person_id' => CertifiedCenterPaymentAgentPerson::factory(),
            'total_payment' => $total,
            'amount_paid' => fake()->randomFloat(2, 0, $total),
            'reason' => fake()->sentence(),
            'date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
        ];
    }
}
