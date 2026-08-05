<?php

// filePath: app/Jobs/ImportCertificationsJob.php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Services\Certification\CertificationImportService;
use Filament\Notifications\Notification;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Throwable;

class ImportCertificationsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 120];

    private const CHUNK_SIZE = 1000;

    public function __construct(
        public readonly string $filePath,
        public readonly int $userId,
    ) {}

    public function handle(CertificationImportService $importService): void
    {
        $startedAt = microtime(true);
        $chunkDir = $this->chunkDir();

        Log::channel('import')->info('Import split started', [
            'job_id' => $this->job?->getJobId(),
            'user_id' => $this->userId,
            'file' => $this->filePath,
            'attempt' => $this->attempts(),
        ]);

        try {
            $chunkFiles = $this->splitIntoChunkFiles($chunkDir);
            $totalChunks = count($chunkFiles);
            $jobs = [];

            foreach ($chunkFiles as $chunkIndex => $chunkPath) {
                $jobs[] = new ImportCertificationChunkJob(
                    $chunkPath,
                    $this->userId,
                    $chunkIndex,
                    $totalChunks,
                );
            }

            $userId = $this->userId;
            $filePath = $this->filePath;

            $batch = Bus::batch($jobs)
                ->then(function (Batch $batch) use ($userId, $filePath, $chunkDir, $startedAt): void {
                    Log::channel('import')->info('Import batch completed', [
                        'batch_id' => $batch->id,
                        'user_id' => $userId,
                        'file' => $filePath,
                        'pending_jobs' => $batch->pendingJobs,
                        'failed_jobs' => $batch->failedJobs,
                        'elapsed_time' => round(microtime(true) - $startedAt, 2),
                    ]);

                    self::cleanupFiles($chunkDir, $filePath);

                    $user = User::find($userId);

                    if ($user) {
                        Notification::make()
                            ->title(__('app.import.notifications.success_title'))
                            ->body(__('app.import.notifications.success_body', [
                                'imported' => $batch->totalJobs - $batch->failedJobs,
                                'failed' => $batch->failedJobs,
                            ]))
                            ->success()
                            ->sendToDatabase($user);
                    }
                })
                ->catch(function (Batch $batch, Throwable $e) use ($userId, $filePath, $chunkDir, $startedAt): void {
                    Log::channel('import')->error('Import batch failed', [
                        'batch_id' => $batch->id,
                        'user_id' => $userId,
                        'file' => $filePath,
                        'exception' => $e,
                        'elapsed_time' => round(microtime(true) - $startedAt, 2),
                    ]);

                    self::cleanupFiles($chunkDir, $filePath);

                    $user = User::find($userId);

                    if ($user) {
                        Notification::make()
                            ->title(__('app.import.notifications.failed_title'))
                            ->body(__('app.import.notifications.failed_body', ['message' => $e->getMessage()]))
                            ->danger()
                            ->sendToDatabase($user);
                    }
                })
                ->dispatch();

            Log::channel('import')->info('Import split dispatched', [
                'job_id' => $this->job?->getJobId(),
                'user_id' => $this->userId,
                'file' => $this->filePath,
                'batch_id' => $batch->id,
                'total_chunks' => $totalChunks,
                'elapsed_time' => round(microtime(true) - $startedAt, 2),
                'memory' => memory_get_usage(true),
            ]);
        } catch (Throwable $e) {
            if (File::exists($chunkDir)) {
                File::deleteDirectory($chunkDir);
            }

            $this->notifyFailure($e);

            throw $e;
        }
    }

    /**
     * Streams the CSV and writes each chunk to its own file so the queued
     * job payload stays small regardless of the source file size.
     *
     * @return string[] Absolute paths of the generated chunk files.
     */
    private function splitIntoChunkFiles(string $chunkDir): array
    {
        if (! File::makeDirectory($chunkDir, 0755, true)) {
            throw new \RuntimeException("Unable to create chunk directory: {$chunkDir}");
        }

        $file = new \SplFileObject($this->filePath);
        $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY | \SplFileObject::READ_AHEAD);
        $file->setCsvControl(',');

        $headers = [];
        $chunkFiles = [];
        $chunkIndex = 0;
        $rowsInChunk = 0;
        $isHeader = true;
        $handle = null;

        foreach ($file as $row) {
            if ($row === false || $this->isEmptyRow($row)) {
                continue;
            }

            if ($isHeader) {
                $headers = $this->stripUtf8Bom($row);
                $isHeader = false;

                continue;
            }

            if ($handle === null) {
                $chunkIndex++;
                $handle = $this->openChunkFile($chunkDir.'/chunk-'.$chunkIndex.'.csv', $headers);
                $rowsInChunk = 0;
            }

            fputcsv($handle, array_values($row));
            $rowsInChunk++;

            if ($rowsInChunk >= self::CHUNK_SIZE) {
                fclose($handle);
                $chunkFiles[] = $chunkDir.'/chunk-'.$chunkIndex.'.csv';
                $handle = null;
            }
        }

        if ($handle !== null) {
            fclose($handle);
            $chunkFiles[] = $chunkDir.'/chunk-'.$chunkIndex.'.csv';
        }

        if (empty($chunkFiles)) {
            throw new \RuntimeException('No importable rows found in the CSV file.');
        }

        return $chunkFiles;
    }

    /**
     * @return resource
     */
    private function openChunkFile(string $path, array $headers): mixed
    {
        $handle = fopen($path, 'w');

        if ($handle === false) {
            throw new \RuntimeException("Unable to open chunk file for writing: {$path}");
        }

        fputcsv($handle, array_values($headers));

        return $handle;
    }

    private function chunkDir(): string
    {
        return storage_path('app/import_chunks/'.md5($this->filePath));
    }

    private static function cleanupFiles(string $chunkDir, string $filePath): void
    {
        if (File::exists($chunkDir)) {
            File::deleteDirectory($chunkDir);
        }

        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }

    private function notifyFailure(Throwable $e): void
    {
        Log::channel('import')->error('Import split failed', [
            'job_id' => $this->job?->getJobId(),
            'user_id' => $this->userId,
            'file' => $this->filePath,
            'exception' => $e,
        ]);

        $user = User::find($this->userId);

        if ($user) {
            Notification::make()
                ->title(__('app.import.notifications.failed_title'))
                ->body(__('app.import.notifications.failed_body', ['message' => $e->getMessage()]))
                ->danger()
                ->sendToDatabase($user);
        }
    }

    private function stripUtf8Bom(array $row): array
    {
        if (! empty($row) && isset($row[0]) && is_string($row[0])) {
            $row[0] = preg_replace('/^\x{FEFF}/u', '', $row[0]);
        }

        return $row;
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $cell) {
            if ($cell !== null && $cell !== '') {
                return false;
            }
        }

        return true;
    }

    public function failed(?Throwable $e): void
    {
        Log::channel('import')->error('Import exhausted all retries', [
            'job_id' => $this->job?->getJobId(),
            'user_id' => $this->userId,
            'file' => $this->filePath,
            'exception' => $e,
        ]);

        self::cleanupFiles($this->chunkDir(), $this->filePath);
    }
}
