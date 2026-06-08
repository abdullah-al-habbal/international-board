<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Config-based seeders (deterministic data)
            CountrySeeder::class,
            ApplicationSettingSeeder::class,
            DocumentTypeSeeder::class,

            // Independent models
            UserSeeder::class,
            StaticPageSeeder::class,
            MembershipSeeder::class,
            BlogPostSeeder::class,
            ContactMessageSeeder::class,

            // Models depending on basic data
            CertifiedCenterSeeder::class,
            TrainerSeeder::class,
            TraineeSeeder::class,

            // Center-dependent models
            CertifiedCenterPaymentAgentPersonSeeder::class,
            CertifiedCenterDocumentTypeSeeder::class,
            CenterTypeRequestSeeder::class,
            CertifiedCenterFinancialRequestSeeder::class,
            AccreditationRequestSeeder::class,

            // Trainer-dependent models
            TrainerAccreditationRequestSeeder::class,
            TrainerDocumentTypeSeeder::class,
            TrainerFinancialRequestSeeder::class,

            // Models depending on everything above
            CertificationSeeder::class,
        ]);
    }
}
