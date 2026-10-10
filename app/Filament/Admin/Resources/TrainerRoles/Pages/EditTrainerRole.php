<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerRoles\Pages;

use App\Filament\Admin\Resources\TrainerRoles\TrainerRoleResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTrainerRole extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TrainerRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
