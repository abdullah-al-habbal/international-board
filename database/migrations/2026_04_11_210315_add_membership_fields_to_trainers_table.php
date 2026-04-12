<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainers', function (Blueprint $table): void {
            $table->string('unique_trainer_code', 20)->unique()->nullable()->after('is_active');
            $table->date('membership_start_date')->nullable()->after('unique_trainer_code');
            $table->date('membership_end_date')->nullable()->after('membership_start_date');
        });
    }

    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table): void {
            $table->dropColumn(['unique_trainer_code', 'membership_start_date', 'membership_end_date']);
        });
    }
};
