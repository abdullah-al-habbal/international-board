<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Trainers\Pages;

use App\Filament\Admin\Resources\Trainers\TrainerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainer extends CreateRecord
{
    protected static string $resource =  null;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
