<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CertifiedCenter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccreditationRequestSeeder extends Seeder
{
    public function run(): void
    {
        $centerIds = CertifiedCenter::pluck('id')->toArray();

        if (empty($centerIds)) {
            return;
        }

        $statuses = ['pending', 'approved', 'rejected'];

        $requests = array_map(fn () => [
            'certified_center_id' => fake()->randomElement($centerIds),
            'request_notes' => fake()->paragraph(),
            'status' => fake()->randomElement($statuses),
            'accreditation_end_date' => fake()->dateTimeBetween('+1 year', '+2 years')->format('Y-m-d H:i:s'),
            'admin_notes' => fake()->boolean(30) ? fake()->paragraph() : null,
            'reviewed_by' => null,
            'reviewed_at' => fake()->boolean(40)
                ? fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d H:i:s')
                : null,
        ], range(1, 15));

        DB::table('center_accreditation_requests')->insert($requests);
    }
}
