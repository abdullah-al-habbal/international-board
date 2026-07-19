<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Specializations\Pages;

use App\Filament\Admin\Resources\Specializations\SpecializationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpecializations extends ListRecords
{
    protected static string $resource = SpecializationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
