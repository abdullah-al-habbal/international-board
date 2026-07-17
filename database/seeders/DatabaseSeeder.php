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
            TrainerSeeder::class,
            TraineeSeeder::class,
            CertifiedCenterPaymentAgentPersonSeeder::class,
            CertifiedCenterDocumentTypeSeeder::class,
            CenterTypeRequestSeeder::class,
            CertifiedCenterFinancialRequestSeeder::class,
            AccreditationRequestSeeder::class,
            TrainerAccreditationRequestSeeder::class,
            TrainerDocumentTypeSeeder::class,
            TrainerFinancialRequestSeeder::class,
            CertificationSeeder::class,
        ]);
    }
}
