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
        Schema::table('certified_centers', function (Blueprint $table) {
            $table->foreignId('country_id')
                ->nullable()
                ->after('address')
                ->constrained('countries')
                ->nullOnDelete();

            $table->index('country_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certified_centers', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropIndex(['country_id']);
            $table->dropColumn('country_id');
        });
    }
};
