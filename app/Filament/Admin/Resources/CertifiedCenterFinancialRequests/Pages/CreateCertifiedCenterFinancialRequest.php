<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\Pages;

use App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\CertifiedCenterFinancialRequestResource;
use App\Filament\Traits\RedirectsToShowPage;
use App\Models\CertifiedCenter;
use Filament\Resources\Pages\CreateRecord;

class CreateCertifiedCenterFinancialRequest extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = CertifiedCenterFinancialRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['requestable_type'] = CertifiedCenter::class;

        return $data;
    }
}
