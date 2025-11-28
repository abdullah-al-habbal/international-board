<?php

// database/migrations/2025_09_09_113323_create_certified_centers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('certified_centers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->text('address')->nullable();
            $table->string('phone', 255)->nullable();
            $table->string('manager_name', 255)->nullable();
            $table->dateTime('accreditation_period_start')->nullable();
            $table->dateTime('accreditation_period_end')->nullable();
            $table->string('accreditation_number', 255)->nullable();
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
