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
        Schema::table('trainees', function (Blueprint $table) {
            $table->string('email')->nullable()->unique()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->unsignedBigInteger('country_id')->nullable()->after('phone');
            $table->date('date_of_birth')->nullable()->after('country_id');
            $table->string('nationality')->nullable()->after('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('nationality');
            $table->string('occupation')->nullable()->after('gender');
            $table->string('organization')->nullable()->after('occupation');
            $table->text('address')->nullable()->after('organization');
            $table->string('emergency_contact_name')->nullable()->after('address');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->text('medical_info')->nullable()->after('emergency_contact_phone');
            $table->text('notes')->nullable()->after('medical_info');

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainees', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn([
                'email',
                'phone',
                'country_id',
                'date_of_birth',
                'nationality',
                'gender',
                'occupation',
                'organization',
                'address',
                'emergency_contact_name',
                'emergency_contact_phone',
                'medical_info',
                'notes',
            ]);
        });
    }
};
