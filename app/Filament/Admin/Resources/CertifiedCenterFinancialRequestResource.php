<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\Pages;
use App\Models\CertifiedCenterFinancialRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\Schemas\CertifiedCenterFinancialRequestForm;
use App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\Tables\CertifiedCenterFinancialRequestsTable;

class CertifiedCenterFinancialRequestResource extends Resource
{
    protected static ?string $model = CertifiedCenterFinancialRequest::class;

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
        return __('app.center_financial_requests');
    }

    public static function getModelLabel(): string
    {
        return __('app.center_financial_request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.center_financial_requests');
    }

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return CertifiedCenterFinancialRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CertifiedCenterFinancialRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertifiedCenterFinancialRequests::route('/'),
            'create' => Pages\CreateCertifiedCenterFinancialRequest::route('/create'),
            'edit' => Pages\EditCertifiedCenterFinancialRequest::route('/{record}/edit'),
        ];
    }
}
