<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CertifiedCenterDocumentTypes;

use App\Filament\Center\Resources\CertifiedCenterDocumentTypes\Pages\ListCertifiedCenterDocumentTypes;
use App\Filament\Center\Resources\CertifiedCenterDocumentTypes\Tables\CertifiedCenterDocumentTypesTable;
use App\Models\CertifiedCenterDocumentType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CertifiedCenterDocumentTypeResource extends Resource
{
    protected static ?string $model = CertifiedCenterDocumentType::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedDocumentCheck;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.content');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.approved_document_types');
    }

    public static function getModelLabel(): string
    {
        return __('app.approved_document_type');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.approved_document_types');
    }

    protected static ?int $navigationSort = 12;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('certified_center_id', Auth::id());
    }

    public static function table(Table $table): Table
    {
        return CertifiedCenterDocumentTypesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCertifiedCenterDocumentTypes::route('/'),
        ];
    }
}
