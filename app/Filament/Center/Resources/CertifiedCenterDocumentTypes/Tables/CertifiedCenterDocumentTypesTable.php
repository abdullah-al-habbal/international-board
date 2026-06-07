<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CertifiedCenterDocumentTypes\Tables;

use App\Models\DocumentType;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CertifiedCenterDocumentTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('documentType.name')
                    ->label(__('app.document_type'))
                    ->searchable()
                    ->sortable(),

                ToggleColumn::make('is_published')
                    ->label(__('app.is_published')),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
