<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Specializations;

use App\Filament\Admin\Resources\Specializations\Pages\CreateSpecialization;
use App\Filament\Admin\Resources\Specializations\Pages\EditSpecialization;
use App\Filament\Admin\Resources\Specializations\Pages\ListSpecializations;
use App\Models\Specialization;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
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

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name.en')
                ->label(__('app.name_english'))
                ->required()
                ->maxLength(255),

            TextInput::make('name.ar')
                ->label(__('app.name_arabic'))
                ->required()
                ->maxLength(255),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name_en')
                ->label(__('app.name_english'))
                ->state(fn ($record) => $record->getTranslation('name', 'en') ?? __('app.no_english'))
                ->searchable(query: fn ($query, $search) => $query->where('name->en', 'like', "%{$search}%"))
                ->sortable(query: fn ($query, $direction) => $query->orderBy('name->en', $direction)),

            TextColumn::make('name_ar')
                ->label(__('app.name_arabic'))
                ->state(fn ($record) => $record->getTranslation('name', 'ar') ?? __('app.no_arabic'))
                ->searchable(query: fn ($query, $search) => $query->where('name->ar', 'like', "%{$search}%"))
                ->sortable(query: fn ($query, $direction) => $query->orderBy('name->ar', $direction)),

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
