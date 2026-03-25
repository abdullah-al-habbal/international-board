<?php

namespace App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class CertifiedCenterDocumentTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('certified_center_id')
                    ->relationship('certifiedCenter', 'name')
                    ->required(),
                Select::make('document_type_id')
                    ->relationship('documentType', 'name')
                    ->required(),
            ]);
    }
}
