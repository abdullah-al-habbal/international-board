<?php

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
use Illuminate\Support\Str;
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

    private const BATCH_JOBS_LIMIT = 500;

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
            $totalGroups = (int) ceil($totalChunks / self::BATCH_JOBS_LIMIT);

            self::writeChunkManifest($chunkDir, $chunkFiles);

            $userId = $this->userId;
            $filePath = $this->filePath;

            $firstBatch = self::dispatchBatchGroup(0, $totalGroups, $userId, $filePath, $chunkDir, $startedAt);

            Log::channel('import')->info('Import split dispatched', [
                'job_id' => $this->job?->getJobId(),
                'user_id' => $userId,
                'file' => $filePath,
                'batch_id' => $firstBatch->id,
                'total_chunks' => $totalChunks,
                'batch_groups' => $totalGroups,
                'elapsed_time' => round(microtime(true) - $startedAt, 2),
                'memory' => memory_get_usage(true),
            ]);
        } catch (\RuntimeException $e) {
            if (File::exists($chunkDir)) {
                File::deleteDirectory($chunkDir);
            }

            self::notifyFailure($e, $this->userId, $this->filePath);

            $this->fail($e);
        } catch (Throwable $e) {
            if (File::exists($chunkDir)) {
                File::deleteDirectory($chunkDir);
            }

            self::notifyFailure($e, $this->userId, $this->filePath);

            throw $e;
        }
    }

    private static function dispatchBatchGroup(
        int $groupNumber,
        int $totalGroups,
        int $userId,
        string $filePath,
        string $chunkDir,
        float $startedAt,
    ): Batch {
        $isLastGroup = $groupNumber === $totalGroups - 1;

        $batch = Bus::batch(self::groupJobs($chunkDir, $groupNumber, $userId))
            ->then(function (Batch $batch) use ($groupNumber, $totalGroups, $isLastGroup, $userId, $filePath, $chunkDir, $startedAt): void {
                Log::channel('import')->info('Import batch completed', [
                    'batch_id' => $batch->id,
                    'group' => $groupNumber + 1,
                    'groups_total' => $totalGroups,
                    'user_id' => $userId,
                    'pending_jobs' => $batch->pendingJobs,
                    'failed_jobs' => $batch->failedJobs,
                    'elapsed_time' => round(microtime(true) - $startedAt, 2),
                ]);

                if ($isLastGroup) {
                    self::cleanupFiles($chunkDir, $filePath);

                    self::notifySuccess($batch, $userId);

                    return;
                }

                self::dispatchBatchGroup($groupNumber + 1, $totalGroups, $userId, $filePath, $chunkDir, $startedAt);
            })
            ->catch(function (Batch $batch, Throwable $e) use ($groupNumber, $totalGroups, $userId, $filePath, $chunkDir, $startedAt): void {
                Log::channel('import')->error('Import batch failed', [
                    'batch_id' => $batch->id,
                    'group' => $groupNumber + 1,
                    'groups_total' => $totalGroups,
                    'user_id' => $userId,
                    'exception' => $e,
                    'elapsed_time' => round(microtime(true) - $startedAt, 2),
                ]);

                self::cleanupFiles($chunkDir, $filePath);

                self::notifyFailure($e, $userId, $filePath);
            })
            ->dispatch();

        return $batch;
    }

    /**
     * @return list<ImportCertificationChunkJob>
     */
    private static function groupJobs(string $chunkDir, int $groupNumber, int $userId): array
    {
        $chunkFiles = self::readChunkManifest($chunkDir);
        $start = $groupNumber * self::BATCH_JOBS_LIMIT;
        $slice = array_slice($chunkFiles, $start, self::BATCH_JOBS_LIMIT);
        $totalChunks = count($chunkFiles);
        $jobs = [];

        foreach ($slice as $offset => $chunkPath) {
            $jobs[] = new ImportCertificationChunkJob(
                $chunkPath,
                $userId,
                $start + $offset,
                $totalChunks,
            );
        }

        return $jobs;
    }

    private static function writeChunkManifest(string $chunkDir, array $chunkFiles): void
    {
        File::put($chunkDir.'/manifest.json', (string) json_encode($chunkFiles));
    }

    /**
     * @return list<string>
     */
    private static function readChunkManifest(string $chunkDir): array
    {
        $manifest = File::get($chunkDir.'/manifest.json');

        return json_decode($manifest, true) ?: [];
    }

    private static function notifySuccess(Batch $batch, int $userId): void
    {
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

        if (! is_file($this->filePath) || ! is_readable($this->filePath)) {
            throw new \RuntimeException("Import file is not a readable regular file: {$this->filePath}");
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

    private static function notifyFailure(Throwable $e, int $userId, string $filePath): void
    {
        Log::channel('import')->error('Import split failed', [
            'user_id' => $userId,
            'file' => $filePath,
            'exception' => $e,
        ]);

        $user = User::find($userId);

        if ($user) {
            Notification::make()
                ->title(__('app.import.notifications.failed_title'))
                ->body(__('app.import.notifications.failed_body', ['message' => Str::limit($e->getMessage(), 300)]))
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
