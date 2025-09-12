<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certified_centers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('manager_name')->nullable();
            $table->datetime('accreditation_period_start')->nullable();
            $table->datetime('accreditation_period_end')->nullable();
            $table->string('accreditation_number')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending', 'suspended'])->default('pending');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certified_centers');
    }
};
