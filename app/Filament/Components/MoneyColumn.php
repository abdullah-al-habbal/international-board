<?php

declare(strict_types=1);

namespace App\Filament\Components;

use App\Models\Currency;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

/**
 * Table column that formats an amount in its own record's currency.
 *
 * Replaces the `->money(fn ($record) => $record->currency?->code ?? 'USD')`
 * closure that was repeated at every financial call site. The currency is read
 * from the eager-loaded `currency` relation, so no query is issued per row; a
 * caller that forgets to eager-load trips `preventLazyLoading` and fails loudly
 * rather than degrading into an N+1.
 */
final class MoneyColumn extends TextColumn
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->money(static fn (Model $record): string => $record->currency?->code ?? Currency::fallbackCode());
    }
}
