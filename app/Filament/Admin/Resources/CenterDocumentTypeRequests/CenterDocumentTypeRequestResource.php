<?php
// filePath: app/Filament/Admin/Resources/CenterDocumentTypeRequests/CenterDocumentTypeRequestResource.php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CenterDocumentTypeRequests;

use App\Filament\Admin\Resources\CenterDocumentTypeRequests\Pages\CreateCenterDocumentTypeRequest;
use App\Filament\Admin\Resources\CenterDocumentTypeRequests\Pages\EditCenterDocumentTypeRequest;
use App\Filament\Admin\Resources\CenterDocumentTypeRequests\Pages\ListCenterDocumentTypeRequests;
use App\Filament\Admin\Resources\CenterDocumentTypeRequests\Pages\ViewCenterDocumentTypeRequest;
use App\Filament\Admin\Resources\CenterDocumentTypeRequests\Schemas\CenterDocumentTypeRequestForm;
use App\Filament\Admin\Resources\CenterDocumentTypeRequests\Schemas\CenterDocumentTypeRequestInfolist;
use App\Filament\Admin\Resources\CenterDocumentTypeRequests\Tables\CenterDocumentTypeRequestsTable;
use App\Models\CenterDocumentTypeRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CenterDocumentTypeRequestResource extends Resource
{
    protected static ?string $model = CenterDocumentTypeRequest::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedDocumentArrowUp;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.content');
    }

    protected static ?int $navigationSort = 7;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() > 0 ? 'warning' : 'primary';
    }

    public static function getNavigationLabel(): string
    {
        return __('app.document_type_requests');
    }

    public static function getModelLabel(): string
    {
        return __('app.document_type_request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.document_type_requests');
    }

    public static function form(Schema $schema): Schema
    {
        return CenterDocumentTypeRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CenterDocumentTypeRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CenterDocumentTypeRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCenterDocumentTypeRequests::route('/'),
            'create' => CreateCenterDocumentTypeRequest::route('/create'),
            'view' => ViewCenterDocumentTypeRequest::route('/{record}'),
            'edit' => EditCenterDocumentTypeRequest::route('/{record}/edit'),
        ];
    }
}
