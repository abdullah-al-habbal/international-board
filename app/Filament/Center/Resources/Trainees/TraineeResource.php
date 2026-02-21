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
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TraineeResource extends Resource
{
    protected static ?string $model = Trainee::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string | UnitEnum | null $navigationGroup = __('filament.navigation.groups.users');

    protected static ?int $navigationSort = 3;

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
        $query = parent::getEloquentQuery();

        if (Auth::guard('web')->check() && Auth::guard('web')->user() instanceof CertifiedCenter) {
            $centerId = Auth::guard('web')->id();

            $query->whereHas('certifications', function (Builder $q) use ($centerId) {
                $q->where('certified_center_id', $centerId);
            });
        }

        return $query;
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
