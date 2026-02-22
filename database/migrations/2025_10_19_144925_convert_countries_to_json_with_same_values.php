<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $countries = DB::table('countries')->get();

        // Store data
        $countryData = [];
        foreach ($countries as $country) {
            $countryData[] = [
                'id' => $country->id,
                'name' => is_string($country->name) ? $country->name : 'Unknown',
                'nationality' => is_string($country->nationality) ? $country->nationality : 'Unknown',
            ];
        }

        try {
            Schema::table('countries', function (Blueprint $table) {
                $table->dropUnique(['name']);
            });
        } catch (\Exception $e) {
        }

        Schema::table('countries', function (Blueprint $table) {
            $table->json('name_json')->nullable()->after('name');
            $table->json('nationality_json')->nullable()->after('nationality');
        });

        foreach ($countryData as $data) {
            DB::table('countries')
                ->where('id', $data['id'])
                ->update([
                    'name_json' => json_encode(['en' => $data['name'], 'ar' => $data['name']]),
                    'nationality_json' => json_encode(['en' => $data['nationality'], 'ar' => $data['nationality']]),
                ]);
        }

        // Step 5: Drop old varchar columns
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['name', 'nationality']);
        });

        // Step 6: Rename JSON columns to final names
        Schema::table('countries', function (Blueprint $table) {
            $table->renameColumn('name_json', 'name');
            $table->renameColumn('nationality_json', 'nationality');
        });
    }

    public function down(): void
    {
        // Get all existing data
        $countries = DB::table('countries')->get();

        // Add temporary varchar columns
        Schema::table('countries', function (Blueprint $table) {
            $table->string('name_varchar')->nullable()->after('name');
            $table->string('nationality_varchar')->nullable()->after('nationality');
        });

        // Extract English values from JSON
        foreach ($countries as $country) {
            $nameData = json_decode($country->name, true);
            $nationalityData = json_decode($country->nationality, true);

            DB::table('countries')
                ->where('id', $country->id)
                ->update([
                    'name_varchar' => $nameData['en'] ?? 'Unknown',
                    'nationality_varchar' => $nationalityData['en'] ?? 'Unknown',
                ]);
        }

        // Drop JSON columns
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['name', 'nationality']);
        });

        // Rename varchar columns
        Schema::table('countries', function (Blueprint $table) {
            $table->renameColumn('name_varchar', 'name');
            $table->renameColumn('nationality_varchar', 'nationality');
        });

        // Re-add unique constraint
        Schema::table('countries', function (Blueprint $table) {
            $table->unique('name');
        });
    }
};
