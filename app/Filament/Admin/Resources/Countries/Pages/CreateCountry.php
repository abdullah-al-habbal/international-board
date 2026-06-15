<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Countries\Pages;

use App\Filament\Admin\Resources\Countries\CountryResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\CreateRecord;

class CreateCountry extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = CountryResource::class;
}
