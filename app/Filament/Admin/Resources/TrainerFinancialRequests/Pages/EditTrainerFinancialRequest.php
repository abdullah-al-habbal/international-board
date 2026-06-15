<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerFinancialRequests\Pages;

use App\Filament\Admin\Resources\TrainerFinancialRequests\TrainerFinancialRequestResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrainerFinancialRequest extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TrainerFinancialRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
