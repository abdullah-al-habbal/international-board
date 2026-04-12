<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trainers', function (Blueprint $table): void {
            if (!Schema::hasColumn('trainers', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            if (!Schema::hasColumn('trainers', 'remember_token')) {
                $table->rememberToken()->after('password');
            }
            if (!Schema::hasColumn('trainers', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table): void {
            $table->dropColumn(['password', 'remember_token', 'email_verified_at']);
        });
    }
};
