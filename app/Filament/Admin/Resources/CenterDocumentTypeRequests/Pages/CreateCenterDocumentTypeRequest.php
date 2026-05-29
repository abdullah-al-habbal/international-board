<?php
// filePath: app/Filament/Admin/Resources/CenterDocumentTypeRequests/Pages/CreateCenterDocumentTypeRequest.php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CenterDocumentTypeRequests\Pages;

use App\Filament\Admin\Resources\CenterDocumentTypeRequests\CenterDocumentTypeRequestResource;
use App\Filament\Admin\Resources\CenterDocumentTypeRequests\Schemas\CenterDocumentTypeRequestForm;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateCenterDocumentTypeRequest extends CreateRecord
{
    protected static string $resource = CenterDocumentTypeRequestResource::class;

    public function form(Schema $schema): Schema
    {
        return CenterDocumentTypeRequestForm::configure($schema);
    }
}
