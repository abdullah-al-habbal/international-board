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
                    ->label(__('app.key'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->weight('medium')
                    ->extraAttributes(['class' => 'text-lg font-semibold']),

                TextColumn::make('name_en')
                    ->label(__('app.name_english'))
                    ->state(fn ($record) => $record->getTranslation('name', 'en') ?? '(no english)')
                    ->searchable(query: fn ($query, $search) => $query->where('name->en', 'like', "%{$search}%"))
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('name->en', $direction)),

                TextColumn::make('name_ar')
                    ->label(__('app.name_arabic'))
                    ->state(fn ($record) => $record->getTranslation('name', 'ar') ?? '(no arabic)')
                    ->searchable(query: fn ($query, $search) => $query->where('name->ar', 'like', "%{$search}%"))
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('name->ar', $direction)),

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
            ->filters([])
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
