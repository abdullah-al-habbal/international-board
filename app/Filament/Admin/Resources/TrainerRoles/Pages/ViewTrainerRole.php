<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerRoles\Pages;

use App\Filament\Admin\Resources\TrainerRoles\TrainerRoleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTrainerRole extends ViewRecord
{
    protected static string $resource = TrainerRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
