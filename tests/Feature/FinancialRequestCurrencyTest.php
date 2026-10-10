<?php

declare(strict_types=1);

use App\Models\Currency;
use App\Models\FinancialRequest;
use App\Models\Trainer;

it('creates a financial request with currency_id', function () {
    $currency = Currency::factory()->create();
    $trainer = Trainer::factory()->create();

    $request = FinancialRequest::factory()->forTrainer()->create([
        'requestable_id' => $trainer->id,
        'currency_id' => $currency->id,
        'total_payment' => 1000.00,
        'amount_paid' => 500.00,
    ]);

    expect($request->currency_id)->toBe($currency->id)
        ->and($request->total_payment)->toBe('1000.00')
        ->and($request->amount_paid)->toBe('500.00');
});

it('loads the currency relationship', function () {
    $currency = Currency::factory()->create(['code' => 'TST']);
    $trainer = Trainer::factory()->create();

    $request = FinancialRequest::factory()->forTrainer()->create([
        'requestable_id' => $trainer->id,
        'currency_id' => $currency->id,
    ]);

    expect($request->currency)->not->toBeNull()
        ->and($request->currency->code)->toBe('TST');
});

it('allows null currency_id for backward compatibility', function () {
    $trainer = Trainer::factory()->create();

    $request = FinancialRequest::factory()->forTrainer()->create([
        'requestable_id' => $trainer->id,
        'currency_id' => null,
    ]);

    expect($request->currency_id)->toBeNull()
        ->and($request->currency)->toBeNull();
});

it('calculates remaining amount correctly regardless of currency', function () {
    $usd = Currency::factory()->create(['code' => 'USA']);
    $syp = Currency::factory()->create(['code' => 'SYA']);
    $trainer = Trainer::factory()->create();

    $usdRequest = FinancialRequest::factory()->forTrainer()->create([
        'requestable_id' => $trainer->id,
        'currency_id' => $usd->id,
        'total_payment' => 1000.00,
        'amount_paid' => 300.00,
    ]);

    $sypRequest = FinancialRequest::factory()->forTrainer()->create([
        'requestable_id' => $trainer->id,
        'currency_id' => $syp->id,
        'total_payment' => 500000.00,
        'amount_paid' => 200000.00,
    ]);

    // remaining_amount is a fixed-scale decimal string, not a float.
    expect($usdRequest->remaining_amount)->toBe('700.00')
        ->and($sypRequest->remaining_amount)->toBe('300000.00');
});

it('backs financial request with default currency', function () {
    Currency::query()->delete();
    $currency = Currency::factory()->default()->create();
    $trainer = Trainer::factory()->create();

    $request = FinancialRequest::factory()->forTrainer()->create([
        'requestable_id' => $trainer->id,
        'currency_id' => $currency->id,
    ]);

    expect($request->currency->code)->toBe('USD')
        ->and($request->currency->is_default)->toBeTrue();
});

it('seed currencies table contains USD and SYP', function () {
    Currency::query()->delete();
    Currency::factory()->default()->create();
    Currency::factory()->create(['code' => 'SYR']);

    expect(Currency::count())->toBe(2)
        ->and(Currency::where('code', 'USD')->first()->is_default)->toBeTrue()
        ->and(Currency::where('code', 'SYR')->first()->is_default)->toBeFalse();
});

it('stores localized name and symbol as translatable arrays', function () {
    $currency = Currency::create([
        'name' => ['en' => 'Euro', 'ar' => 'البيورو'],
        'code' => 'EUR',
        'symbol' => ['en' => 'EUR', 'ar' => '€'],
        'is_default' => false,
    ]);

    expect($currency->getTranslation('name', 'en'))->toBe('Euro')
        ->and($currency->getTranslation('name', 'ar'))->toBe('البيورو')
        ->and($currency->getTranslation('symbol', 'en'))->toBe('EUR')
        ->and($currency->getTranslation('symbol', 'ar'))->toBe('€');
});
