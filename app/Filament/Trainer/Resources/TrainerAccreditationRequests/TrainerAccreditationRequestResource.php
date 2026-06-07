<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Resources\TrainerAccreditationRequests;

use App\Enums\AccreditationStatus;
use App\Models\TrainerAccreditationRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Trainer\Resources\TrainerAccreditationRequests\Schemas\TrainerAccreditationRequestForm;
use App\Filament\Trainer\Resources\TrainerAccreditationRequests\Tables\TrainerAccreditationRequestsTable;
use App\Filament\Trainer\Resources\TrainerAccreditationRequests\Pages\ListTrainerAccreditationRequests;
use App\Filament\Trainer\Resources\TrainerAccreditationRequests\Pages\CreateTrainerAccreditationRequest;
use App\Filament\Trainer\Resources\TrainerAccreditationRequests\Pages\ViewTrainerAccreditationRequest;
use App\Filament\Trainer\Resources\TrainerAccreditationRequests\Pages\EditTrainerAccreditationRequest;
use Illuminate\Support\Facades\Auth;

class TrainerAccreditationRequestResource extends Resource
{
    protected static ?string $model = TrainerAccreditationRequest::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedClipboardDocumentCheck;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.accreditation_management');
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

    protected static ?int $navigationSort = 20;

    public static function canCreate(): bool
    {
        return ! TrainerAccreditationRequest::query()
            ->where('trainer_id', Auth::id())
            ->where('status', '!=', AccreditationStatus::Rejected->value)
            ->exists();
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() > 0 ? 'warning' : 'gray';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('trainer_id', Auth::id());
    }

    public static function form(Schema $schema): Schema
    {
        return TrainerAccreditationRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainerAccreditationRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainerAccreditationRequests::route('/'),
            'create' => CreateTrainerAccreditationRequest::route('/create'),
            'view' => ViewTrainerAccreditationRequest::route('/{record}'),
            'edit' => EditTrainerAccreditationRequest::route('/{record}/edit'),
        ];
    }
}
