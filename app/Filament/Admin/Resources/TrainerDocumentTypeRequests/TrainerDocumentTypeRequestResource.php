<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerDocumentTypeRequests;

use App\Filament\Admin\Resources\TrainerDocumentTypeRequests\Pages\ListTrainerDocumentTypeRequests;
use App\Filament\Admin\Resources\TrainerDocumentTypeRequests\Pages\ViewTrainerDocumentTypeRequest;
use App\Filament\Admin\Resources\TrainerDocumentTypeRequests\Schemas\TrainerDocumentTypeRequestInfolist;
use App\Filament\Admin\Resources\TrainerDocumentTypeRequests\Tables\TrainerDocumentTypeRequestsTable;
use App\Models\TrainerDocumentTypeRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TrainerDocumentTypeRequestResource extends Resource
{
    protected static ?string $model = TrainerDocumentTypeRequest::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedDocumentArrowUp;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.content');
    }

    protected static ?int $navigationSort = 8;

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
        return __('app.trainer_document_type_requests');
    }

    public static function getModelLabel(): string
    {
        return __('app.trainer_document_type_request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.trainer_document_type_requests');
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrainerDocumentTypeRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainerDocumentTypeRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainerDocumentTypeRequests::route('/'),
            'view' => ViewTrainerDocumentTypeRequest::route('/{record}'),
        ];
    }
}
