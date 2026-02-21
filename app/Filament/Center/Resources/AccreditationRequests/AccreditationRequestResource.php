<?php

namespace App\Filament\Center\Resources\AccreditationRequests;

use App\Filament\Center\Resources\AccreditationRequests\Pages\CreateAccreditationRequest;
use App\Filament\Center\Resources\AccreditationRequests\Pages\EditAccreditationRequest;
use App\Filament\Center\Resources\AccreditationRequests\Pages\ListAccreditationRequests;
use App\Filament\Center\Resources\AccreditationRequests\Pages\ViewAccreditationRequest;
use App\Filament\Center\Resources\AccreditationRequests\Schemas\AccreditationRequestForm;
use App\Filament\Center\Resources\AccreditationRequests\Schemas\AccreditationRequestInfolist;
use App\Filament\Center\Resources\AccreditationRequests\Tables\AccreditationRequestsTable;
use App\Models\AccreditationRequest;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AccreditationRequestResource extends Resource
{
    protected static ?string $model = AccreditationRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static UnitEnum | string | null $navigationGroup = __('filament.navigation.groups.content');

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getEloquentQuery()->count() > 0 ? 'warning' : 'primary';
    }

    public static function getNavigationLabel(): string
    {
        return __('app.accreditation_requests');
    }

    public static function getModelLabel(): string
    {
        return __('app.accreditation_request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.accreditation_requests');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('certified_center_id', Auth::id());
    }

    public static function form(Schema $schema): Schema
    {
        return AccreditationRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AccreditationRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccreditationRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccreditationRequests::route('/'),
            'create' => CreateAccreditationRequest::route('/create'),
            'view' => ViewAccreditationRequest::route('/{record}'),
            'edit' => EditAccreditationRequest::route('/{record}/edit'),
        ];
    }
}
