<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\AccreditationRequests\Pages;

use App\Filament\Center\Resources\AccreditationRequests\AccreditationRequestResource;
use App\Models\CertifiedCenter;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAccreditationRequests extends ListRecords
{
    protected static string $resource =  null;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->disabled(! AccreditationRequestResource::canCreate())
                ->tooltip($this->getCreateDisabledTooltip()),
        ];
    }

    private function getCreateDisabledTooltip(): ?string
    {
        if (AccreditationRequestResource::canCreate()) {
            return null;
        }

        /** @var CertifiedCenter|null $center */
        $center = auth('certified_center')->user();

        return $center instanceof CertifiedCenter && $center->hasActiveAccreditationRequest()
            ? __('accreditation.create_disabled.has_active')
            : null;
    }
}
