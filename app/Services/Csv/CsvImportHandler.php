<?php

declare(strict_types=1);

namespace App\Services\Csv;

use Illuminate\Support\Facades\DB;
use SplFileObject;

final class CsvImportHandler
{
    public function import(string $filePath, callable $rowProcessor, array $options = []): array
    {
        $stats = [
            'total' => 0,
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $batchSize = $options['batch_size'] ?? 500;
        $batchInserter = $options['batch_inserter'] ?? null;
        $useTransaction = $options['transaction'] ?? true;
        $delimiter = $options['delimiter'] ?? ',';
        $headers = [];
        $dataBatch = [];
        $file = $this->openFile($filePath, $delimiter);

        if ($useTransaction) {
            DB::beginTransaction();
        }

        try {
            foreach ($file as $index => $row) {
                if ($row === false || $this->isEmptyRow($row)) {
                    continue;
                }

                if ($index === 0 && ($options['has_header'] ?? true)) {
                    $headers = $this->stripUtf8Bom($row);

                    continue;
                }

                $stats['total']++;

                try {
                    $result = $rowProcessor($row, $index, $headers);

                    if ($result === false) {
                        $stats['failed']++;
                        $stats['errors'][] = "Row {$index}: processor returned false.";

                        continue;
                    }

                    if ($batchInserter !== null && is_array($result)) {
                        $dataBatch[] = $result;

                        if (count($dataBatch) >= $batchSize) {
                            $batchInserter($dataBatch);
                            $dataBatch = [];
                        }
                    }

                    $stats['success']++;
                } catch (\Throwable $exception) {
                    $stats['failed']++;
                    $stats['errors'][] = "Row {$index}: {$exception->getMessage()}";
                }
            }

            if ($batchInserter !== null && ! empty($dataBatch)) {
                $batchInserter($dataBatch);
            }

            if ($useTransaction) {
                DB::commit();
            }
        } catch (\Throwable $exception) {
            if ($useTransaction) {
                DB::rollBack();
            }

            throw new \RuntimeException('CSV import failed: '.$exception->getMessage(), 0, $exception);
        }

        return $stats;
    }

    private function openFile(string $filePath, string $delimiter): SplFileObject
    {
        $file = new SplFileObject($filePath);
        $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::READ_AHEAD);
        $file->setCsvControl($delimiter);

        return $file;
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
}
