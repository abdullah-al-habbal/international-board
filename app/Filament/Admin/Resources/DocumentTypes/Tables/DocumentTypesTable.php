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

                TextColumn::make('name.en')
                    ->label(__('app.name_english'))
                    ->placeholder('(no english)')
                    ->formatStateUsing(fn($state) => $state ?: '(no english)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name.ar')
                    ->label(__('app.name_arabic'))
                    ->placeholder('(no arabic)')
                    ->formatStateUsing(fn($state) => $state ?: '(no arabic)')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('certifications_count')
                    ->label(__('app.usage_count'))
                    ->counts('certifications')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->alignCenter(),

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
