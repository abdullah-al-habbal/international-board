<?php
// filePath: app/Filament/Admin/Resources/CertifiedCenterDocumentTypes/Tables/CertifiedCenterDocumentTypesTable.php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CertifiedCenterDocumentTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('certifiedCenter.name')
                    ->label(__('app.certified_center'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('documentType.key')
                    ->label(__('app.document_type_key'))
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('documentType.name')
                    ->label(__('app.document_type_name'))
                    ->formatStateUsing(fn($record) => $record->documentType?->getTranslation('name', 'en')),

                TextColumn::make('documentType.certifications_count')
                    ->label(__('app.usage_count'))
                    ->counts('documentType.certifications')
                    ->badge()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label(__('app.assigned_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([])
            ->defaultSort('created_at', 'desc');
    }
}
