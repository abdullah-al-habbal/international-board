<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerDocumentTypeRequests;

use App\Filament\Trainer\Resources\TrainerDocumentTypeRequests\Pages\CreateTrainerDocumentTypeRequest;
use App\Filament\Trainer\Resources\TrainerDocumentTypeRequests\Pages\ListTrainerDocumentTypeRequests;
use App\Models\TrainerDocumentTypeRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Trainer\Resources\TrainerDocumentTypeRequests\Schemas\TrainerDocumentTypeRequestForm;
use App\Filament\Trainer\Resources\TrainerDocumentTypeRequests\Tables\TrainerDocumentTypeRequestsTable;
use Illuminate\Support\Facades\Auth;

class TrainerDocumentTypeRequestResource extends Resource
{
    protected static ?string $model = TrainerDocumentTypeRequest::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedDocumentPlus;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.financial_management');
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

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', \App\Enums\DocumentTypeRequestStatus::Pending)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() > 0 ? 'warning' : 'gray';
    }

    public static function getRecordTitle(?\Illuminate\Database\Eloquent\Model $record): string
    {
        if (!$record) {
            return static::getModelLabel();
        }

        return $record->trainer?->name ?? 'Request #' . $record->id;
    }


    protected static ?int $navigationSort = 11;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('trainer_id', Auth::id());
    }

    public static function form(Schema $schema): Schema
    {
        return TrainerDocumentTypeRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainerDocumentTypeRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainerDocumentTypeRequests::route('/'),
            'create' => CreateTrainerDocumentTypeRequest::route('/create'),
        ];
    }
}
