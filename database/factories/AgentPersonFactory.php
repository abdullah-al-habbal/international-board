<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AgentPerson;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

#[UseModel(AgentPerson::class)]
class AgentPersonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
        ];
    }
}
