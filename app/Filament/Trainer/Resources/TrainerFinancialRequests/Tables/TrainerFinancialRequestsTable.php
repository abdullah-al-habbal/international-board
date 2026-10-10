<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerFinancialRequests\Tables;

use App\Filament\FinancialRequests\FinancialRequestFields;
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
                ...FinancialRequestFields::amountColumns(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('date', 'desc');
    }
}
