<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerFinancialRequests\Pages;

use App\Filament\Trainer\Resources\TrainerFinancialRequests\TrainerFinancialRequestResource;
use Filament\Resources\Pages\EditRecord;

class EditTrainerFinancialRequest extends EditRecord
{
    protected static string $resource = TrainerFinancialRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
