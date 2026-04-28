<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function isSensitiveChange(): bool
    {
        return filled($this->data['password'] ?? null)
            || $this->record->getOriginal('email') !== $this->record->email;
    }

    protected function afterSave(): void
    {
        $auth = Filament::auth();
        $user = $this->record;

        if ($auth->check() && $auth->id() === $user->id && $this->isSensitiveChange()) {

            Notification::make()
                ->title(__('app.security_update_title'))
                ->body(__('app.security_update_body'))
                ->warning()
                ->send();

            $auth->logout();

            request()->session()->invalidate();
            request()->session()->regenerateToken();

            $this->redirect(Filament::getLoginUrl());
        }
    }
}
