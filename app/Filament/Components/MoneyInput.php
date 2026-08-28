<?php

declare(strict_types=1);

namespace App\Filament\Components;

use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

/**
 * Money-aware text input.
 *
 * Wraps the Alpine `$money` mask that Filament v4 documents for `TextInput`
 * (shipped through Livewire's bundled `@alpinejs/mask`), so an operator types
 * `100000` and reads `100,000.00`. `stripCharacters(',')` removes the mask's
 * thousands separators through a state cast, so validation and the database
 * receive a plain decimal.
 *
 * `dir="ltr"` is set explicitly: amounts must read left-to-right even when the
 * panel renders in Arabic, otherwise the sign and the separators land on the
 * wrong side of the number.
 */
final class MoneyInput extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->numeric()
            ->mask(RawJs::make('$money($input)'))
            ->stripCharacters(',')
            ->step(0.01)
            ->minValue(0)
            ->extraInputAttributes(['dir' => 'ltr'], merge: true)
            ->live(onBlur: true);
    }
}
