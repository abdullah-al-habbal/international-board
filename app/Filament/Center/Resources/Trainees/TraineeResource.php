<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Trainees;

use App\Filament\Center\Resources\Trainees\Pages\CreateTrainee;
use App\Filament\Center\Resources\Trainees\Pages\EditTrainee;
use App\Filament\Center\Resources\Trainees\Pages\ListTrainees;
use App\Filament\Center\Resources\Trainees\Pages\ViewTrainee;
use App\Filament\Center\Resources\Trainees\Schemas\TraineeForm;
use App\Filament\Center\Resources\Trainees\Schemas\TraineeInfolist;
use App\Filament\Center\Resources\Trainees\Tables\TraineesTable;
use App\Models\CertifiedCenter;
use App\Models\Trainee;
use App\Services\Accreditation\AccreditationGateService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TraineeResource extends Resource
{
    protected static ?string $model = Trainee::class;

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

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedUsers;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.content');
    }

    protected static ?int $navigationSort = 9;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function getNavigationLabel(): string
    {
        return __('app.trainees');
    }

    public static function getModelLabel(): string
    {
        return __('app.trainee');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.trainees');
    }

    public static function getEloquentQuery(): Builder
    {
        $centerId = auth('certified_center')->id();

        return parent::getEloquentQuery()
            ->whereHas(
                'certifications',
                fn ($q) => $q->where('creator_type', CertifiedCenter::class)
                    ->where('creator_id', $centerId)
            );
    }

    public static function form(Schema $schema): Schema
    {
        return TraineeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TraineeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TraineesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainees::route('/'),
            'create' => CreateTrainee::route('/create'),
            'view' => ViewTrainee::route('/{record}'),
            'edit' => EditTrainee::route('/{record}/edit'),
        ];
    }
}
