<?php

namespace App\Filament\Admin\Resources\Membership\Pages;

use App\Filament\Admin\Resources\MembershipResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMembership extends CreateRecord
{
    protected static string $resource = MembershipResource::class;
}
