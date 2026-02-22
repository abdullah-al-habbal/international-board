<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TrainersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label(__('Avatar'))
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png'))
                    ->size(40),

                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->getStateUsing(fn ($record) => $record->phone ?: __('app.no_value'))
                    ->toggleable(),

                TextColumn::make('country.name')
                    ->label(__('Country'))
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->country->name ?? __('app.no_value'))
                    ->toggleable(),

                TextColumn::make('specializations')
                    ->label(__('Specializations'))
                    ->badge()
                    ->separator(',')
                    ->limit(2)
                    ->getStateUsing(fn ($record) => ! empty($record->specializations) ? $record->specializations : __('app.no_value'))
                    ->toggleable(),

                TextColumn::make('certifications_count')
                    ->label(__('Certifications'))
                    ->counts('certifications')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('Active Status'))
                    ->placeholder(__('All Trainers'))
                    ->trueLabel(__('Active Only'))
                    ->falseLabel(__('Inactive Only')),

                SelectFilter::make('country_id')
                    ->label(__('Country'))
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
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
