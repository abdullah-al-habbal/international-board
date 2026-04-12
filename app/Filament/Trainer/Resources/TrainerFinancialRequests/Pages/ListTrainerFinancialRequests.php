<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerFinancialRequests\Pages;

use App\Filament\Trainer\Resources\TrainerFinancialRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListTrainerFinancialRequests extends ListRecords
{
    protected static string $resource = TrainerFinancialRequestResource::class;
}
