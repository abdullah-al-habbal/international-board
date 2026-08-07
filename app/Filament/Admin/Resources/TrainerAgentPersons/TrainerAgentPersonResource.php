<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerAgentPersons;

use App\Filament\Admin\Resources\PaymentAgentPersons\RelationManagers\TrainerFinancialRequestsRelationManager;
use App\Filament\Admin\Resources\TrainerAgentPersons\Schemas\TrainerAgentPersonForm;
use App\Filament\Admin\Resources\TrainerAgentPersons\Schemas\TrainerAgentPersonInfolist;
use App\Filament\Admin\Resources\TrainerAgentPersons\Tables\TrainerAgentPersonsTable;
use App\Models\AgentPerson;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TrainerAgentPersonResource extends Resource
{
    protected static ?string $model = AgentPerson::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedUserGroup;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.financial_management_trainers');
    }

    protected static ?int $navigationSort = 13;

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
        return __('app.trainer_agent_persons');
    }

    public static function getModelLabel(): string
    {
        return __('app.trainer_agent_person');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.trainer_agent_persons');
    }

    public static function getRecordTitle(?Model $record): string
    {
        if (! $record) {
            return static::getModelLabel();
        }

        return $record->name ?? 'Agent #'.$record->id;
    }

    public static function form(Schema $schema): Schema
    {
        return TrainerAgentPersonForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TrainerAgentPersonInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainerAgentPersonsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TrainerFinancialRequestsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrainerAgentPersons::route('/'),
            'create' => Pages\CreateTrainerAgentPerson::route('/create'),
            'view' => Pages\ViewTrainerAgentPerson::route('/{record}'),
            'edit' => Pages\EditTrainerAgentPerson::route('/{record}/edit'),
        ];
    }
}
