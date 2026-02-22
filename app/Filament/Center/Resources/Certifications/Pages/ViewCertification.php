<?php

namespace App\Filament\Center\Resources\Certifications\Pages;

use App\Filament\Center\Resources\Certifications\CertificationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCertification extends ViewRecord
{
    protected static string $resource =  null;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
