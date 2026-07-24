<?php

declare(strict_types=1);

namespace App\Exports\Stats;

use App\Eloquent\Resolvers\Trainer\TrainerTrainersExportResolver;
use App\Exports\Contracts\CsvStatExportable;
use App\Models\Trainer;
use App\Services\Csv\CsvExportHandler;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class TrainersExport implements CsvStatExportable
{
    public function __construct(
        private readonly CsvExportHandler $csvExportHandler,
        private readonly TrainerTrainersExportResolver $resolver,
    ) {}

    public function export(): StreamedResponse
    {
        $headers = ['ID', 'Name', 'Email', 'Phone', 'Country', 'Specializations', 'Created At'];

        $formatter = fn (Trainer $trainer): array => [
            $trainer->id,
            $trainer->name,
            $trainer->email,
            $trainer->phone,
            $trainer->country?->name,
            is_array($trainer->specializations)
                ? implode(', ', $trainer->specializations)
                : (string) $trainer->specializations,
            $trainer->created_at?->format('Y-m-d'),
        ];

        return $this->csvExportHandler->export(
            $this->resolver->query(),
            $headers,
            $formatter,
            'trainers_'.now()->format('Ymd_His').'.csv'
        );
    }

    public function label(): string
    {
        return 'Trainers';
    }
}
