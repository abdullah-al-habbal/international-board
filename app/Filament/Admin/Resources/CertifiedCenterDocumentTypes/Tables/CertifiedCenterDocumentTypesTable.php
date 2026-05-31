<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CertifiedCenterDocumentTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount(['certifications' => fn ($q) => $q->whereColumn('certifications.certified_center_id', 'certified_center_document_types.certified_center_id')]))
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
                    ->formatStateUsing(fn ($record) => $record->documentType?->getTranslation('name', app()->getLocale()) ?: '—'),

                TextColumn::make('certifications_count')
                    ->label(__('app.usage_count'))
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('app.assigned_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
