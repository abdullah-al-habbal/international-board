<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerFinancialRequests;

use App\Filament\Trainer\Resources\TrainerFinancialRequests\Pages\ListTrainerFinancialRequests;
use App\Filament\Trainer\Resources\TrainerFinancialRequests\Pages\ViewTrainerFinancialRequest;
use App\Filament\Trainer\Resources\TrainerFinancialRequests\Schemas\TrainerFinancialRequestInfolist;
use App\Filament\Trainer\Resources\TrainerFinancialRequests\Tables\TrainerFinancialRequestsTable;
use App\Models\FinancialRequest;
use App\Models\Trainer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TrainerFinancialRequestResource extends Resource
{
    protected static ?string $model = FinancialRequest::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedBanknotes;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.financial_management_trainers');
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

    public static function getRecordTitle(?Model $record): string
    {
        if (! $record) {
            return static::getModelLabel();
        }

        return $record->requestable?->name ?? __('app.record.request_n', ['id' => $record->id]);
    }

    protected static ?int $navigationSort = 10;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() > 0 ? 'warning' : 'gray';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('requestable_type', Trainer::class)
            ->where('requestable_id', auth('trainer')->id())
            ->with(['currency']);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrainerFinancialRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainerFinancialRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainerFinancialRequests::route('/'),
            'view' => ViewTrainerFinancialRequest::route('/{record}'),
        ];
    }
}
