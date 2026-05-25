<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CenterFinancialRequests\Pages;

use App\Filament\Center\Resources\CenterFinancialRequests\CenterFinancialRequestResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCenterFinancialRequest extends ViewRecord
{
    protected static string $resource = CenterFinancialRequestResource::class;
}
