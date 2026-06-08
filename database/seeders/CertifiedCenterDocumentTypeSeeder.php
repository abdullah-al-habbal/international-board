<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CertifiedCenter;
use App\Models\CertifiedCenterDocumentType;
use Illuminate\Database\Seeder;

class CertifiedCenterDocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $centers = CertifiedCenter::all();
        CertifiedCenterDocumentType::factory(5)->recycle($centers)->create();
    }
}
