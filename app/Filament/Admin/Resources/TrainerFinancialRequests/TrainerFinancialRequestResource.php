<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerFinancialRequests;

use App\Filament\Admin\Resources\TrainerFinancialRequests\Schemas\TrainerFinancialRequestForm;
use App\Filament\Admin\Resources\TrainerFinancialRequests\Schemas\TrainerFinancialRequestInfolist;
use App\Filament\Admin\Resources\TrainerFinancialRequests\Tables\TrainerFinancialRequestsTable;
use App\Models\TrainerFinancialRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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

    protected static ?int $navigationSort = 12;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
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

    public static function getRecordTitle(?Model $record): string
    {
        if (! $record) {
            return static::getModelLabel();
        }

        return $record->trainer?->name ?? 'Request #'.$record->id;
    }

    public static function form(Schema $schema): Schema
    {
        return TrainerFinancialRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrainerFinancialRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainerFinancialRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrainerFinancialRequests::route('/'),
            'create' => Pages\CreateTrainerFinancialRequest::route('/create'),
            'view' => Pages\ViewTrainerFinancialRequest::route('/{record}'),
            'edit' => Pages\EditTrainerFinancialRequest::route('/{record}/edit'),
        ];
    }
}
