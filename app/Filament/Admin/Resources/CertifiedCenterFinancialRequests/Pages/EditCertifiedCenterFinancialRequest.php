<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\Pages;

use App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\CertifiedCenterFinancialRequestResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\EditRecord;

class EditCertifiedCenterFinancialRequest extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = CertifiedCenterFinancialRequestResource::class;
}
