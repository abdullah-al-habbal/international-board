<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_roles', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->timestamps();
        });

        Schema::table('trainers', function (Blueprint $table) {
            $table->foreignId('trainer_role_id')
                ->nullable()
                ->after('country_id')
                ->constrained('trainer_roles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('trainer_role_id');
        });

        Schema::dropIfExists('trainer_roles');
    }
};
