<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainees\Pages;

use App\Filament\Admin\Resources\Trainees\TraineeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTrainee extends ViewRecord
{
    protected static string $resource =  null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
