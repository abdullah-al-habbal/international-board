<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('accreditation_requests', function (Blueprint $table): void {
            if (!Schema::hasColumn('accreditation_requests', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('accreditation_requests', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->after('reviewed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('accreditation_requests', function (Blueprint $table): void {
            if (Schema::hasColumn('accreditation_requests', 'reviewed_by')) {
                $table->dropForeign(['reviewed_by']);
                $table->dropColumn('reviewed_by');
            }
            if (Schema::hasColumn('accreditation_requests', 'reviewed_at')) {
                $table->dropColumn('reviewed_at');
            }
        });
    }
};
