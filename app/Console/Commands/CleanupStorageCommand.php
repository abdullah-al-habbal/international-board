<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

class CleanupStorageCommand extends Command
{
    protected $signature = 'cleanup:storage';

    protected $description = 'Delete old logs and unwanted files in storage';

    private const LOG_RETENTION_DAYS = 7;

    private const TARGET_FILENAME = 't.txt';

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
