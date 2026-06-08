<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CertifiedCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertifiedCenterPaymentAgentPersonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'certified_center_id' => CertifiedCenter::factory(),
        ];
    }
}
