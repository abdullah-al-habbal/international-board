<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StaticPageFactory extends Factory
{
    public function definition(): array
    {
        $titleEn = fake()->sentence(3);
        $titleAr = 'صفحة '.fake()->word();

        return [
            'slug' => fake()->unique()->slug(3),
            'title' => [
                'en' => $titleEn,
                'ar' => $titleAr,
            ],
            'content' => [
                'en' => fake()->paragraphs(3, true),
                'ar' => 'محتوى الصفحة '.fake()->paragraph(),
            ],
            'image' => fake()->boolean(30) ? fake()->imageUrl(800, 600) : null,
        ];
    }
}
