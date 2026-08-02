<?php

namespace App\Filament\Admin\Resources\CenterAccreditationRequests;

use App\Filament\Admin\Resources\CenterAccreditationRequests\Pages\EditCenterAccreditationRequest;
use App\Filament\Admin\Resources\CenterAccreditationRequests\Pages\ListCenterAccreditationRequests;
use App\Filament\Admin\Resources\CenterAccreditationRequests\Pages\ViewCenterAccreditationRequest;
use App\Filament\Admin\Resources\CenterAccreditationRequests\Schemas\CenterAccreditationRequestForm;
use App\Filament\Admin\Resources\CenterAccreditationRequests\Schemas\CenterAccreditationRequestInfolist;
use App\Filament\Admin\Resources\CenterAccreditationRequests\Tables\CenterAccreditationRequestsTable;
use App\Models\CenterAccreditationRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CenterAccreditationRequestResource extends Resource
{
    protected static ?string $model = CenterAccreditationRequest::class;

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

    public static function getRecordTitle(?Model $record): string
    {
        if (! $record) {
            return static::getModelLabel();
        }

        return $record->certifiedCenter?->name ?? 'Request #'.$record->id;
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

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return CenterAccreditationRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CenterAccreditationRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CenterAccreditationRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCenterAccreditationRequests::route('/'),
            'view' => ViewCenterAccreditationRequest::route('/{record}'),
            'edit' => EditCenterAccreditationRequest::route('/{record}/edit'),
        ];
    }
}
