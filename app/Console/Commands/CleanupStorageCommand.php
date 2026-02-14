<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Carbon\Carbon;

class CleanupStorageCommand extends Command
{
    protected $signature = 'cleanup:storage';
    protected $description = 'Delete log files older than 7 days and remove any t.txt files in storage';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle(): int
    {
        $now = Carbon::now();
        $deleted = 0;

        $logsPath = base_path('storage/logs');
        if ($this->files->isDirectory($logsPath)) {
            $files = $this->files->files($logsPath);
            foreach ($files as $file) {
                try {
                    $modified = Carbon::createFromTimestamp($file->getMTime());
                    if ($modified->diffInDays($now) > 7) {
                        $this->files->delete($file->getPathname());
                        $deleted++;
                    }
                } catch (\Throwable $e) {
                    $this->error('Error checking/deleting: ' . $file->getPathname() . ' — ' . $e->getMessage());
                }
            }
        } else {
            $this->info("Logs path not found: {$logsPath}");
        }

        $storagePath = base_path('storage');
        if ($this->files->isDirectory($storagePath)) {
            $all = $this->files->allFiles($storagePath);
            foreach ($all as $f) {
                if (strtolower($f->getFilename()) === 't.txt') {
                    try {
                        $this->files->delete($f->getPathname());
                        $deleted++;
                    } catch (\Throwable $e) {
                        $this->error('Error deleting t.txt: ' . $f->getPathname() . ' — ' . $e->getMessage());
                    }
                }
            }
        } else {
            $this->info("Storage path not found: {$storagePath}");
        }

        $this->info("Cleanup finished. Files deleted: {$deleted}");

        return 0;
    }
}
