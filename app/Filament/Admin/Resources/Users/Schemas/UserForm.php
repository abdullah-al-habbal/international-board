<?php
// app/Filament/Admin/Resources/Users/Schemas/UserForm.php
declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Enums\UserType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label(__('app.name'))
                ->required()
                ->maxLength(255)
                ->autofocus()
                ->columnSpan(1),

            TextInput::make('email')
                ->label(__('app.email'))
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->columnSpan(1),


            TextInput::make('password')
                ->label(__('app.password'))
                ->password()
                ->placeholder(__('app.placeholder_dash'))
                ->minLength(8)
                ->confirmed()
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $context) => $context === 'create')
                ->columnSpan(1),


            TextInput::make('password_confirmation')
                ->label(__('app.confirm_password'))
                ->password()
                ->placeholder(__('app.placeholder_dash'))
                ->minLength(8)
                ->dehydrated(false)
                ->columnSpan(1),

            Select::make('type')
                ->label(__('app.user_type'))
                ->options(UserType::class)
                ->default(UserType::Admin->value)
                ->required()
                ->columnSpan(1),
        ])->columns(2);
    }
}
