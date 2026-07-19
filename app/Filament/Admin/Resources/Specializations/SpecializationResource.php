<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Specializations;

use App\Filament\Admin\Resources\Specializations\Pages\CreateSpecialization;
use App\Filament\Admin\Resources\Specializations\Pages\EditSpecialization;
use App\Filament\Admin\Resources\Specializations\Pages\ListSpecializations;
use App\Models\Specialization;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SpecializationResource extends Resource
{
    protected static ?string $model = Specialization::class;

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return 'heroicon-o-tag';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.settings');
    }

    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('app.specializations');
    }

    public static function getModelLabel(): string
    {
        return __('app.specialization');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.specializations');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label(__('app.name'))
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            Toggle::make('is_active')
                ->label(__('app.active'))
                ->default(true)
                ->inline(false)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')
                ->label(__('app.name'))
                ->searchable()
                ->sortable(),

            IconColumn::make('is_active')
                ->label(__('app.active'))
                ->boolean()
                ->sortable(),

            TextColumn::make('created_at')
                ->label(__('app.created_at'))
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])->defaultSort('name', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSpecializations::route('/'),
            'create' => CreateSpecialization::route('/create'),
            'edit' => EditSpecialization::route('/{record}/edit'),
        ];
    }
}
