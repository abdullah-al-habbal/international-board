<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Membership\Pages;

use App\Filament\Admin\Resources\MembershipResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\CreateRecord;

class CreateMembership extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = MembershipResource::class;
}