<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CenterFinancialRequests\Tables;

use App\Filament\FinancialRequests\FinancialRequestFields;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CenterFinancialRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')->date()->sortable(),
                TextColumn::make('agentPerson.name')->label(__('app.agent_person')),
                ...FinancialRequestFields::amountColumns(),
            ])
            ->defaultSort('date', 'desc');
    }
}
