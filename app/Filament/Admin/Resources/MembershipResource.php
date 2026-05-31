<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\Membership\Pages\CreateMembership;
use App\Filament\Admin\Resources\Membership\Pages\EditMembership;
use App\Filament\Admin\Resources\Membership\Pages\ListMemberships;
use App\Filament\Admin\Resources\Membership\Pages\ViewMembership;
use App\Filament\Admin\Resources\Membership\Schemas\MembershipForm;
use App\Filament\Admin\Resources\Membership\Schemas\MembershipInfolist;
use App\Filament\Admin\Resources\Membership\Tables\MembershipsTable;
use App\Models\Membership;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MembershipResource extends Resource
{
    protected static ?string $model = Membership::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedIdentification;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.memberships');
    }

    protected static ?int $navigationSort = 3; // adjust as needed

    protected static ?string $recordTitleAttribute = 'title';

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
        return __('app.memberships');
    }

    public static function getModelLabel(): string
    {
        return __('app.membership');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.memberships');
    }

    public static function form(Schema $schema): Schema
    {
        return MembershipForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MembershipInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MembershipsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMemberships::route('/'),
            'create' => CreateMembership::route('/create'),
            'view' => ViewMembership::route('/{record}'),
            'edit' => EditMembership::route('/{record}/edit'),
        ];
    }
}