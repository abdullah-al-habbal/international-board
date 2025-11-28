<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Countries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CountriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Country Name'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->nationality ?? '-'),

                TextColumn::make('code')
                    ->label(__('ISO 3'))
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('code_2')
                    ->label(__('ISO 2'))
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable()
                    ->alignCenter(),

                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
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
                TernaryFilter::make('is_active')
                    ->label(__('Active Status'))
                    ->placeholder(__('All countries'))
                    ->trueLabel(__('Active only'))
                    ->falseLabel(__('Inactive only'))
                    ->native(false),
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
            ->defaultSort('name', 'asc');
    }
}
