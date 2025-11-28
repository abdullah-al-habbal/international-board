<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            // Add missing columns to match the Trainer model
            $table->string('email', 255)->nullable()->after('name');
            $table->string('phone', 255)->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->foreignId('country_id')->nullable()->after('address')->constrained('countries')->nullOnDelete();
            $table->json('specializations')->nullable()->after('country_id');
            $table->boolean('is_active')->default(true)->index()->after('avatar');

            // Add index for email for searching
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropIndex(['email']);
            $table->dropColumn([
                'email',
                'phone',
                'address',
                'country_id',
                'specializations',
                'is_active',
            ]);
        });
    }
};
