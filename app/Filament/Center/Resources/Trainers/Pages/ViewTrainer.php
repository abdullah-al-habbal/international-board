<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Trainers\Pages;

use App\Filament\Center\Resources\Trainers\TrainerResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTrainer extends ViewRecord
{
    protected static string $resource = TrainerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
