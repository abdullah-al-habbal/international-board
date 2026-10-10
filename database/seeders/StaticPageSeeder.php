<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\StaticPage;
use Illuminate\Database\Seeder;

class StaticPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = config('data-to-seed.static_pages', []);

        foreach ($pages as $data) {
            StaticPage::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => [
                        'ar' => $data['title']['ar'] ?? '',
                        'en' => $data['title']['en'] ?? '',
                    ],
                    'content' => [
                        'ar' => $data['content']['ar'] ?? '',
                        'en' => $data['content']['en'] ?? '',
                    ],
                    'image' => $data['image'] ?? null,
                ]
            );
        }
    }
}
