<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->unique();
            $table->string('email', 255)->nullable()->index();
            $table->string('phone', 255)->nullable();
            $table->json('address')->nullable();
            $table->foreignId('country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete();
            $table->json('specializations')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->string('unique_trainer_code', 20)->unique()->nullable();
            $table->string('accreditation_number')->nullable();
            $table->date('membership_start_date')->nullable();
            $table->date('membership_end_date')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
}