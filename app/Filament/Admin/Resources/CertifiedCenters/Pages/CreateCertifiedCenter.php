<?php

namespace App\Filament\Admin\Resources\CertifiedCenters\Pages;

use App\Filament\Admin\Resources\CertifiedCenters\CertifiedCenterResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\CreateRecord;

class CreateCertifiedCenter extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = CertifiedCenterResource::class;
}
