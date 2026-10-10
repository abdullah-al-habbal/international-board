<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\FinancialRequest;
use App\Support\Money;
use DomainException;

/**
 * Domain invariants for financial amounts.
 *
 * Filament's `minValue()` / `maxValue()` only guard the panel forms; this runs
 * on every write to the model, so the same rules hold for seeders, factories,
 * imports, tinker and any future entry point:
 *
 *   total_payment > 0
 *   amount_paid  >= 0
 *   amount_paid  <= total_payment
 */
class FinancialRequestObserver
{
    public function creating(FinancialRequest $request): void
    {
        $this->assertAmountsAreCoherent($request);
    }

    public function updating(FinancialRequest $request): void
    {
        if (! $request->isDirty(['total_payment', 'amount_paid'])) {
            return;
        }

        $this->assertAmountsAreCoherent($request);
    }

    private function assertAmountsAreCoherent(FinancialRequest $request): void
    {
        $total = $request->total_payment;
        $paid = $request->amount_paid;

        if (! Money::isPositive($total)) {
            throw new DomainException(__('financial.errors.total_payment_not_positive'));
        }

        if (Money::isNegative($paid)) {
            throw new DomainException(__('financial.errors.amount_paid_negative'));
        }

        if (Money::isGreaterThan($paid, $total)) {
            throw new DomainException(__('financial.errors.amount_paid_exceeds_total', [
                'paid' => Money::normalize($paid),
                'total' => Money::normalize($total),
            ]));
        }
    }
}
