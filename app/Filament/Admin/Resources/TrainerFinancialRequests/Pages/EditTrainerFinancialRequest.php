<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerFinancialRequests\Pages;

use App\Filament\Admin\Resources\TrainerFinancialRequests\TrainerFinancialRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrainerFinancialRequest extends EditRecord
{
    protected static string $resource = TrainerFinancialRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
