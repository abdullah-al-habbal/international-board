<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\StaticPages\Tables;

use App\Models\StaticPage;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StaticPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('app.title'))
                    ->searchable(query: function ($query, $search) {
                        return $query->where('title->'.app()->getLocale(), 'like', "%{$search}%");
                    })
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderByRaw("JSON_EXTRACT(title, '$.\"".app()->getLocale()."\"') {$direction}");
                    })
                    ->weight('bold')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('title', app()->getLocale()) ?: '—'),

                TextColumn::make('slug')
                    ->label(__('app.slug'))
                    ->searchable()
                    ->sortable(),

                ImageColumn::make('image')
                    ->label(__('app.image'))
                    ->placeholder(__('app.no_image')),

                TextColumn::make('content')
                    ->label(__('app.content'))
                    ->limit(50)
                    ->tooltip(fn (StaticPage $record): string => $record->getTranslation('content', app()->getLocale()) ?? '')
                    ->formatStateUsing(fn ($record) => $record->getTranslation('content', app()->getLocale()) ?: '—'),

                IconColumn::make('is_active')
                    ->label(__('app.is_active'))
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('app.updated_at'))
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(),
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
            ->defaultSort('created_at', 'desc');
    }
}
