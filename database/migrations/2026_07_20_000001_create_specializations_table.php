<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specializations', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('specialization_trainer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialization_trainer');
        Schema::dropIfExists('specializations');
    }
};
