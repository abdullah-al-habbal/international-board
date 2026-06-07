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
            $table->enum('status', ['pending', 'approved', 'rejected', 'under_review'])
                ->default('pending')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('accreditation_requests', function (Blueprint $table): void {
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->change();
        });
    }
};
