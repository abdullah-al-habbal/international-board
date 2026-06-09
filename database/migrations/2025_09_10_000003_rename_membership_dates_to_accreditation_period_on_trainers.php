<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->renameColumn('membership_start_date', 'accreditation_period_start');
            $table->renameColumn('membership_end_date', 'accreditation_period_end');
        });

        Schema::table('trainers', function (Blueprint $table) {
            $table->dateTime('accreditation_period_start')->nullable()->change();
            $table->dateTime('accreditation_period_end')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->dateTime('accreditation_period_start')->nullable()->change();
            $table->dateTime('accreditation_period_end')->nullable()->change();
        });

        Schema::table('trainers', function (Blueprint $table) {
            $table->renameColumn('accreditation_period_start', 'membership_start_date');
            $table->renameColumn('accreditation_period_end', 'membership_end_date');
        });
    }
};
