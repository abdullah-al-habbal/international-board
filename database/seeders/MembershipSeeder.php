<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Membership;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    public function run(): void
    {
        $memberships = config('data-to-seed.memberships', []);

        foreach ($memberships as $data) {
            Membership::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => [
                        'ar' => $data['title']['ar'] ?? '',
                        'en' => $data['title']['en'] ?? '',
                    ],
                    'description' => [
                        'ar' => $data['description']['ar'] ?? '',
                        'en' => $data['description']['en'] ?? '',
                    ],
                ]
            );
        }
    }
}
