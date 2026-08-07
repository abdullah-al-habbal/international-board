<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerFinancialRequests\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrainerFinancialRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('requestable.name')
                    ->label(__('app.trainer')),
                TextColumn::make('agentPerson.name')
                    ->label(__('app.agent_person'))
                    ->sortable(),
                TextColumn::make('total_payment')
                    ->label(__('app.total_amount'))
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('amount_paid')
                    ->label(__('app.paid_amount'))
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('remaining_amount')
                    ->label(__('app.remaining_amount'))
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('date')
                    ->label(__('app.date'))
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('date', 'desc');
    }
}
