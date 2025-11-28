<?php

namespace App\Filament\Admin\Resources\ApplicationSettings;

use App\Filament\Admin\Resources\ApplicationSettings\Pages\CreateApplicationSetting;
use App\Filament\Admin\Resources\ApplicationSettings\Pages\EditApplicationSetting;
use App\Filament\Admin\Resources\ApplicationSettings\Pages\ListApplicationSettings;
use App\Filament\Admin\Resources\ApplicationSettings\Pages\ViewApplicationSetting;
use App\Filament\Admin\Resources\ApplicationSettings\Schemas\ApplicationSettingForm;
use App\Filament\Admin\Resources\ApplicationSettings\Schemas\ApplicationSettingInfolist;
use App\Filament\Admin\Resources\ApplicationSettings\Tables\ApplicationSettingsTable;
use App\Models\ApplicationSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ApplicationSettingResource extends Resource
{
    protected static ?string $model = ApplicationSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'key';

    public static function getNavigationLabel(): string
    {
        return __('app.application_settings');
    }

    public static function getModelLabel(): string
    {
        return __('app.application_setting');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.application_settings');
    }

    public static function form(Schema $schema): Schema
    {
        return ApplicationSettingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ApplicationSettingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApplicationSettingsTable::configure($table);
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
            'index' => ListApplicationSettings::route('/'),
            'create' => CreateApplicationSetting::route('/create'),
            'view' => ViewApplicationSetting::route('/{record}'),
            'edit' => EditApplicationSetting::route('/{record}/edit'),
        ];
    }
}
