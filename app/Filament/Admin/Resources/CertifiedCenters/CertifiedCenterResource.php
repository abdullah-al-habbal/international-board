<?php

namespace App\Filament\Admin\Resources\CertifiedCenters;

use App\Filament\Admin\Resources\CertifiedCenters\Pages\CreateCertifiedCenter;
use App\Filament\Admin\Resources\CertifiedCenters\Pages\EditCertifiedCenter;
use App\Filament\Admin\Resources\CertifiedCenters\Pages\ListCertifiedCenters;
use App\Filament\Admin\Resources\CertifiedCenters\Pages\ViewCertifiedCenter;
use App\Filament\Admin\Resources\CertifiedCenters\RelationManagers\ApprovedDocumentTypesRelationManager;
use App\Filament\Admin\Resources\CertifiedCenters\RelationManagers\DocumentTypeRequestsRelationManager;
use App\Filament\Admin\Resources\CertifiedCenters\RelationManagers\FinancialRequestsRelationManager;
use App\Filament\Admin\Resources\CertifiedCenters\Schemas\CertifiedCenterForm;
use App\Filament\Admin\Resources\CertifiedCenters\Schemas\CertifiedCenterInfolist;
use App\Filament\Admin\Resources\CertifiedCenters\Tables\CertifiedCentersTable;
use App\Models\CertifiedCenter;
use App\Services\CertifiedCenter\CertifiedCenterService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CertifiedCenterResource extends Resource
{
    protected static ?string $model = CertifiedCenter::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedBuildingOffice2;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.centers');
    }

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $expired = cache()->remember('certified_centers_expired_count', 60, function () {
            return app(CertifiedCenterService::class)->getExpiredAccreditationCount();
        });

        return $expired > 0 ? 'danger' : 'primary';
    }

    public static function getNavigationLabel(): string
    {
        return __('app.certified_centers');
    }

    public static function getModelLabel(): string
    {
        return __('app.certified_center');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.certified_centers');
    }

    public static function getRecordTitle(?\Illuminate\Database\Eloquent\Model $record): string
    {
        if (!$record) {
            return static::getModelLabel();
        }

        return $record->name ?? 'Center #' . $record->id;
    }


    public static function form(Schema $schema): Schema
    {
        return CertifiedCenterForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CertifiedCenterInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CertifiedCentersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ApprovedDocumentTypesRelationManager::class,
            DocumentTypeRequestsRelationManager::class,
            FinancialRequestsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCertifiedCenters::route('/'),
            'create' => CreateCertifiedCenter::route('/create'),
            'view' => ViewCertifiedCenter::route('/{record}'),
            'edit' => EditCertifiedCenter::route('/{record}/edit'),
        ];
    }
}
