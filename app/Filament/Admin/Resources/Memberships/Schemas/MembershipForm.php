<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Memberships\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MembershipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('title')
                ->label(__('Title'))
                ->required()
                ->maxLength(255)
                ->autofocus()
                ->columnSpanFull(),

            Textarea::make('description')
                ->label(__('Description'))
                ->maxLength(65535)
                ->rows(4)
                ->nullable()
                ->columnSpanFull(),

            FileUpload::make('descriptive_image')
                ->label(__('Descriptive Image'))
                ->image()
                ->directory('memberships/images')
                ->visibility('public')
                ->nullable()
                ->columnSpan(1),

            TextInput::make('sort_order')
                ->label(__('Sort Order'))
                ->numeric()
                ->default(0)
                ->required()
                ->columnSpan(1),

            Toggle::make('is_active')
                ->label(__('Active'))
                ->default(true)
                ->inline(false)
                ->columnSpanFull(),
        ])->columns(2);
    }
}
