<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PaymentAgentPersons\Pages;

use App\Filament\Admin\Resources\PaymentAgentPersons\PaymentAgentPersonResource;
use App\Filament\Traits\RedirectsToShowPage;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentAgentPerson extends CreateRecord
{
    use RedirectsToShowPage;

    protected static string $resource = PaymentAgentPersonResource::class;
}
