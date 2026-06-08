<?php

namespace App\Filament\Admin\Resources\Certifications\Pages;

use App\Filament\Admin\Resources\Certifications\CertificationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCertification extends CreateRecord
{
    protected static string $resource = CertificationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
