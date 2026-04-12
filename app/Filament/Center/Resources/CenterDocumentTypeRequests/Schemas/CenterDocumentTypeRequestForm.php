<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CenterDocumentTypeRequests\Schemas;

use App\Models\DocumentType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CenterDocumentTypeRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('requested_document_types')
                    ->label(__('app.document_types'))
                    ->multiple()
                    ->options(DocumentType::pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),
                Textarea::make('admin_notes')
                    ->label(__('app.admin_notes'))
                    ->disabled()
                    ->visible(fn ($record) => $record !== null),
            ]);
    }
}
