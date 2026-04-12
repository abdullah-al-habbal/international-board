<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\Pages;

use App\Filament\Admin\Resources\CertifiedCenterFinancialRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCertifiedCenterFinancialRequest extends CreateRecord
{
    protected static string $resource = CertifiedCenterFinancialRequestResource::class;
}
