<?php

namespace App\Filament\Admin\Resources\StaticPages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StaticPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->label(__('app.slug'))
                    ->required(),
                TextInput::make('title')
                    ->label(__('app.title'))
                    ->required(),
                FileUpload::make('image')
                    ->label(__('app.image'))
                    ->image(),
                TextInput::make('content')
                    ->label(__('app.content'))
                    ->required(),
                Toggle::make('is_active')
                    ->label(__('app.is_active'))
                    ->required(),
            ]);
    }
}
