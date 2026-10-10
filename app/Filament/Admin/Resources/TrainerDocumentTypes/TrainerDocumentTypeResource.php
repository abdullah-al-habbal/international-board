<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerDocumentTypes;

use App\Filament\Admin\Resources\TrainerDocumentTypes\Pages\ListTrainerDocumentTypes;
use App\Filament\Admin\Resources\TrainerDocumentTypes\Schemas\TrainerDocumentTypeForm;
use App\Filament\Admin\Resources\TrainerDocumentTypes\Schemas\TrainerDocumentTypeInfolist;
use App\Filament\Admin\Resources\TrainerDocumentTypes\Tables\TrainerDocumentTypesTable;
use App\Models\TrainerDocumentType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TrainerDocumentTypeResource extends Resource
{
    protected static ?string $model = TrainerDocumentType::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedDocumentCheck;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.settings');
    }

    protected static ?int $navigationSort = 37;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function getNavigationLabel(): string
    {
        return __('app.trainer_document_types');
    }

    public static function getModelLabel(): string
    {
        return __('app.trainer_document_type');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.trainer_document_types');
    }

    public static function form(Schema $schema): Schema
    {
        return TrainerDocumentTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrainerDocumentTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainerDocumentTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainerDocumentTypes::route('/'),
        ];
    }
}
