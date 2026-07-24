<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenters\RelationManagers;

use App\Filament\Admin\Resources\Trainers\TrainerResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrainersRelationManager extends RelationManager
{
    protected static string $relationship = 'trainers';

    protected static ?string $relatedResource = TrainerResource::class;

    protected static ?string $recordTitleAttribute = 'name';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('app.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label(__('app.email'))
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label(__('app.phone'))
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->getStateUsing(fn ($record) => $record->phone ?: __('app.no_value'))
                    ->toggleable(),

                TextColumn::make('specializations')
                    ->label(__('app.specializations'))
                    ->badge()
                    ->separator(',')
                    ->limit(2)
                    ->getStateUsing(fn ($record) => ! empty($record->specializations) ? $record->specializations : __('app.no_value'))
                    ->toggleable(),
            ])
            ->defaultSort('name');
    }
}
