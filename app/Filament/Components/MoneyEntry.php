<?php

declare(strict_types=1);

namespace App\Filament\Components;

use App\Models\Currency;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Model;

/**
 * Infolist entry counterpart of {@see MoneyColumn}.
 *
 * Read-only financial values are displayed with this rather than with a
 * disabled `TextInput`: they are never edited, and `TextInput` has no `money()`
 * formatter in Filament v4.
 */
final class MoneyEntry extends TextEntry
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->money(static fn (Model $record): string => $record->currency?->code ?? Currency::fallbackCode());
    }
}
