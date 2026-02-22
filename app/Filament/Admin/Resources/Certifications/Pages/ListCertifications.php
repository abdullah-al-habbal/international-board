<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Certifications\Pages;

use App\Filament\Admin\Resources\Certifications\CertificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListCertifications extends ListRecords
{
    protected static string $resource =  null;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('app.create_certification')),
        ];
    }
}
