<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources;

use App\Filament\Trainer\Resources\TrainerFinancialRequests\Pages\ListTrainerFinancialRequests;
use App\Models\TrainerFinancialRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Trainer\Resources\TrainerFinancialRequests\Schemas\TrainerFinancialRequestForm;
use App\Filament\Trainer\Resources\TrainerFinancialRequests\Tables\TrainerFinancialRequestsTable;

class TrainerFinancialRequestResource extends Resource
{
    protected static ?string $model = TrainerFinancialRequest::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedBanknotes;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.financial_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.financial_history');
    }

    public static function getModelLabel(): string
    {
        return __('app.financial_request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.financial_history');
    }

    protected static ?int $navigationSort = 10;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('trainer_id', auth()->id());
    }

    public static function form(Schema $schema): Schema
    {
        return TrainerFinancialRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainerFinancialRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainerFinancialRequests::route('/'),
        ];
    }
}
