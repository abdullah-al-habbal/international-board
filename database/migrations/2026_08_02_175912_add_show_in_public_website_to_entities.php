<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->boolean('show_in_public_website')->default(true)->after('notes');
        });

        Schema::table('certified_centers', function (Blueprint $table) {
            $table->boolean('show_in_public_website')->default(true)->after('address');
        });

        Schema::table('trainers', function (Blueprint $table) {
            $table->boolean('show_in_public_website')->default(true)->after('bio');
        });

        Schema::table('trainees', function (Blueprint $table) {
            $table->boolean('show_in_public_website')->default(true)->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('certifications', fn (Blueprint $table) => $table->dropColumn('show_in_public_website'));
        Schema::table('certified_centers', fn (Blueprint $table) => $table->dropColumn('show_in_public_website'));
        Schema::table('trainers', fn (Blueprint $table) => $table->dropColumn('show_in_public_website'));
        Schema::table('trainees', fn (Blueprint $table) => $table->dropColumn('show_in_public_website'));
    }
};
