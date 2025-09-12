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
                    ->required(),
                TextInput::make('title')
                    ->required(),
                FileUpload::make('image')
                    ->image(),
                TextInput::make('content')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
