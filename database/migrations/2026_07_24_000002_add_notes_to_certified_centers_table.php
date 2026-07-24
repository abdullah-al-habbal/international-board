<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certified_centers', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('manager_name');
        });
    }

    public function down(): void
    {
        Schema::table('certified_centers', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
