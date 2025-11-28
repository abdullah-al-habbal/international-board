<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $countries = DB::table('countries')->get();
        
        foreach ($countries as $country) {
            $name = $country->name;
            $nationality = $country->nationality;
            
            $maxIterations = 10; 
            $iteration = 0;
            
            while ($iteration < $maxIterations) {
                $iteration++;
                $decoded = json_decode($name, true);
                
                if (is_array($decoded) && isset($decoded['en'])) {
                    $name = $decoded['en'];
                } else if (is_string($decoded)) {
                    $name = $decoded;
                } else {
                    break;
                }
            }
            
            $iteration = 0;
            while ($iteration < $maxIterations) {
                $iteration++;
                $decoded = json_decode($nationality, true);
                
                if (is_array($decoded) && isset($decoded['en'])) {
                    $nationality = $decoded['en'];
                } else if (is_string($decoded)) {
                    $nationality = $decoded;
                } else {
                    break;
                }
            }

            $finalName = is_string($name) && str_starts_with($name, '{') ? json_decode($name, true) : $name;
            $finalNationality = is_string($nationality) && str_starts_with($nationality, '{') ? json_decode($nationality, true) : $nationality;
            
            if (is_array($finalName)) {
                $finalName = $finalName['en'] ?? (is_array($finalName) ? reset($finalName) : $name);
            }
            if (is_array($finalNationality)) {
                $finalNationality = $finalNationality['en'] ?? (is_array($finalNationality) ? reset($finalNationality) : $nationality);
            }
            
            DB::table('countries')
                ->where('id', $country->id)
                ->update([
                    'name' => $finalName,
                    'nationality' => $finalNationality
                ]);
        }
    }
    
    public function down(): void
    {
    }
};
