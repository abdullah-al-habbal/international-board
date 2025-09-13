<?php

declare(strict_types=1);

use App\Console\Commands\AnalyzeCertificationData;
use App\Console\Commands\CheckAccreditationExpiry;
use App\Console\Commands\MigrateDocumentTypes;

return [
    CheckAccreditationExpiry::class,
    AnalyzeCertificationData::class,
    MigrateDocumentTypes::class,
];
