<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogPostForm
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
                    ->prefix(url('/blog/'))
                    ->placeholder('my-blog-post-title')
                    ->helperText(__('app.slug_helper'))
                    ->hint(__('app.slug_hint'))
                    ->hintIcon('heroicon-m-question-mark-circle')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('slug', Str::slug($state));
                    })
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
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $currentSlug = $get('slug');
                                        if (blank($currentSlug) && filled($state)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->columnSpanFull(),

                                TextInput::make("excerpt.{$locale}")
                                    ->label(__('app.excerpt')." ({$locale})")
                                    ->required()
                                    ->maxLength(500)
                                    ->columnSpanFull(),

                                RichEditor::make("content.{$locale}")
                                    ->label(__('app.content')." ({$locale})")
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                        )->toArray()
                    )
                    ->columnSpanFull(),

                FileUpload::make('image')
                    ->label(__('app.image'))
                    ->image()
                    ->directory('blog-images')
                    ->columnSpan(1),

                Toggle::make('is_published')
                    ->label(__('app.is_published'))
                    ->default(false)
                    ->columnSpan(1),

                DateTimePicker::make('published_at')
                    ->label(__('app.published_at'))
                    ->default(now())
                    ->required()
                    ->columnSpan(1),
            ]);
    }
}
