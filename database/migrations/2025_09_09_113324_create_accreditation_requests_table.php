<?php

// database/migrations/2025_09_09_113315_create_accreditation_requests_table.php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('accreditation_requests')) {
            return;
        }

        Schema::create('accreditation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certified_center_id')->constrained()->onDelete('cascade');
            $table->dateTime('requested_start_date');
            $table->dateTime('requested_end_date');
            $table->text('request_notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('accreditation_requests');
    }
};
