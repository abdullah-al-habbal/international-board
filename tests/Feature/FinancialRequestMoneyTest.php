<?php

declare(strict_types=1);

use App\Models\Currency;
use App\Models\FinancialRequest;
use App\Models\Trainer;
use App\Support\Money;
use Illuminate\Database\QueryException;

function financialRequest(array $attributes = []): FinancialRequest
{
    return FinancialRequest::factory()
        ->for(Trainer::factory(), 'requestable')
        ->create($attributes);
}

describe('remaining amount precision', function () {
    it('subtracts decimals without float drift', function (string $total, string $paid, string $remaining) {
        $request = financialRequest(['total_payment' => $total, 'amount_paid' => $paid]);

        expect($request->remaining_amount)->toBe($remaining);
    })->with([
        ['100.00', '0.10', '99.90'],
        ['100000.00', '25000.00', '75000.00'],
        ['0.30', '0.10', '0.20'],
        ['4.35', '4.34', '0.01'],
        ['9999999999.99', '0.01', '9999999999.98'],
        ['500.00', '500.00', '0.00'],
    ]);

    it('returns the remaining amount as a fixed-scale decimal string', function () {
        $request = financialRequest(['total_payment' => '1000.00', 'amount_paid' => '500.00']);

        expect($request->remaining_amount)->toBeString()->toBe('500.00');
    });

    it('recomputes the remaining amount from persisted values, not from the write', function () {
        $request = financialRequest(['total_payment' => '100.00', 'amount_paid' => '0.10']);

        // Re-read so the values come back through the decimal:2 cast.
        expect($request->fresh()->remaining_amount)->toBe('99.90');
    });

    it('appends the remaining amount to the serialized model', function () {
        $request = financialRequest(['total_payment' => '250.00', 'amount_paid' => '100.00']);

        expect($request->toArray())->toHaveKey('remaining_amount')
            ->and($request->toArray()['remaining_amount'])->toBe('150.00');
    });
});

describe('domain invariants', function () {
    it('refuses a paid amount above the total on create', function () {
        expect(fn () => financialRequest(['total_payment' => '100.00', 'amount_paid' => '100.01']))
            ->toThrow(DomainException::class);

        expect(FinancialRequest::query()->count())->toBe(0);
    });

    it('refuses a paid amount above the total on update', function () {
        $request = financialRequest(['total_payment' => '100.00', 'amount_paid' => '50.00']);

        expect(fn () => $request->update(['amount_paid' => '100.01']))
            ->toThrow(DomainException::class);

        expect($request->fresh()->amount_paid)->toBe('50.00');
    });

    it('refuses a paid amount above the total when the total is lowered', function () {
        $request = financialRequest(['total_payment' => '100.00', 'amount_paid' => '80.00']);

        expect(fn () => $request->update(['total_payment' => '50.00']))
            ->toThrow(DomainException::class);
    });

    it('accepts a fully paid request', function () {
        $request = financialRequest(['total_payment' => '100.00', 'amount_paid' => '100.00']);

        expect($request->remaining_amount)->toBe('0.00');
    });

    it('rejects a non-positive total', function (string $total) {
        expect(fn () => financialRequest(['total_payment' => $total, 'amount_paid' => '0.00']))
            ->toThrow(DomainException::class);
    })->with([['0.00'], ['-1.00']]);

    it('rejects a negative paid amount', function () {
        expect(fn () => financialRequest(['total_payment' => '100.00', 'amount_paid' => '-0.01']))
            ->toThrow(DomainException::class);
    });

    it('does not re-validate amounts when only other fields change', function () {
        $request = financialRequest(['total_payment' => '100.00', 'amount_paid' => '25.00']);

        $request->update(['reason' => 'Updated note']);

        expect($request->fresh()->reason)->toBe('Updated note');
    });

    it('enforces the invariant outside the panel forms', function () {
        // The Filament schemas are only one entry point; a direct model write
        // must be rejected too.
        expect(fn () => FinancialRequest::query()->create([
            'requestable_type' => Trainer::class,
            'requestable_id' => Trainer::factory()->create()->id,
            'total_payment' => '10.00',
            'amount_paid' => '10.01',
            'date' => now()->toDateString(),
        ]))->toThrow(DomainException::class);
    });
});

describe('currency relationship', function () {
    it('exposes currency_id and the currency relation', function () {
        $currency = Currency::factory()->create(['code' => 'ABC']);
        $request = financialRequest(['currency_id' => $currency->id]);

        expect($request->currency_id)->toBe($currency->id)
            ->and($request->currency->code)->toBe('ABC');
    });

    it('falls back to the configured code when no currency is set', function () {
        $request = financialRequest(['currency_id' => null]);

        expect($request->currency_id)->toBeNull()
            ->and($request->currencyCode())->toBe(config('currencies.fallback_code'));
    });

    it('refuses to delete a currency referenced by a financial request', function () {
        $currency = Currency::factory()->create(['code' => 'XYZ']);
        financialRequest(['currency_id' => $currency->id]);

        expect(fn () => $currency->delete())->toThrow(DomainException::class)
            ->and(Currency::query()->whereKey($currency->id)->exists())->toBeTrue();
    });

    it('restricts the delete at the database boundary too', function () {
        $currency = Currency::factory()->create(['code' => 'DBR']);
        financialRequest(['currency_id' => $currency->id]);

        // Bypass the observer to prove the foreign key is `restrict`, not
        // `set null`: historical amounts keep their denomination even if the
        // domain guard is circumvented.
        expect(fn () => DB::table('currencies')->where('id', $currency->id)->delete())
            ->toThrow(QueryException::class);
    });

    it('allows deleting an unreferenced currency', function () {
        $currency = Currency::factory()->create(['code' => 'FRE']);

        $currency->delete();

        expect(Currency::query()->whereKey($currency->id)->exists())->toBeFalse();
    });

    it('ships the configured reference currencies through the migration', function () {
        // Deployment runs `migrate --force` only, never a seeder, so the
        // currencies the selector needs have to arrive with the schema.
        expect(Currency::query()->pluck('code')->all())
            ->toContain('USD')
            ->toContain('SYP')
            ->and(Currency::query()->where('code', 'USD')->first()->getTranslation('name', 'ar'))
            ->toBe('الدولار الأمريكي');
    });
});

it('keeps the model and the UI on one calculation', function () {
    $request = financialRequest(['total_payment' => '100000.00', 'amount_paid' => '25000.00']);

    // The live form preview calls the same helper the accessor does.
    expect(Money::subtract('100000.00', '25000.00'))->toBe($request->remaining_amount);
});
