<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerFinancialRequests\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrainerFinancialRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')->date()->sortable(),
                TextColumn::make('agentPerson.name')->label(__('app.agent_person')),
                TextColumn::make('total_payment')->money(fn ($record) => $record->currency?->code ?? 'USD'),
                TextColumn::make('amount_paid')->money(fn ($record) => $record->currency?->code ?? 'USD'),
                TextColumn::make('remaining_amount')->money(fn ($record) => $record->currency?->code ?? 'USD'),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('date', 'desc');
    }
}
