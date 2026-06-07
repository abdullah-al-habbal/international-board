<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerDocumentTypes;

use App\Filament\Trainer\Resources\TrainerDocumentTypes\Pages\ListTrainerDocumentTypes;
use App\Filament\Trainer\Resources\TrainerDocumentTypes\Tables\TrainerDocumentTypesTable;
use App\Models\TrainerDocumentType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TrainerDocumentTypeResource extends Resource
{
    protected static ?string $model = TrainerDocumentType::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedDocumentCheck;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.financial_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.approved_document_types');
    }

    public static function getModelLabel(): string
    {
        return __('app.approved_document_type');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.approved_document_types');
    }

    protected static ?int $navigationSort = 12;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('trainer_id', Auth::id());
    }

    public static function table(Table $table): Table
    {
        return TrainerDocumentTypesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainerDocumentTypes::route('/'),
        ];
    }
}
