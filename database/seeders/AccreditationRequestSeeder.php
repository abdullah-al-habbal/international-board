<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CenterAccreditationRequest;
use App\Models\CertifiedCenter;
use Illuminate\Database\Seeder;

class AccreditationRequestSeeder extends Seeder
{
    public function run(): void
    {
        $centers = CertifiedCenter::all();

        foreach ($centers as $center) {
            CenterAccreditationRequest::factory()
                ->recycle($centers)
                ->create(['certified_center_id' => $center->id]);
        }
    }
}
