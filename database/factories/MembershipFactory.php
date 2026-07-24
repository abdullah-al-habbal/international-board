<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MembershipFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'slug' => Str::slug($title),
            'title' => ['en' => $title, 'ar' => 'عضوية '.fake()->word()],
            'description' => ['en' => fake()->paragraph(), 'ar' => fake()->paragraph()],
        ];
    }
}
