<?php

namespace App\Filament\Admin\Resources\CertifiedCenters\RelationManagers;

use App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\CertifiedCenterFinancialRequestResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FinancialRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'financialRequests';

    protected static ?string $relatedResource = CertifiedCenterFinancialRequestResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('agentPerson.name')
                    ->label(__('app.agent_person')),
                TextColumn::make('total_payment')
                    ->label(__('app.total_amount'))
                    ->money('USD'),
                TextColumn::make('amount_paid')
                    ->label(__('app.paid_amount'))
                    ->money('USD'),
                TextColumn::make('remaining_amount')
                    ->label(__('app.remaining_amount'))
                    ->money('USD'),
                TextColumn::make('date')
                    ->label(__('app.date'))
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('date', 'desc');
    }
}
