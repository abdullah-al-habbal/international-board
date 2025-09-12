<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TrainerFactory extends Factory
{
    public function definition(): array
    {
        $nameEn = fake()->name();
        $nameAr = 'أ. ' . fake()->firstName();

        return [
            'name' => [
                'en' => $nameEn,
                'ar' => $nameAr,
            ],
            'bio' => [
                'en' => fake()->paragraph(2),
                'ar' => 'السيرة الذاتية ' . fake()->paragraph(),
            ],
            'address' => [
                'en' => fake()->address(),
                'ar' => 'العنوان ' . fake()->city(),
            ],
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'avatar' => fake()->boolean(40) ? fake()->imageUrl(200, 200, 'people') : null,
            'is_active' => fake()->boolean(90),
        ];
    }
}
