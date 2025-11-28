<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\DocumentTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label(__('Key'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->weight('medium'),

                TextColumn::make('name.en')
                    ->label(__('Name (English)'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name.ar')
                    ->label(__('Name (Arabic)'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('certifications_count')
                    ->label(__('Usage'))
                    ->counts('certifications')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('key', 'asc');
    }
}
