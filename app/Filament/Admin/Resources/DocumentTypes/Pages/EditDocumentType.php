<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\DocumentTypes\Pages;

use App\Filament\Admin\Resources\DocumentTypes\DocumentTypeResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDocumentType extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = DocumentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
