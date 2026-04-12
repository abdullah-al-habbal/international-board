<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PaymentAgentPersons\Pages;

use App\Filament\Admin\Resources\PaymentAgentPersonResource;
use Filament\Resources\Pages\EditRecord;

class EditPaymentAgentPerson extends EditRecord
{
    protected static string $resource = PaymentAgentPersonResource::class;
}
