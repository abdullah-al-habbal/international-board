<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accreditation_requests', function (Blueprint $table): void {
            $table->index(['certified_center_id', 'status'], 'idx_center_active_request_status');
        });

        Schema::table('trainer_accreditation_requests', function (Blueprint $table): void {
            $table->index(['trainer_id', 'status'], 'idx_trainer_active_request_status');
        });
    }

    public function down(): void
    {
        Schema::table('accreditation_requests', function (Blueprint $table): void {
            $table->dropIndex('idx_center_active_request_status');
        });

        Schema::table('trainer_accreditation_requests', function (Blueprint $table): void {
            $table->dropIndex('idx_trainer_active_request_status');
        });
    }
};
