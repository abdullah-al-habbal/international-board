<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\StaticPages\Pages;

use App\Filament\Admin\Resources\StaticPages\StaticPageResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStaticPage extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = StaticPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
