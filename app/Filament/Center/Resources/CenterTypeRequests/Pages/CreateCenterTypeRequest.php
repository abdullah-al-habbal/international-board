<?php

namespace App\Filament\Center\Resources\CenterTypeRequests\Pages;

use App\Enums\CenterTypeRequestStatus;
use App\Filament\Center\Resources\CenterTypeRequests\CenterTypeRequestResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateCenterTypeRequest extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = CenterTypeRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['certified_center_id'] = Auth::guard('web')->id();
        $data['status'] = CenterTypeRequestStatus::Pending->value;

        return $data;
    }
}
