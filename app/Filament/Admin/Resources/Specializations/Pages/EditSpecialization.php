<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Specializations\Pages;

use App\Filament\Admin\Resources\Specializations\SpecializationResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSpecialization extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = SpecializationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
