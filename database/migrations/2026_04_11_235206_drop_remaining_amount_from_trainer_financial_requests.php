<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trainer_financial_requests', function (Blueprint $table) {
            $table->dropColumn('remaining_amount');
        });
    }

    public function down(): void
    {
        Schema::table('trainer_financial_requests', function (Blueprint $table) {
            $table->decimal('remaining_amount', 12, 2)->default(0);
        });
    }
};
