<?php

declare(strict_types=1);

namespace App\Filament\Center\Resources\Trainees\Pages;

use App\Filament\Center\Resources\Trainees\TraineeResource;
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
        ];
    }
}
