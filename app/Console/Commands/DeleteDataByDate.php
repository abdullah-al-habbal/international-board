<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('delete:data-by-date
            {date? : Target date (Y-m-d) or timestamp (Y-m-d H:i:s)}
            {--around= : Central timestamp (Y-m-d H:i:s) to delete around}
            {--range-hours=1 : Hours before/after the central timestamp}
            {--dry-run : Show counts without deleting}
            {--exclude=* : Table names to exclude}
            {--disable-fk : Disable foreign key checks during delete}
            {--force : Required to run in production}')]
#[Description('Delete rows created or updated on a specific date/time range across all tables')]
class DeleteDataByDate extends Command
{
    protected array $dateColumns = ['created_at', 'updated_at'];

    public function handle(): int
    {
        if (app()->environment('production') && ! $this->option('force')) {
            $this->error('Refusing to run in production without --force.');
            $this->warn('This command deletes rows from every table that has a created_at/updated_at column.');
            $this->warn('Production holds live data and there are no backups. Re-run with --force only if you are certain.');

            return self::FAILURE;
        }

        $excluded = $this->option('exclude') ?: [];
        $dryRun = $this->option('dry-run');
        $disableFK = $this->option('disable-fk');

        $around = $this->option('around');
        $rangeHours = (float) $this->option('range-hours');

        if ($around) {
            try {
                $central = Carbon::parse($around);
            } catch (\Exception $e) {
                $this->error('Invalid --around format. Use "Y-m-d H:i:s", e.g. "2026-08-12 13:49:49".');

                return self::FAILURE;
            }

            $start = $central->copy()->subHours($rangeHours);
            $end = $central->copy()->addHours($rangeHours);
            $this->info("Deleting rows between {$start} and {$end}");
        } else {
            $date = $this->argument('date') ?? now()->format('Y-m-d');
            if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $this->error('Invalid date format. Use Y-m-d, e.g. 2025-08-12');

                return self::FAILURE;
            }
            $start = Carbon::parse($date)->startOfDay();
            $end = Carbon::parse($date)->endOfDay();
            $this->info("Deleting rows for the whole day: {$date}");
        }

        $database = DB::connection()->getDatabaseName();

        $tables = collect(DB::select('SHOW TABLES'))
            ->map(fn ($row) => array_values((array) $row)[0])
            ->filter(fn ($table) => ! in_array($table, $excluded, true))
            ->values();

        if ($tables->isEmpty()) {
            $this->warn('No tables found.');

            return self::SUCCESS;
        }

        $tableColumns = DB::table('information_schema.columns')
            ->select('table_name', 'column_name')
            ->where('table_schema', $database)
            ->whereIn('table_name', $tables->all())
            ->whereIn('column_name', $this->dateColumns)
            ->whereIn('data_type', ['datetime', 'timestamp', 'date'])
            ->get()
            ->groupBy('table_name')
            ->map(fn ($cols) => $cols->pluck('column_name')->all());

        if ($tableColumns->isEmpty()) {
            $this->warn('No tables with created_at/updated_at columns found.');

            return self::SUCCESS;
        }

        if ($disableFK && ! $dryRun) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $this->warn('Foreign key checks disabled.');
        }

        try {
            foreach ($tableColumns as $table => $columns) {
                $query = DB::table($table);

                if (in_array('created_at', $columns, true) && in_array('updated_at', $columns, true)) {
                    $query->where(function ($q) use ($start, $end) {
                        $q->whereBetween('created_at', [$start, $end])
                            ->orWhereBetween('updated_at', [$start, $end]);
                    });
                } elseif (in_array('created_at', $columns, true)) {
                    $query->whereBetween('created_at', [$start, $end]);
                } elseif (in_array('updated_at', $columns, true)) {
                    $query->whereBetween('updated_at', [$start, $end]);
                } else {
                    continue;
                }

                $count = (clone $query)->count();

                if ($count === 0) {
                    $this->line("<fg=gray>No rows to delete in {$table}</>");

                    continue;
                }

                if ($dryRun) {
                    $this->warn("[DRY RUN] Would delete {$count} rows from {$table}");

                    continue;
                }

                $deleted = $query->delete();
                $this->info("Deleted {$deleted} rows from {$table}");
            }
        } finally {
            if ($disableFK && ! $dryRun) {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
                $this->warn('Foreign key checks re-enabled.');
            }
        }

        $this->newLine();
        $this->info('Done.');

        return self::SUCCESS;
    }
}
