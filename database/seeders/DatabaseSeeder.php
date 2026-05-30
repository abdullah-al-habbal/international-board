<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            DocumentTypeSeeder::class,
            UserSeeder::class,
            ApplicationSettingSeeder::class,
            CertifiedCenterSeeder::class,
            TrainerSeeder::class,
            AccreditationRequestSeeder::class,
            CertificationSeeder::class,
            BlogPostSeeder::class,
            StaticPageSeeder::class,
            MembershipSeeder::class,
        ]);
    }
}
