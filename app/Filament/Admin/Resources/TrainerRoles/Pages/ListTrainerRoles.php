<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TrainerRoles\Pages;

use App\Filament\Admin\Resources\TrainerRoles\TrainerRoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrainerRoles extends ListRecords
{
    protected static string $resource = TrainerRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
