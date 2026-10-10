<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Services\Certification\CertificationImportService;
use Filament\Notifications\Notification;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use SplFileObject;

#[Tries(3)]
#[Backoff([60, 120])]
final class ImportCertificationChunkJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly string $chunkPath,
        private readonly int $creatorId,
        private readonly int $chunkIndex,
        private readonly int $totalChunks,
    ) {}

    public function handle(CertificationImportService $importService): void
    {
        Log::channel('import')->info('Import chunk started', [
            'chunk_index' => $this->chunkIndex,
            'total_chunks' => $this->totalChunks,
            'creator_id' => $this->creatorId,
            'job_id' => $this->job?->getJobId(),
        ]);

        try {
            $stats = $importService->importChunk(
                $this->readChunkRows(),
                $this->creatorId,
                $this->readChunkHeaders(),
            );

            $this->notifySuccess($stats);
        } catch (UniqueConstraintViolationException $e) {
            Log::channel('import')->error('Import chunk failed: duplicate accreditation number', [
                'chunk_index' => $this->chunkIndex,
                'total_chunks' => $this->totalChunks,
                'creator_id' => $this->creatorId,
                'exception' => $e,
                'job_id' => $this->job?->getJobId(),
            ]);

            $this->fail($e);

            return;
        }

        Log::channel('import')->info('Import chunk completed', [
            'chunk_index' => $this->chunkIndex,
            'total_chunks' => $this->totalChunks,
            'stats' => $stats,
            'memory' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'job_id' => $this->job?->getJobId(),
        ]);
    }

    private function notifySuccess(array $stats): void
    {
        $user = User::find($this->creatorId);

        if ($user) {
            Notification::make()
                ->title(__('app.import.notifications.chunk_success_title'))
                ->body(__('app.import.notifications.chunk_success_body', [
                    'index' => $this->chunkIndex + 1,
                    'total' => $this->totalChunks,
                    'imported' => $stats['success'],
                    'failed' => $stats['failed'],
                ]))
                ->success()
                ->sendToDatabase($user);
        }
    }

    private function readChunkHeaders(): array
    {
        $file = new SplFileObject($this->chunkPath);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::READ_AHEAD);
        $file->setCsvControl(',');

        foreach ($file as $row) {
            if ($row !== false && ! $this->isEmptyRow($row)) {
                return $row;
            }
        }

        return [];
    }

    private function readChunkRows(): LazyCollection
    {
        return LazyCollection::make(function (): \Generator {
            $file = new SplFileObject($this->chunkPath);
            $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::READ_AHEAD);
            $file->setCsvControl(',');

            $isHeader = true;

            foreach ($file as $row) {
                if ($row === false || $this->isEmptyRow($row)) {
                    continue;
                }

                if ($isHeader) {
                    $isHeader = false;

                    continue;
                }

                yield $row;
            }
        });
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

    public function failed(\Throwable $exception): void
    {
        Log::channel('import')->error('Import chunk failed', [
            'chunk_index' => $this->chunkIndex,
            'total_chunks' => $this->totalChunks,
            'creator_id' => $this->creatorId,
            'exception' => $exception,
            'job_id' => $this->job?->getJobId(),
        ]);

        $user = User::find($this->creatorId);

        if ($user) {
            Notification::make()
                ->title(__('app.import.notifications.chunk_failed_title'))
                ->body(__('app.import.notifications.chunk_failed_body', [
                    'index' => $this->chunkIndex + 1,
                    'total' => $this->totalChunks,
                    'message' => $exception->getMessage(),
                ]))
                ->danger()
                ->sendToDatabase($user);
        }
    }
}
