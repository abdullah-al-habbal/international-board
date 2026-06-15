<?php

namespace App\Filament\Trainer\Resources\Certifications\Pages;

use App\Filament\Trainer\Resources\Certifications\CertificationResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\EditRecord;

class EditCertification extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = CertificationResource::class;
}
