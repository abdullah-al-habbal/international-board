<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Countries;

use App\Filament\Admin\Resources\Countries\Pages\CreateCountry;
use App\Filament\Admin\Resources\Countries\Pages\EditCountry;
use App\Filament\Admin\Resources\Countries\Pages\ListCountries;
use App\Filament\Admin\Resources\Countries\Pages\ViewCountry;
use App\Filament\Admin\Resources\Countries\Schemas\CountryForm;
use App\Filament\Admin\Resources\Countries\Schemas\CountryInfolist;
use App\Filament\Admin\Resources\Countries\Tables\CountriesTable;
use App\Models\Country;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('app.countries');
    }

    public static function getModelLabel(): string
    {
        return __('app.country');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.countries');
    }

    public static function form(Schema $schema): Schema
    {
        return CountryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CountryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CountriesTable::configure($table);
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
            'index' => ListCountries::route('/'),
            'create' => CreateCountry::route('/create'),
            'view' => ViewCountry::route('/{record}'),
            'edit' => EditCountry::route('/{record}/edit'),
        ];
    }
}
