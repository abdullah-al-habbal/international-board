<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Trainers\Pages;

use App\Filament\Center\Resources\Trainers\TrainerResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTrainer extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TrainerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
