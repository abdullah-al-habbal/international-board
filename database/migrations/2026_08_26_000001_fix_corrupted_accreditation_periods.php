<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';

        // --- 1. Fix specific corrupted end dates ---
        DB::table('certified_centers')
            ->where('id', 13)
            ->update(['accreditation_period_end' => '2027-08-06 00:00:00']);

        DB::table('trainers')
            ->where('id', 7)
            ->update(['accreditation_period_end' => '2027-07-31 00:00:00']);

        DB::table('trainers')
            ->where('id', 57)
            ->update(['accreditation_period_end' => '2027-08-08 00:00:00']);

        // --- 2. Strip stray time components from all accreditation period fields ---
        $tables = ['certified_centers', 'trainers'];
        $columns = ['accreditation_period_start', 'accreditation_period_end'];

        foreach ($tables as $table) {
            foreach ($columns as $column) {
                if ($isSqlite) {
                    $rows = DB::table($table)
                        ->whereNotNull($column)
                        ->where($column, 'LIKE', '% %')
                        ->select('id', $column)
                        ->get();

                    foreach ($rows as $row) {
                        $dateOnly = substr($row->$column, 0, 10);
                        DB::table($table)
                            ->where('id', $row->id)
                            ->update([$column => $dateOnly.' 00:00:00']);
                    }
                } else {
                    DB::statement(
                        "UPDATE {$table} SET {$column} = DATE({$column}) WHERE {$column} IS NOT NULL AND {$column} LIKE '% %'"
                    );
                }
            }
        }
    }

    public function down(): void
    {
        // Data-only migration — reversing would destroy the correction.
    }
};
