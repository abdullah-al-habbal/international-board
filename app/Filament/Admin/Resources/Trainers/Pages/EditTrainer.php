<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainers\Pages;

use App\Filament\Admin\Resources\Trainers\TrainerResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrainer extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TrainerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
