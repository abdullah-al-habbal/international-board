<?php

namespace App\Filament\Admin\Resources\CenterAccreditationRequests\Pages;

use App\Filament\Admin\Resources\CenterAccreditationRequests\CenterAccreditationRequestResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\CreateRecord;

class CreateCenterAccreditationRequest extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = CenterAccreditationRequestResource::class;
}
