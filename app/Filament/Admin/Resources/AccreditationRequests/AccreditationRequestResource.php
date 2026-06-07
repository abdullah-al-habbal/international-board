<?php

namespace App\Filament\Admin\Resources\AccreditationRequests;

use App\Filament\Admin\Resources\AccreditationRequests\Pages\EditAccreditationRequest;
use App\Filament\Admin\Resources\AccreditationRequests\Pages\ListAccreditationRequests;
use App\Filament\Admin\Resources\AccreditationRequests\Pages\ViewAccreditationRequest;
use App\Filament\Admin\Resources\AccreditationRequests\Schemas\AccreditationRequestForm;
use App\Filament\Admin\Resources\AccreditationRequests\Schemas\AccreditationRequestInfolist;
use App\Filament\Admin\Resources\AccreditationRequests\Tables\AccreditationRequestsTable;
use App\Models\AccreditationRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AccreditationRequestResource extends Resource
{
    protected static ?string $model = AccreditationRequest::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedRectangleStack;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.content');
    }

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::pending()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::pending()->count() > 0 ? 'warning' : 'gray';
    }

    public static function getRecordTitle(?\Illuminate\Database\Eloquent\Model $record): string
    {
        if (!$record) {
            return static::getModelLabel();
        }

        return $record->certifiedCenter?->name ?? 'Request #' . $record->id;
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
            'view' => ViewAccreditationRequest::route('/{record}'),
            'edit' => EditAccreditationRequest::route('/{record}/edit'),
        ];
    }
}
