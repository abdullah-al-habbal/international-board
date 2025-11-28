<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Enums\UserType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label(__('Name'))
                ->required()
                ->maxLength(255)
                ->autofocus()
                ->columnSpan(1),

            TextInput::make('email')
                ->label(__('Email'))
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->columnSpan(1),

            TextInput::make('password')
                ->label(__('Password'))
                ->password()
                ->required()
                ->minLength(8)
                ->confirmed()
                ->columnSpan(1),

            TextInput::make('password_confirmation')
                ->label(__('Confirm Password'))
                ->password()
                ->required()
                ->minLength(8)
                ->columnSpan(1),

            Select::make('type')
                ->label(__('User Type'))
                ->options(UserType::class)
                ->default(UserType::Admin->value)
                ->required()
                ->columnSpan(1),

            Toggle::make('email_verified')
                ->label(__('Email Verified'))
                ->default(false)
                ->columnSpan(1),
        ])->columns(2);
    }
}
