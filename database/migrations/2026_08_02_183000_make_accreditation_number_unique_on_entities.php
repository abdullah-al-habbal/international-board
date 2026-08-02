<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Accreditation numbers must be unique so a public verification URL
        // resolves to exactly one certification. Keep the earliest row per
        // number and drop the duplicates.
        $duplicateNumbers = DB::table('certifications')
            ->select('accreditation_number')
            ->whereNotNull('accreditation_number')
            ->groupBy('accreditation_number')
            ->havingRaw('count(*) > 1')
            ->pluck('accreditation_number');

        foreach ($duplicateNumbers as $number) {
            $keepId = DB::table('certifications')
                ->where('accreditation_number', $number)
                ->min('id');

            DB::table('certifications')
                ->where('accreditation_number', $number)
                ->where('id', '!=', $keepId)
                ->delete();
        }

        Schema::table('certifications', function (Blueprint $table): void {
            $table->unique('accreditation_number');
        });

        Schema::table('trainers', function (Blueprint $table): void {
            $table->unique('accreditation_number');
        });
    }

    public function down(): void
    {
        Schema::table('certifications', function (Blueprint $table): void {
            $table->dropUnique(['accreditation_number']);
        });

        Schema::table('trainers', function (Blueprint $table): void {
            $table->dropUnique(['accreditation_number']);
        });
    }
};
