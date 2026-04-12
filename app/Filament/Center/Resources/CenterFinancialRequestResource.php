<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources;

use App\Models\CertifiedCenterFinancialRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Center\Resources\CenterFinancialRequests\Schemas\CenterFinancialRequestForm;
use App\Filament\Center\Resources\CenterFinancialRequests\Tables\CenterFinancialRequestsTable;
use App\Filament\Center\Resources\CenterFinancialRequests\Pages\ListCenterFinancialRequests;
class CenterFinancialRequestResource extends Resource
{
    protected static ?string $model = CertifiedCenterFinancialRequest::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedBanknotes;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.content');
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
        return parent::getEloquentQuery()->where('certified_center_id', auth()->id());
    }

    public static function form(Schema $schema): Schema
    {
        return CenterFinancialRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CenterFinancialRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCenterFinancialRequests::route('/'),
        ];
    }
}
