<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\Membership\Pages\CreateMembership;
use App\Filament\Admin\Resources\Membership\Pages\EditMembership;
use App\Filament\Admin\Resources\Membership\Pages\ListMemberships;
use App\Filament\Admin\Resources\Membership\Pages\ViewMembership;
use App\Models\Membership;
use BackedEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
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

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        $locales = ['ar', 'en'];

        return $schema
            ->components([
                TextInput::make('slug')
                    ->label(__('app.slug'))
                    ->required()
                    ->unique(ignoreRecord: true),

                Tabs::make('Translations')
                    ->tabs(
                        collect($locales)->map(fn ($locale) => Tab::make(strtoupper($locale))
                            ->schema([
                                TextInput::make("title.{$locale}")
                                    ->label(__('app.title') . " ({$locale})")
                                    ->required(),
                                RichEditor::make("description.{$locale}")
                                    ->label(__('app.description') . " ({$locale})")
                                    ->required(),
                            ])
                        )->toArray()
                    ),

                Toggle::make('is_active')
                    ->label(__('app.is_active'))
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('app.title'))
                    ->searchable(query: function ($query, $search) {
                        return $query->where('title->' . app()->getLocale(), 'like', "%{$search}%");
                    }),
                TextColumn::make('slug')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([])
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
