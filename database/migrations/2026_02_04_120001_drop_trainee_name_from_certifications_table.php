<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('certifications', 'trainee_name')) {
            return;
        }

        Schema::table('certifications', function (Blueprint $table) {
            $table->dropColumn('trainee_name');
        });
    }

    public function down(): void
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->string('trainee_name')->nullable()->after('trainee_id')->index();
        });
    }
};
