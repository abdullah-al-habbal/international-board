<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CertifiedCenterDocumentTypes;

use App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\Pages\ListCertifiedCenterDocumentTypes;
use App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\Pages\ViewCertifiedCenterDocumentType;
use App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\Schemas\CertifiedCenterDocumentTypeForm;
use App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\Schemas\CertifiedCenterDocumentTypeInfolist;
use App\Filament\Admin\Resources\CertifiedCenterDocumentTypes\Tables\CertifiedCenterDocumentTypesTable;
use App\Models\CertifiedCenterDocumentType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CertifiedCenterDocumentTypeResource extends Resource
{
    protected static ?string $model = CertifiedCenterDocumentType::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedDocumentCheck;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.settings');
    }

    protected static ?int $navigationSort = 36;

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
        return __('app.certified_center_document_types');
    }

    public static function getModelLabel(): string
    {
        return __('app.certified_center_document_type');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.certified_center_document_types');
    }

    public static function infolist(Schema $schema): Schema
    {
        return CertifiedCenterDocumentTypeInfolist::configure($schema);
    }

    public static function form(Schema $schema): Schema
    {
        return CertifiedCenterDocumentTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CertifiedCenterDocumentTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCertifiedCenterDocumentTypes::route('/'),
            'view' => ViewCertifiedCenterDocumentType::route('/{record}'),
        ];
    }
}
