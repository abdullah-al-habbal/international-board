<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $titleEn = $this->faker->sentence();
        $titleAr = "عنوان افتراضي " . $this->faker->word();

        return [
            'title' => [
                'en' => $titleEn,
                'ar' => $titleAr,
            ],
            'slug' => Str::slug($titleEn),
            'excerpt' => [
                'en' => $this->faker->paragraph(1),
                'ar' => 'مقتطف قصير من المقال باللغة العربية.',
            ],
            'content' => [
                'en' => '<p>' . implode('</p><p>', $this->faker->paragraphs(3)) . '</p>',
                'ar' => '<p>محتوى المقال باللغة العربية يوضح تفاصيل الخبر.</p>',
            ],
            'is_published' => true,
            'published_at' => now(),
        ];
    }
}
