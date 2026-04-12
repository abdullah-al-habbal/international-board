<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table): void {
            $table->string('code', 3)->nullable()->change();
            $table->string('code_2', 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table): void {
            $table->string('code', 3)->nullable(false)->change();
            $table->string('code_2', 2)->nullable(false)->change();
        });
    }
};
