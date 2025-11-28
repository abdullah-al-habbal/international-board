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
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AccreditationRequestResource extends Resource
{
    protected static ?string $model = AccreditationRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

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
        return [
            //
        ];
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
