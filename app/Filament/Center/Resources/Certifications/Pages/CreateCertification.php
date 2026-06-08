<?php

namespace App\Filament\Center\Resources\Certifications\Pages;

use App\Enums\CertificationSource;
use App\Filament\Center\Resources\Certifications\CertificationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCertification extends CreateRecord
{
    protected static string $resource = CertificationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['source'] = CertificationSource::Center->value;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
