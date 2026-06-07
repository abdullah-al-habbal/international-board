<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\CertifiedCenter;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $centers = DB::table('certified_centers')->whereNull('accreditation_number')->get();
            foreach ($centers as $center) {
                do {
                    $number = random_int(10000, 99999);
                    $candidate = 'IBVTQ' . $number;
                } while (CertifiedCenter::where('accreditation_number', $candidate)->exists());

                DB::table('certified_centers')
                    ->where('id', $center->id)
                    ->update(['accreditation_number' => $candidate]);
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
