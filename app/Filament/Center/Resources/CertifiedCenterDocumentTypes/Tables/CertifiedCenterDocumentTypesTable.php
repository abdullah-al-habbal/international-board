<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CertifiedCenterDocumentTypes\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CertifiedCenterDocumentTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label(__('app.document_type_key'))
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name.en')
                    ->label(__('app.name_english'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name.ar')
                    ->label(__('app.name_arabic'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('app.status'))
                    ->badge()
                    ->color(fn ($state) => $state?->color() ?? 'gray')
                    ->formatStateUsing(fn ($state) => $state?->label() ?? '—'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
