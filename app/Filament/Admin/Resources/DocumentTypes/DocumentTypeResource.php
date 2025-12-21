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
    protected static ?string $model = DocumentType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'key';

    public static function getNavigationLabel(): string
    {
        return __('Document Types');
    }

    public static function getModelLabel(): string
    {
        return __('Document Type');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Document Types');
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
