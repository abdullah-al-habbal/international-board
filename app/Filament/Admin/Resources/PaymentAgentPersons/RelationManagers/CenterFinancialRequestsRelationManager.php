<?php

namespace App\Filament\Admin\Resources\PaymentAgentPersons\RelationManagers;

use App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\CertifiedCenterFinancialRequestResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CenterFinancialRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'centerFinancialRequests';

    protected static ?string $relatedResource = CertifiedCenterFinancialRequestResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('requestable.name')
                    ->label(__('app.certified_center')),
                TextColumn::make('total_payment')
                    ->label(__('app.total_amount'))
                    ->money(fn ($record) => $record->currency?->code ?? 'USD'),
                TextColumn::make('amount_paid')
                    ->label(__('app.paid_amount'))
                    ->money(fn ($record) => $record->currency?->code ?? 'USD'),
                TextColumn::make('remaining_amount')
                    ->label(__('app.remaining_amount'))
                    ->money(fn ($record) => $record->currency?->code ?? 'USD'),
                TextColumn::make('date')
                    ->label(__('app.date'))
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('date', 'desc');
    }
}
