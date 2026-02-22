<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Countries\Pages;

use App\Filament\Admin\Resources\Countries\CountryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCountry extends ViewRecord
{
    protected static string $resource =  null;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
