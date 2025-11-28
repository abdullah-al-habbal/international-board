<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipFactory extends Factory
{
    public function definition(): array
    {
        $titleEn = fake()->sentence(3);
        $titleAr = 'عضوية '.fake()->words(2, true);

        return [
            'title' => [
                'en' => $titleEn,
                'ar' => $titleAr,
            ],
            'description' => [
                'en' => fake()->paragraphs(2, true),
                'ar' => 'وصف العضوية '.fake()->paragraph(),
            ],
            'descriptive_image' => fake()->boolean(60) ? fake()->imageUrl(600, 400) : null,
            'is_active' => fake()->boolean(80),
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}
