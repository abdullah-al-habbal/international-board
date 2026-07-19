<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\StaticPages\Pages;

use App\Filament\Admin\Resources\StaticPages\StaticPageResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\CreateRecord;

class CreateStaticPage extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = StaticPageResource::class;
}
