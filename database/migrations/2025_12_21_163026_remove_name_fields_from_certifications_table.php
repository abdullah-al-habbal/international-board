<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->dropColumn(['trainee_name', 'trainer_name']);
        });
    }


    public function down(): void
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->string('trainee_name')->nullable()->after('trainee_id');
            $table->string('trainer_name')->nullable()->after('trainer_id');
        });
    }
};
