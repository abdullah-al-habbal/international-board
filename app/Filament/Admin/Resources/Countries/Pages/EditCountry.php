<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Countries\Pages;

use App\Filament\Admin\Resources\Countries\CountryResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCountry extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
