<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Memberships;

use App\Filament\Admin\Resources\Memberships\Pages\CreateMembership;
use App\Filament\Admin\Resources\Memberships\Pages\EditMembership;
use App\Filament\Admin\Resources\Memberships\Pages\ListMemberships;
use App\Filament\Admin\Resources\Memberships\Pages\ViewMembership;
use App\Filament\Admin\Resources\Memberships\Schemas\MembershipForm;
use App\Filament\Admin\Resources\Memberships\Schemas\MembershipInfolist;
use App\Filament\Admin\Resources\Memberships\Tables\MembershipsTable;
use App\Models\Membership;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MembershipResource extends Resource
{
    protected static ?string $model = Membership::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getModelLabel(): string
    {
        return __('Membership');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Memberships');
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
        return [
            //
        ];
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
