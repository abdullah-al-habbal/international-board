<?php

namespace App\Filament\Trainer\Resources\Certifications\Pages;

use App\Filament\Trainer\Resources\Certifications\CertificationResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\CreateRecord;

class CreateCertification extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = CertificationResource::class;
}
