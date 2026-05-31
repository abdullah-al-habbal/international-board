<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\StaticPages;

use App\Filament\Admin\Resources\StaticPages\Pages\CreateStaticPage;
use App\Filament\Admin\Resources\StaticPages\Pages\EditStaticPage;
use App\Filament\Admin\Resources\StaticPages\Pages\ListStaticPages;
use App\Filament\Admin\Resources\StaticPages\Pages\ViewStaticPage;
use App\Filament\Admin\Resources\StaticPages\Schemas\StaticPageForm;
use App\Filament\Admin\Resources\StaticPages\Schemas\StaticPageInfolist;
use App\Filament\Admin\Resources\StaticPages\Tables\StaticPagesTable;
use App\Models\StaticPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StaticPageResource extends Resource
{
    protected static ?string $model = StaticPage::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedDocument;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.content');
    }

    protected static ?int $navigationSort = 20;

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
        return __('app.static_pages');
    }

    public static function getModelLabel(): string
    {
        return __('app.static_page');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.static_pages');
    }

    public static function form(Schema $schema): Schema
    {
        return StaticPageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StaticPageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StaticPagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStaticPages::route('/'),
            'create' => CreateStaticPage::route('/create'),
            'view' => ViewStaticPage::route('/{record}'),
            'edit' => EditStaticPage::route('/{record}/edit'),
        ];
    }
}