<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainees\Pages;

use App\Filament\Admin\Resources\Trainees\TraineeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrainee extends EditRecord
{
    protected static string $resource =  null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
