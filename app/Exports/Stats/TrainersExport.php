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
        $headers = [
            __('exports.headers.id'), __('exports.headers.name'), __('exports.headers.email'), __('exports.headers.phone'),
            __('exports.headers.country'), __('exports.headers.center'), __('exports.headers.accreditation_number'),
            __('exports.headers.specializations'), __('exports.headers.created_at'),
        ];

        $formatter = fn (Trainer $trainer): array => [
            $trainer->id,
            $trainer->name,
            $trainer->email,
            $trainer->phone,
            $trainer->country?->name,
            $trainer->center?->name,
            $trainer->accreditation_number,
            $trainer->specializations->pluck('name')->implode(', '),
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
        return __('exports.titles.trainers');
    }
}
