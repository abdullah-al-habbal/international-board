<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\AccreditationStatus;
use App\Filament\Admin\Resources\TrainerAccreditationRequests\Pages;
use App\Filament\Admin\Resources\TrainerAccreditationRequests\Schemas\TrainerAccreditationRequestForm;
use App\Filament\Admin\Resources\TrainerAccreditationRequests\Tables\TrainerAccreditationRequestsTable;
use App\Models\TrainerAccreditationRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
        return __('app.trainer_accreditation_requests');
    }

    public static function getModelLabel(): string
    {
        return __('app.trainer_accreditation_request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.trainer_accreditation_requests');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', AccreditationStatus::Pending)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() > 0 ? 'warning' : 'gray';
    }

    public static function getRecordTitle(?Model $record): string
    {
        if (! $record) {
            return static::getModelLabel();
        }

        return $record->trainer?->name ?? 'Request #'.$record->id;
    }

    protected static ?int $navigationSort = 20;

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
            'index' => Pages\ListTrainerAccreditationRequests::route('/'),
            'create' => Pages\CreateTrainerAccreditationRequest::route('/create'),
            'edit' => Pages\EditTrainerAccreditationRequest::route('/{record}/edit'),
        ];
    }
}
