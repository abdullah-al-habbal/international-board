<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerRoles\Pages;

use App\Filament\Admin\Resources\TrainerRoles\TrainerRoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainerRole extends CreateRecord
{
    protected static string $resource = TrainerRoleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
