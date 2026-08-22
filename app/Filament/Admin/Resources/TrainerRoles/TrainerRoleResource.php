<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerRoles;

use App\Filament\Admin\Resources\TrainerRoles\Pages\CreateTrainerRole;
use App\Filament\Admin\Resources\TrainerRoles\Pages\EditTrainerRole;
use App\Filament\Admin\Resources\TrainerRoles\Pages\ListTrainerRoles;
use App\Filament\Admin\Resources\TrainerRoles\Pages\ViewTrainerRole;
use App\Filament\Admin\Resources\TrainerRoles\Schemas\TrainerRoleForm;
use App\Filament\Admin\Resources\TrainerRoles\Schemas\TrainerRoleInfolist;
use App\Filament\Admin\Resources\TrainerRoles\Tables\TrainerRolesTable;
use App\Models\TrainerRole;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TrainerRoleResource extends Resource
{
    protected static ?string $model = TrainerRole::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return 'heroicon-o-identification';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.settings');
    }

    protected static ?int $navigationSort = 41;

    protected static ?string $recordTitleAttribute = 'name';

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
        return __('app.trainer_roles');
    }

    public static function getModelLabel(): string
    {
        return __('app.trainer_role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.trainer_roles');
    }

    public static function form(Schema $schema): Schema
    {
        return TrainerRoleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrainerRoleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainerRolesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainerRoles::route('/'),
            'create' => CreateTrainerRole::route('/create'),
            'view' => ViewTrainerRole::route('/{record}'),
            'edit' => EditTrainerRole::route('/{record}/edit'),
        ];
    }
}
