<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Currencies\Pages;

use App\Filament\Admin\Resources\Currencies\CurrencyResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCurrency extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
