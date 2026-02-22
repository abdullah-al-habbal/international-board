<?php

namespace App\Filament\Admin\Resources\Certifications\Pages;

use App\Filament\Admin\Resources\Certifications\CertificationResource;
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
