<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Trainers;

use App\Filament\Center\Resources\Trainers\Pages\CreateTrainer;
use App\Filament\Center\Resources\Trainers\Pages\EditTrainer;
use App\Filament\Center\Resources\Trainers\Pages\ListTrainers;
use App\Filament\Center\Resources\Trainers\Pages\ViewTrainer;
use App\Filament\Center\Resources\Trainers\Schemas\TrainerForm;
use App\Filament\Center\Resources\Trainers\Schemas\TrainerInfolist;
use App\Filament\Center\Resources\Trainers\Tables\TrainersTable;
use App\Models\Trainer;
use App\Services\Accreditation\AccreditationGateService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TrainerResource extends Resource
{
    protected static ?string $model = Trainer::class;

    protected static ?int $navigationSort = 7;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedUserGroup;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.content');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.trainers');
    }

    public static function getModelLabel(): string
    {
        return __('app.trainer');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.trainers');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function canCreate(): bool
    {
        return app(AccreditationGateService::class)->currentCenterCanPerformActions();
    }

    public static function canEdit(Model $record): bool
    {
        return app(AccreditationGateService::class)->currentCenterCanPerformActions();
    }

    public static function canDelete(Model $record): bool
    {
        return app(AccreditationGateService::class)->currentCenterCanPerformActions();
    }

    public static function canDeleteAny(): bool
    {
        return app(AccreditationGateService::class)->currentCenterCanPerformActions();
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('center_id', auth('certified_center')->id());
    }

    public static function form(Schema $schema): Schema
    {
        return TrainerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrainerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainers::route('/'),
            'create' => CreateTrainer::route('/create'),
            'view' => ViewTrainer::route('/{record}'),
            'edit' => EditTrainer::route('/{record}/edit'),
        ];
    }
}
