<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $centers = DB::table('certified_centers')->whereNull('accreditation_number')->get();
            foreach ($centers as $center) {
                DB::table('certified_centers')
                    ->where('id', $center->id)
                    ->update(['accreditation_number' => (string) Str::orderedUuid()]);
            }
        });

        Schema::table('certified_centers', function (Blueprint $table): void {
            $table->string('accreditation_number', 255)->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
    }
};
