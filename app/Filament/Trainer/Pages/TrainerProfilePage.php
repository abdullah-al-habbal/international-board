<?php

declare(strict_types=1);

namespace App\Filament\Trainer\Pages;

use Filament\Auth\Pages\EditProfile;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;

class TrainerProfilePage extends EditProfile
{
    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label(__('filament-panels::auth/pages/edit-profile.form.name.label'))
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::auth/pages/edit-profile.form.email.label'))
            ->email()
            ->required()
            ->maxLength(255)
            ->unique(ignoreRecord: true)
            ->live(debounce: 500);
    }

    protected function getPasswordFormComponent(): ?Component
    {
        return null;
    }

    protected function getPasswordConfirmationFormComponent(): ?Component
    {
        return null;
    }

    protected function getCurrentPasswordFormComponent(): ?Component
    {
        return null;
    }
}
