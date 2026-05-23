<?php

declare(strict_types=1);

namespace App\Services\Csv;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class CsvExportHandler
{
    public function export(
        Builder $query,
        array $headers,
        callable $rowFormatter,
        string $fileName = 'export.csv',
        int $chunkSize = 500,
        string $delimiter = ',',
        string $enclosure = '"'
    ): StreamedResponse {
        return response()->streamDownload(function () use ($query, $headers, $rowFormatter, $chunkSize, $delimiter, $enclosure) {
            $output = fopen('php://output', 'w');

            if ($output === false) {
                throw new \RuntimeException('Unable to open output stream for CSV export.');
            }

            $this->writeCsvRow($output, $headers, $delimiter, $enclosure);

            $query->chunk($chunkSize, function ($items) use ($output, $rowFormatter, $delimiter, $enclosure) {
                foreach ($items as $item) {
                    $row = $rowFormatter($item);

                    if (! is_array($row)) {
                        continue;
                    }

                    $this->writeCsvRow($output, $row, $delimiter, $enclosure);
                }
            });

            fclose($output);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function writeCsvRow($stream, array $fields, string $delimiter, string $enclosure): void
    {
        static $bomWritten = false;

        if (! $bomWritten) {
            fwrite($stream, "\xEF\xBB\xBF");
            $bomWritten = true;
        }

        fputcsv($stream, $fields, $delimiter, $enclosure);
    }
}
