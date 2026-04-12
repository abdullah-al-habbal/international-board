<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\Schemas;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CertifiedCenterFinancialRequestTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('certifiedCenter.name')
                    ->label(__('app.certified_center'))
                    ->searchable()
                    ->sortable(),
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
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }
}
