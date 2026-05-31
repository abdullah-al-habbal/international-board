<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenterFinancialRequests;

use App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\Schemas\CertifiedCenterFinancialRequestForm;
use App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\Schemas\CertifiedCenterFinancialRequestInfolist;
use App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\Tables\CertifiedCenterFinancialRequestsTable;
use App\Models\CertifiedCenterFinancialRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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

    protected static ?int $navigationSort = 11;

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

    public static function getRecordTitle(?Model $record): string
    {
        if (! $record) {
            return static::getModelLabel();
        }

        return $record->certifiedCenter?->name ?? 'Request #'.$record->id;
    }

    public static function form(Schema $schema): Schema
    {
        return CertifiedCenterFinancialRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CertifiedCenterFinancialRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CertifiedCenterFinancialRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertifiedCenterFinancialRequests::route('/'),
            'create' => Pages\CreateCertifiedCenterFinancialRequest::route('/create'),
            'view' => Pages\ViewCertifiedCenterFinancialRequest::route('/{record}'),
            'edit' => Pages\EditCertifiedCenterFinancialRequest::route('/{record}/edit'),
        ];
    }
}
