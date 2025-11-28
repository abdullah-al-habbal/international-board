<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AccreditationRequest;
use App\Models\CertifiedCenter;
use Illuminate\Database\Seeder;

class AccreditationRequestSeeder extends Seeder
{
    public function run(): void
    {
        $centerIds = CertifiedCenter::pluck('id')->toArray();

        if (empty($centerIds)) {
            return;
        }

        $requests = AccreditationRequest::factory()
            ->count(15)
            ->make()
            ->map(function ($request) use ($centerIds) {
                $request->certified_center_id = fake()->randomElement($centerIds);

                return $request->toArray();
            })
            ->toArray();

        AccreditationRequest::insert($requests);
    }
}
