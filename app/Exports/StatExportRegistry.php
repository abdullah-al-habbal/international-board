<?php

declare(strict_types=1);

namespace App\Exports;

use App\Exports\Contracts\CsvStatExportable;
use App\Exports\Stats\ActiveCentersExport;
use App\Exports\Stats\AdminUsersExport;
use App\Exports\Stats\CentersExport;
use App\Exports\Stats\CertificationsExport;
use App\Exports\Stats\ExpiredCentersExport;
use App\Exports\Stats\MonthlyCertificationsExport;
use App\Exports\Stats\PendingRequestsExport;
use App\Exports\Stats\TraineesExport;
use App\Exports\Stats\TrainersExport;
use InvalidArgumentException;

final class StatExportRegistry
{
    private const MAP = [
        'total_centers' => CentersExport::class,
        'active_centers' => ActiveCentersExport::class,
        'expired_centers' => ExpiredCentersExport::class,
        'total_certifications' => CertificationsExport::class,
        'pending_requests' => PendingRequestsExport::class,
        'admin_users' => AdminUsersExport::class,
        'trainers' => TrainersExport::class,
        'trainees' => TraineesExport::class,
        'monthly_certifications' => MonthlyCertificationsExport::class,
    ];

    public function resolve(string $type): CsvStatExportable
    {
        if (! isset(self::MAP[$type])) {
            throw new InvalidArgumentException("Unknown export type: {$type}");
        }

        return app(self::MAP[$type]);
    }

    public function isValid(string $type): bool
    {
        return isset(self::MAP[$type]);
    }
}
