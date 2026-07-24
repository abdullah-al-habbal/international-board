<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Trainers\Tables;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                    ->getStateUsing(fn ($record) => $record->avatar_url)
                    ->defaultImageUrl(url('assets/website/images/avatar.png'))
                    ->size(40),

                TextColumn::make('name')
                    ->label(__('app.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('accreditation_number')
                    ->label(__('app.accreditation_number'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-identification')
                    ->copyable()
                    ->placeholder('—'),

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
                    ->placeholder(__('app.no_value'))
                    ->toggleable(),

                TextColumn::make('specializations.name')
                    ->label(__('app.specializations'))
                    ->badge()
                    ->separator(',')
                    ->limitList(2)
                    ->placeholder(__('app.no_value'))
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
