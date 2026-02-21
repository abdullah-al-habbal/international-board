<?php

namespace App\Filament\Admin\Resources\CertifiedCenters;

use App\Filament\Admin\Resources\CertifiedCenters\Pages\CreateCertifiedCenter;
use App\Filament\Admin\Resources\CertifiedCenters\Pages\EditCertifiedCenter;
use App\Filament\Admin\Resources\CertifiedCenters\Pages\ListCertifiedCenters;
use App\Filament\Admin\Resources\CertifiedCenters\Pages\ViewCertifiedCenter;
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
use UnitEnum;

class CertifiedCenterResource extends Resource
{
    protected static ?string $model = CertifiedCenter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static string | UnitEnum | null $navigationGroup = 'filament.navigation.groups.centers';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $expired = app(CertifiedCenterService::class)->getExpiredAccreditationCount();
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
        return [];
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
