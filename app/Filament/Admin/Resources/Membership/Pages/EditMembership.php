<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Membership\Pages;

use App\Filament\Admin\Resources\MembershipResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMembership extends EditRecord
{
    use RedirectsToShowPage;

    protected static string $resource = MembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
