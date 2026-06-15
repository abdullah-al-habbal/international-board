<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\DocumentTypes\Pages;

use App\Filament\Admin\Resources\DocumentTypes\DocumentTypeResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentType extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = DocumentTypeResource::class;
}
