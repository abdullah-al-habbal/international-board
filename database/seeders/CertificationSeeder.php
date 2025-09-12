<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\CertifiedCenter;
use Illuminate\Database\Seeder;

class CertificationSeeder extends Seeder
{
    public function run(): void
    {
        $centerIds = CertifiedCenter::pluck('id')->toArray();

        if (empty($centerIds)) {
            return;
        }

        $certifications = Certification::factory()
            ->count(50)
            ->make()
            ->map(function ($certification) use ($centerIds) {
                $certification->certified_center_id = fake()->randomElement($centerIds);
                return $certification->toArray();
            })
            ->toArray();

        collect($certifications)->chunk(25)->each(function ($chunk) {
            Certification::insert($chunk->toArray());
        });
    }
}
