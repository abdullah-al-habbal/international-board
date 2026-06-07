<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerDocumentTypes\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class TrainerDocumentTypesTable
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
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
