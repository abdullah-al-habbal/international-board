<?php
// filePath: app/Filament/Admin/Resources/CenterDocumentTypeRequests/Pages/EditCenterDocumentTypeRequest.php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CenterDocumentTypeRequests\Pages;

use App\Filament\Admin\Resources\CenterDocumentTypeRequests\CenterDocumentTypeRequestResource;
use App\Filament\Admin\Resources\CenterDocumentTypeRequests\Forms\CenterDocumentTypeRequestForm;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditCenterDocumentTypeRequest extends EditRecord
{
    protected static string $resource = CenterDocumentTypeRequestResource::class;

    public function form(Schema $schema): Schema
    {
        return CenterDocumentTypeRequestForm::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
