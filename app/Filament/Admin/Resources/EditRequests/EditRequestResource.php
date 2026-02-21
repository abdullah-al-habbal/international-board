<?php

namespace App\Filament\Admin\Resources\EditRequests;

use App\Filament\Admin\Resources\EditRequests\Pages\ListEditRequests;
use App\Filament\Admin\Resources\EditRequests\Pages\ViewEditRequest;
use App\Filament\Admin\Resources\EditRequests\Schemas\EditRequestForm;
use App\Filament\Admin\Resources\EditRequests\Schemas\EditRequestInfolist;
use App\Filament\Admin\Resources\EditRequests\Tables\EditRequestsTable;
use App\Models\EditRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EditRequestResource extends Resource
{
    protected static ?string $model = EditRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string | UnitEnum | null $navigationGroup = __('filament.navigation.groups.content');

    protected static ?int $navigationSort = 9;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'warning' : 'primary';
    }

    public static function getNavigationLabel(): string
    {
        return __('app.edit_requests');
    }

    public static function getModelLabel(): string
    {
        return __('app.edit_request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.edit_requests');
    }

    public static function form(Schema $schema): Schema
    {
        return EditRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EditRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EditRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEditRequests::route('/'),
            'view' => ViewEditRequest::route('/{record}'),
        ];
    }
}
