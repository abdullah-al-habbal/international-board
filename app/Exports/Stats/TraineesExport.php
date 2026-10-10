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
        $headers = [
            __('exports.headers.id'), __('exports.headers.name'), __('exports.headers.email'), __('exports.headers.phone'),
            __('exports.headers.country'), __('exports.headers.date_of_birth'), __('exports.headers.gender'),
            __('exports.headers.certifications'), __('exports.headers.notes'), __('exports.headers.created_at'),
        ];

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
        return __('exports.titles.trainees');
    }
}
