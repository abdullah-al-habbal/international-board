<?php

namespace App\Filament\Center\Resources\Certifications\Pages;

use App\Filament\Center\Resources\Certifications\CertificationResource;
use App\Models\CertifiedCenter;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateCertification extends CreateRecord
{
    protected static string $resource = CertificationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['creator_type'] = CertifiedCenter::class;
        $data['creator_id'] = Auth::guard('certified_center')->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
