<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\Pages;

use App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\CertifiedCenterFinancialRequestResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListCertifiedCenterFinancialRequests extends ListRecords
{
    protected static string $resource = CertifiedCenterFinancialRequestResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
