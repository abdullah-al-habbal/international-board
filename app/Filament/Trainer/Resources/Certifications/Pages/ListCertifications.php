<?php

namespace App\Filament\Trainer\Resources\Certifications\Pages;

use App\Filament\Trainer\Resources\Certifications\CertificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCertifications extends ListRecords
{
    protected static string $resource = CertificationResource::class;

    public function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('app.add_certification')),
        ];
    }
}
