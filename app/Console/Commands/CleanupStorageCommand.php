<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Carbon\CarbonImmutable;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Finder\SplFileInfo;

#[Signature('cleanup:storage')]
#[Description('Delete old logs and unwanted files in storage')]
class CleanupStorageCommand extends Command
{
    private const LOG_RETENTION_DAYS = 7;

    private const TARGET_FILENAME = 't.txt';

    /**
     * Upload directory on the `public` disk => [table, column] that references it.
     * Filament stores a new file on every replacement and never removes the old
     * one, so unreferenced files accumulate here.
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private const UPLOAD_SOURCES = [
        'trainers/avatars' => ['trainers', 'avatar'],
        'centers/logos' => ['certified_centers', 'logo'],
        'users/avatars' => ['users', 'avatar'],
    ];

    /**
     * Files younger than this are never swept, so an upload that is still being
     * attached to its record cannot be deleted out from under it.
     */
    private const ORPHAN_GRACE_HOURS = 24;

    public function __construct(
        private readonly Filesystem $files
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $deleted = 0;

        $deleted += $this->cleanupLogs();
        $deleted += $this->cleanupStorageFiles();
        $deleted += $this->cleanupOrphanedUploads();

        $this->info("Cleanup finished. Files deleted: {$deleted}");

        return self::SUCCESS;
    }

    private function cleanupLogs(): int
    {
        $path = storage_path('logs');

        if (! $this->files->isDirectory($path)) {
            $this->warn("Logs path not found: {$path}");

            return 0;
        }

        return collect($this->files->files($path))
            ->filter(fn (SplFileInfo $file) => $this->isOlderThanRetention($file))
            ->reduce(fn (int $count, SplFileInfo $file) => $count + $this->safeDelete($file), 0);
    }

    private function cleanupStorageFiles(): int
    {
        $path = storage_path();

        if (! $this->files->isDirectory($path)) {
            $this->warn("Storage path not found: {$path}");

            return 0;
        }

        return collect($this->files->allFiles($path))
            ->filter(fn (SplFileInfo $file) => $this->isTargetFile($file))
            ->reduce(fn (int $count, SplFileInfo $file) => $count + $this->safeDelete($file), 0);
    }

    /**
     * Delete uploaded images on the `public` disk that no record points at any
     * more. A file is kept if any of the upload columns still references it, so
     * a path shared between records is never removed while it is in use.
     */
    private function cleanupOrphanedUploads(): int
    {
        $disk = Storage::disk('public');
        $referenced = $this->referencedUploadPaths();
        $cutoff = CarbonImmutable::now()->subHours(self::ORPHAN_GRACE_HOURS)->getTimestamp();

        return collect(array_keys(self::UPLOAD_SOURCES))
            ->flatMap(fn (string $directory): array => $disk->files($directory))
            ->reject(fn (string $path): bool => $referenced->has($path))
            ->filter(fn (string $path): bool => $disk->lastModified($path) < $cutoff)
            ->reduce(function (int $count, string $path) use ($disk): int {
                try {
                    $disk->delete($path);
                    $this->line("Deleted orphaned upload: {$path}");

                    return $count + 1;
                } catch (\Throwable $e) {
                    $this->error("Delete failed: {$path} — {$e->getMessage()}");

                    return $count;
                }
            }, 0);
    }

    /**
     * @return Collection<string, int> paths keyed for O(1) lookup
     */
    private function referencedUploadPaths(): Collection
    {
        return collect(self::UPLOAD_SOURCES)
            ->flatMap(function (array $source): array {
                [$table, $column] = $source;

                if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
                    return [];
                }

                return DB::table($table)
                    ->whereNotNull($column)
                    ->where($column, '!=', '')
                    ->pluck($column)
                    ->all();
            })
            ->unique()
            ->flip();
    }

    private function isOlderThanRetention(SplFileInfo $file): bool
    {
        $modified = CarbonImmutable::createFromTimestamp($file->getMTime());

        return $modified->diffInDays(now()) > self::LOG_RETENTION_DAYS;
    }

    private function isTargetFile(SplFileInfo $file): bool
    {
        return strtolower($file->getFilename()) === self::TARGET_FILENAME;
    }

    private function safeDelete(SplFileInfo $file): int
    {
        try {
            $this->files->delete($file->getPathname());

            return 1;
        } catch (\Throwable $e) {
            $this->error("Delete failed: {$file->getPathname()} — {$e->getMessage()}");

            return 0;
        }
    }
}
