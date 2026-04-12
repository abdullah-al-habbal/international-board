<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->string('accreditation_number')->nullable()->after('unique_trainer_code');
            // We reuse membership_start_date and membership_end_date and ensure they exist or are properly indexed
            if (!Schema::hasColumn('trainers', 'membership_start_date')) {
                $table->date('membership_start_date')->nullable()->after('accreditation_number');
            }
            if (!Schema::hasColumn('trainers', 'membership_end_date')) {
                $table->date('membership_end_date')->nullable()->after('membership_start_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->dropColumn(['accreditation_number']);
        });
    }
};
