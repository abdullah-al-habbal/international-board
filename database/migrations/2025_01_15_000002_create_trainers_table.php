<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->json('address')->nullable();
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('set null');
            $table->json('specializations')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('name');
            $table->index('email');
            $table->index('is_active');
            $table->index('country_id');

            $table->check('LENGTH(name) >= 2', 'chk_trainer_name_length');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};
