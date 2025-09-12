<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code', 3)->unique(); // ISO 3166-1 alpha-3
            $table->string('code_2', 2)->unique(); // ISO 3166-1 alpha-2
            $table->string('nationality')->nullable(); // e.g., "Saudi" for Saudi Arabia
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('name');
            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
