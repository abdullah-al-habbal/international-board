<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerFinancialRequests;

use App\Filament\Admin\Resources\TrainerFinancialRequests\Pages;
use App\Models\TrainerFinancialRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Admin\Resources\TrainerFinancialRequests\Schemas\TrainerFinancialRequestForm;
use App\Filament\Admin\Resources\TrainerFinancialRequests\Tables\TrainerFinancialRequestsTable;

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
        return __('app.trainer_financial_requests');
    }

    public static function getModelLabel(): string
    {
        return __('app.trainer_financial_request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.trainer_financial_requests');
    }

    public static function getRecordTitle(?\Illuminate\Database\Eloquent\Model $record): string
    {
        if (!$record) {
            return static::getModelLabel();
        }

        return $record->trainer?->name ?? 'Request #' . $record->id;
    }

    protected static ?int $navigationSort = 12;

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
            'index' => Pages\ListTrainerFinancialRequests::route('/'),
            'create' => Pages\CreateTrainerFinancialRequest::route('/create'),
            'edit' => Pages\EditTrainerFinancialRequest::route('/{record}/edit'),
        ];
    }
}
