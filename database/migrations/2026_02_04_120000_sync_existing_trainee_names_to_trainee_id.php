<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only run if the column exists
        if (! Schema::hasColumn('certifications', 'trainee_name')) {
            return;
        }

        $certifications = DB::table('certifications')
            ->whereNotNull('trainee_name')
            ->whereNull('trainee_id')
            ->get();

        foreach ($certifications as $cert) {
            $name = trim($cert->trainee_name);

            if ($name === '') {
                continue;
            }

            $trainee = DB::table('trainees')
                ->where('name', $name)
                ->first();

            if (! $trainee) {
                $traineeId = DB::table('trainees')->insertGetId([
                    'name' => $name,
                    'country_id' => $cert->country_id ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $traineeId = $trainee->id;
            }

            DB::table('certifications')
                ->where('id', $cert->id)
                ->update(['trainee_id' => $traineeId]);
        }
    }

    public function down(): void
    {
        // Revert: set trainee_id to null where trainee was created by this migration
        // We can't reliably detect which were created, so we skip destructive down.
    }
};
