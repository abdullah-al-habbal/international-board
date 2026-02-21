<?php

namespace App\Filament\Center\Resources\CenterTypeRequests;

use App\Filament\Center\Resources\CenterTypeRequests\Pages\CreateCenterTypeRequest;
use App\Filament\Center\Resources\CenterTypeRequests\Pages\ListCenterTypeRequests;
use App\Filament\Center\Resources\CenterTypeRequests\Pages\ViewCenterTypeRequest;
use App\Filament\Center\Resources\CenterTypeRequests\Schemas\CenterTypeRequestForm;
use App\Filament\Center\Resources\CenterTypeRequests\Schemas\CenterTypeRequestInfolist;
use App\Filament\Center\Resources\CenterTypeRequests\Tables\CenterTypeRequestsTable;
use App\Models\CenterTypeRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class CenterTypeRequestResource extends Resource
{
    protected static ?string $model = CenterTypeRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static string | UnitEnum | null $navigationGroup = __('filament.navigation.groups.content');

    protected static ?int $navigationSort = 7;

    protected static ?string $recordTitleAttribute = 'requested_name';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'warning' : 'primary';
    }

    public static function getNavigationLabel(): string
    {
        return __('app.center_type_requests');
    }

    public static function getModelLabel(): string
    {
        return __('app.center_type_request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.center_type_requests');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('certified_center_id', Auth::id());
    }

    public static function form(Schema $schema): Schema
    {
        return CenterTypeRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CenterTypeRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CenterTypeRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCenterTypeRequests::route('/'),
            'create' => CreateCenterTypeRequest::route('/create'),
            'view' => ViewCenterTypeRequest::route('/{record}'),
        ];
    }
}
