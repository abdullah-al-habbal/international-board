<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Trainers\Tables;

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
                    ->label(__('app.avatar'))
                    ->circular()
                    ->defaultImageUrl(url('assets/website/images/avatar.png'))
                    ->size(40),

                TextColumn::make('name')
                    ->label(__('app.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label(__('app.email'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label(__('app.phone'))
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->getStateUsing(fn ($record) => $record->phone ?: __('app.no_value'))
                    ->toggleable(),

                TextColumn::make('country.name')
                    ->label(__('app.country'))
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->country->name ?? __('app.no_value'))
                    ->toggleable(),

                TextColumn::make('specializations')
                    ->label(__('app.specializations'))
                    ->badge()
                    ->separator(',')
                    ->limit(2)
                    ->getStateUsing(fn ($record) => ! empty($record->specializations) ? $record->specializations : __('app.no_value'))
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label(__('app.active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('app.active_status'))
                    ->placeholder(__('app.all_trainers'))
                    ->trueLabel(__('app.active_only'))
                    ->falseLabel(__('app.inactive_only')),

                SelectFilter::make('country_id')
                    ->label(__('app.country'))
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
