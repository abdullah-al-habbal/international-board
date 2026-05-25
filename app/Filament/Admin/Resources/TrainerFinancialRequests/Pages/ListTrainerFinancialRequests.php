<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerFinancialRequests\Pages;

use App\Filament\Admin\Resources\TrainerFinancialRequests\TrainerFinancialRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrainerFinancialRequests extends ListRecords
{
    protected static string $resource = TrainerFinancialRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
