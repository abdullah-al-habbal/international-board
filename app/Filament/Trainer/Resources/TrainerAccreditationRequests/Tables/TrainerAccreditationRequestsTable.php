<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerAccreditationRequests\Tables;

use App\Enums\AccreditationStatus;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrainerAccreditationRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('app.requested_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('app.status'))
                    ->badge()
                    ->color(fn (AccreditationStatus $state): string => $state->color()),
                TextColumn::make('reviewedBy.name')
                    ->label(__('app.reviewed_by'))
                    ->placeholder(__('app.no_value'))
                    ->sortable(),
                TextColumn::make('accreditation_start_date')
                    ->label(__('app.start_date'))
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('accreditation_end_date')
                    ->label(__('app.end_date'))
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('admin_notes')
                    ->label(__('app.admin_notes'))
                    ->limit(50)
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
