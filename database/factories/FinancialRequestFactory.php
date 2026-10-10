<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AgentPerson;
use App\Models\CertifiedCenter;
use App\Models\Currency;
use App\Models\FinancialRequest;
use App\Models\Trainer;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

#[UseModel(FinancialRequest::class)]
class FinancialRequestFactory extends Factory
{
    public function definition(): array
    {
        $total = fake()->randomFloat(2, 100, 10000);

        return [
            'agent_person_id' => AgentPerson::factory(),
            // Reuse the reference currency rather than minting a new one per
            // record; `currencies` is an admin-managed lookup table.
            'currency_id' => fn (): int => Currency::query()->where('is_default', true)->value('id')
                ?? Currency::query()->value('id')
                ?? Currency::factory()->default()->create()->id,
            'total_payment' => $total,
            // The domain invariant is amount_paid <= total_payment.
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
