<?php

namespace App\Filament\Admin\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->label(__('app.slug'))
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('title')
                    ->label(__('app.title'))
                    ->required(),
                TextInput::make('excerpt')
                    ->label(__('app.excerpt')),
                RichEditor::make('content')
                    ->label(__('app.content'))
                    ->required(),
                FileUpload::make('image')
                    ->label(__('app.image'))
                    ->image(),
                DateTimePicker::make('published_at')
                    ->label(__('app.published_at')),
                Toggle::make('is_published')
                    ->label(__('app.is_published'))
                    ->required(),
            ]);
    }
}
