<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Currency;
use DomainException;

/**
 * Currencies are reference data for historical financial records. Removing one
 * that is still referenced would rewrite that history, so deletion is refused
 * at the domain boundary — the same guarantee the `restrict` foreign key gives
 * at the database boundary, but with a translated message instead of a driver
 * error.
 */
class CurrencyObserver
{
    public function deleting(Currency $currency): void
    {
        if ($currency->financialRequests()->exists()) {
            throw new DomainException(__('financial.errors.currency_in_use', [
                'code' => (string) $currency->code,
            ]));
        }
    }
}
