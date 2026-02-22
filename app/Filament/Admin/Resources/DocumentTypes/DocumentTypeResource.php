<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\DocumentTypes;

use App\Filament\Admin\Resources\DocumentTypes\Pages\CreateDocumentType;
use App\Filament\Admin\Resources\DocumentTypes\Pages\EditDocumentType;
use App\Filament\Admin\Resources\DocumentTypes\Pages\ListDocumentTypes;
use App\Filament\Admin\Resources\DocumentTypes\Pages\ViewDocumentType;
use App\Filament\Admin\Resources\DocumentTypes\Schemas\DocumentTypeForm;
use App\Filament\Admin\Resources\DocumentTypes\Schemas\DocumentTypeInfolist;
use App\Filament\Admin\Resources\DocumentTypes\Tables\DocumentTypesTable;
use App\Models\DocumentType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DocumentTypeResource extends Resource
{
    protected static ?string $model =  null;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return 'heroicon-o-document-text';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.settings');
    }

    protected static ?string $recordTitleAttribute = 'key';

    protected static ?int $navigationSort = 35;

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.document_types.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.document_types.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.document_types.plural_model_label');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 50 ? 'warning' : 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return DocumentTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DocumentTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocumentTypes::route('/'),
            'create' => CreateDocumentType::route('/create'),
            'view' => ViewDocumentType::route('/{record}'),
            'edit' => EditDocumentType::route('/{record}/edit'),
        ];
    }
}
