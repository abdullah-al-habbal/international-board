<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\CenterDocumentTypeRequests;

use App\Models\CenterDocumentTypeRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Center\Resources\CenterDocumentTypeRequests\Schemas\CenterDocumentTypeRequestForm;
use App\Filament\Center\Resources\CenterDocumentTypeRequests\Tables\CenterDocumentTypeRequestsTable;
use App\Filament\Center\Resources\CenterDocumentTypeRequests\Pages\ListCenterDocumentTypeRequests;
use App\Filament\Center\Resources\CenterDocumentTypeRequests\Pages\CreateCenterDocumentTypeRequest;

class CenterDocumentTypeRequestResource extends Resource
{
    protected static ?string $model = CenterDocumentTypeRequest::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedDocumentPlus;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.content');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.request_document_types');
    }

    public static function getModelLabel(): string
    {
        return __('app.document_type_request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.request_document_types');
    }

    protected static ?int $navigationSort = 11;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('certified_center_id', auth()->id());
    }

    public static function form(Schema $schema): Schema
    {
        return CenterDocumentTypeRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CenterDocumentTypeRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCenterDocumentTypeRequests::route('/'),
            'create' => CreateCenterDocumentTypeRequest::route('/create'),
        ];
    }
}
