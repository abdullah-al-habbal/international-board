<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CertifiedCenter;
use App\Models\CertifiedCenterPaymentAgentPerson;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertifiedCenterFinancialRequestFactory extends Factory
{
    public function definition(): array
    {
        $total = fake()->randomFloat(2, 100, 10000);

        return [
            'certified_center_id' => CertifiedCenter::factory(),
            'agent_person_id' => CertifiedCenterPaymentAgentPerson::factory(),
            'total_payment' => $total,
            'amount_paid' => fake()->randomFloat(2, 0, $total),
            'reason' => fake()->sentence(),
            'date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
        ];
    }
}
