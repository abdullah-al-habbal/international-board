<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PaymentAgentPersons;

use App\Filament\Admin\Resources\PaymentAgentPersons\RelationManagers\CenterFinancialRequestsRelationManager;
use App\Filament\Admin\Resources\PaymentAgentPersons\RelationManagers\TrainerFinancialRequestsRelationManager;
use App\Filament\Admin\Resources\PaymentAgentPersons\Schemas\PaymentAgentPersonForm;
use App\Filament\Admin\Resources\PaymentAgentPersons\Schemas\PaymentAgentPersonInfolist;
use App\Filament\Admin\Resources\PaymentAgentPersons\Tables\PaymentAgentPersonsTable;
use App\Models\CertifiedCenterPaymentAgentPerson;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PaymentAgentPersonResource extends Resource
{
    protected static ?string $model = CertifiedCenterPaymentAgentPerson::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedUserGroup;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.financial_management');
    }

    protected static ?int $navigationSort = 10;

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
        return __('app.agent_persons');
    }

    public static function getModelLabel(): string
    {
        return __('app.agent_person');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.agent_persons');
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
        return PaymentAgentPersonForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PaymentAgentPersonInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentAgentPersonsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CenterFinancialRequestsRelationManager::class,
            TrainerFinancialRequestsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentAgentPersons::route('/'),
            'create' => Pages\CreatePaymentAgentPerson::route('/create'),
            'view' => Pages\ViewPaymentAgentPerson::route('/{record}'),
            'edit' => Pages\EditPaymentAgentPerson::route('/{record}/edit'),
        ];
    }
}
