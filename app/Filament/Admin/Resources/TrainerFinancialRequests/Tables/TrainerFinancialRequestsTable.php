<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerFinancialRequests\Tables;

use App\Filament\FinancialRequests\FinancialRequestFields;
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
                ...FinancialRequestFields::amountColumns(),
                TextColumn::make('date')
                    ->label(__('app.date'))
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('date', 'desc');
    }
}
