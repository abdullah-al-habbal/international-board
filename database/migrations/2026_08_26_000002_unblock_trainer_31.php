<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $trainerId = 31;
        $start = '2026-08-26 00:00:00';
        $end = '2027-08-26 00:00:00';

        if (! DB::table('trainers')->where('id', $trainerId)->exists()) {
            return;
        }

        $hasApproved = DB::table('trainer_accreditation_requests')
            ->where('trainer_id', $trainerId)
            ->where('status', 'approved')
            ->where('accreditation_end_date', '>=', now()->startOfDay())
            ->exists();

        if ($hasApproved) {
            return;
        }

        $adminId = DB::table('users')->min('id');

        DB::table('trainer_accreditation_requests')->insert([
            'trainer_id' => $trainerId,
            'status' => 'approved',
            'admin_notes' => 'Auto-created: trainer had 165 certifications but no accreditation record.',
            'reviewed_by' => $adminId,
            'reviewed_at' => now(),
            'accreditation_start_date' => $start,
            'accreditation_end_date' => $end,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('trainers')
            ->where('id', $trainerId)
            ->update([
                'accreditation_period_start' => $start,
                'accreditation_period_end' => $end,
                'updated_at' => now(),
            ]);
    }

    public function down(): void {}
};
