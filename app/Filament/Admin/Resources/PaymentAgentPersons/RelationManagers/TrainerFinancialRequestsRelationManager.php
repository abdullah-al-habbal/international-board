<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PaymentAgentPersons\RelationManagers;

use App\Filament\Admin\Resources\TrainerFinancialRequests\TrainerFinancialRequestResource;
use App\Filament\FinancialRequests\FinancialRequestFields;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TrainerFinancialRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'trainerFinancialRequests';

    protected static ?string $relatedResource = TrainerFinancialRequestResource::class;

    public function table(Table $table): Table
    {
        return $table
            // MoneyColumn reads each record's currency, and
            // preventLazyLoading is enabled globally, so the relation has to
            // be eager-loaded here. (A static getEloquentQuery() override is
            // never called on a Filament v4 RelationManager.)
            ->modifyQueryUsing(static fn (Builder $query): Builder => $query->with('currency'))
            ->columns([
                TextColumn::make('requestable.name')
                    ->label(__('app.trainer')),
                ...FinancialRequestFields::amountColumns(),
                TextColumn::make('date')
                    ->label(__('app.date'))
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('date', 'desc');
    }
}
