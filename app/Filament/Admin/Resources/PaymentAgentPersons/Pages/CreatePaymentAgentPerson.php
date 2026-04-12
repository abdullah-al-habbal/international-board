<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PaymentAgentPersons\Pages;

use App\Filament\Admin\Resources\PaymentAgentPersonResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentAgentPerson extends CreateRecord
{
    protected static string $resource = PaymentAgentPersonResource::class;
}
