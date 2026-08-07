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
            ApplicationSettingSeeder::class,
            DocumentTypeSeeder::class,
            UserSeeder::class,
            StaticPageSeeder::class,
            MembershipSeeder::class,
            BlogPostSeeder::class,
            ContactMessageSeeder::class,
            CertifiedCenterSeeder::class,
            SpecializationSeeder::class,
            TrainerSeeder::class,
            TraineeSeeder::class,
            AgentPersonSeeder::class,
            CertifiedCenterDocumentTypeSeeder::class,
            FinancialRequestSeeder::class,
            AccreditationRequestSeeder::class,
            TrainerAccreditationRequestSeeder::class,
            TrainerDocumentTypeSeeder::class,
            CertificationSeeder::class,
        ]);
    }
}
