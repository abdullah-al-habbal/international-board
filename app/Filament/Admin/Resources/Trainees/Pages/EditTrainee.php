<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainees\Pages;

use App\Filament\Admin\Resources\Trainees\TraineeResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrainee extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = TraineeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
