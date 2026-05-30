<?php

namespace App\Filament\Admin\Resources\StaticPages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StaticPageForm
{
    public static function configure(Schema $schema): Schema
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
                                RichEditor::make("content.{$locale}")
                                    ->label(__('app.content') . " ({$locale})")
                                    ->required(),
                            ])
                        )->toArray()
                    ),

                FileUpload::make('image')
                    ->label(__('app.image'))
                    ->image(),

                Toggle::make('is_active')
                    ->label(__('app.is_active'))
                    ->default(true),
            ]);
    }
}
