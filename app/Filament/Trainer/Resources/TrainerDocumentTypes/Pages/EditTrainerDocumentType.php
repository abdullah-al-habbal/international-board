<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerDocumentTypes\Pages;

use App\Filament\Trainer\Resources\TrainerDocumentTypes\TrainerDocumentTypeResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\EditRecord;

class EditTrainerDocumentType extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TrainerDocumentTypeResource::class;
}
