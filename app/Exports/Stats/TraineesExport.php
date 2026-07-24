<?php

declare(strict_types=1);

namespace App\Exports\Stats;

use App\Eloquent\Resolvers\Trainee\TraineeTraineesExportResolver;
use App\Exports\Contracts\CsvStatExportable;
use App\Models\Trainee;
use App\Services\Csv\CsvExportHandler;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class TraineesExport implements CsvStatExportable
{
    public function __construct(
        private readonly CsvExportHandler $csvExportHandler,
        private readonly TraineeTraineesExportResolver $resolver,
    ) {}

    public function export(): StreamedResponse
    {
        $headers = ['ID', 'Name', 'Email', 'Phone', 'Country', 'Date of Birth', 'Gender', 'Certifications', 'Notes', 'Created At'];

        $formatter = fn (Trainee $trainee): array => [
            $trainee->id,
            $trainee->name,
            $trainee->email,
            $trainee->phone,
            $trainee->country?->name,
            $trainee->date_of_birth,
            $trainee->gender,
            $trainee->certifications_count ?? 0,
            $trainee->notes,
            $trainee->created_at?->format('Y-m-d'),
        ];

        return $this->csvExportHandler->export(
            $this->resolver->query(),
            $headers,
            $formatter,
            'trainees_'.now()->format('Ymd_His').'.csv'
        );
    }

    public function label(): string
    {
        return 'Trainees';
    }
}
