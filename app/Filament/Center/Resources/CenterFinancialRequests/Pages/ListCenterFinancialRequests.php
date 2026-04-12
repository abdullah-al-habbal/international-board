<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CenterFinancialRequests\Pages;

use App\Filament\Center\Resources\CenterFinancialRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListCenterFinancialRequests extends ListRecords
{
    protected static string $resource = CenterFinancialRequestResource::class;
}
