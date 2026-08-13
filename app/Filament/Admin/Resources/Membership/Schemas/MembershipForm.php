<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Membership\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class MembershipForm
{
    public static function configure(Schema $schema): Schema
    {
        $locales = ['ar', 'en'];

        return $schema
            ->schema([
                TextInput::make('slug')
                    ->label(__('app.slug'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),

                Tabs::make('translations')
                    ->label(__('app.translations'))
                    ->tabs(
                        collect($locales)->map(fn ($locale) => Tab::make(strtoupper($locale))
                            ->schema([
                                TextInput::make("title.{$locale}")
                                    ->label(__('app.title')." ({$locale})")
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                RichEditor::make("description.{$locale}")
                                    ->label(__('app.description')." ({$locale})")
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                        )->toArray()
                    )
                    ->columnSpanFull(),
            ]);
    }
}
