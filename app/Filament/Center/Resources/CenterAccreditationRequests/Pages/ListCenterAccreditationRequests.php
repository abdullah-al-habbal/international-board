<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CenterAccreditationRequests\Pages;

use App\Filament\Center\Resources\CenterAccreditationRequests\CenterAccreditationRequestResource;
use App\Models\CertifiedCenter;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCenterAccreditationRequests extends ListRecords
{
    protected static string $resource = CenterAccreditationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->disabled(! CenterAccreditationRequestResource::canCreate())
                ->tooltip($this->getCreateDisabledTooltip()),
        ];
    }

    private function getCreateDisabledTooltip(): ?string
    {
        if (CenterAccreditationRequestResource::canCreate()) {
            return null;
        }

        /** @var CertifiedCenter|null $center */
        $center = auth('certified_center')->user();

        return $center instanceof CertifiedCenter && $center->hasActiveAccreditationRequest()
            ? __('accreditation.create_disabled.has_active')
            : null;
    }
}
