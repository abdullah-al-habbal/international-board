<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Currencies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CurrenciesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_en')
                    ->label(__('app.name_english'))
                    ->state(fn ($record) => $record->getTranslation('name', 'en') ?? __('app.no_english'))
                    ->searchable(query: fn ($query, $search) => $query->where('name->en', 'like', "%{$search}%"))
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('name->en', $direction)),

                TextColumn::make('name_ar')
                    ->label(__('app.name_arabic'))
                    ->state(fn ($record) => $record->getTranslation('name', 'ar') ?? __('app.no_arabic'))
                    ->searchable(query: fn ($query, $search) => $query->where('name->ar', 'like', "%{$search}%"))
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('name->ar', $direction)),

                TextColumn::make('code')
                    ->label(__('app.currency_code'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('symbol_en')
                    ->label(__('app.currency_symbol_english'))
                    ->state(fn ($record) => $record->getTranslation('symbol', 'en') ?? '-'),

                TextColumn::make('symbol_ar')
                    ->label(__('app.currency_symbol_arabic'))
                    ->state(fn ($record) => $record->getTranslation('symbol', 'ar') ?? '-'),

                IconColumn::make('is_default')
                    ->label(__('app.is_default_currency'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('app.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
            ->defaultSort('name->en', 'asc');
    }
}
