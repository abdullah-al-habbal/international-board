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
    $currency = Currency::factory()->create(['code' => 'USD']);
    $trainer = Trainer::factory()->create();

    $request = FinancialRequest::factory()->forTrainer()->create([
        'requestable_id' => $trainer->id,
        'currency_id' => $currency->id,
    ]);

    expect($request->currency)->not->toBeNull()
        ->and($request->currency->code)->toBe('USD');
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
    $usd = Currency::factory()->create(['code' => 'USD']);
    $syp = Currency::factory()->create(['code' => 'SYP']);
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

    expect($usdRequest->remaining_amount)->toBe(700.00)
        ->and($sypRequest->remaining_amount)->toBe(300000.00);
});

it('backs financial request with USD currency', function () {
    $usd = Currency::factory()->create(['code' => 'USD', 'is_default' => true]);
    $trainer = Trainer::factory()->create();

    $request = FinancialRequest::factory()->forTrainer()->create([
        'requestable_id' => $trainer->id,
        'currency_id' => $usd->id,
    ]);

    expect($request->currency->code)->toBe('USD')
        ->and($request->currency->is_default)->toBeTrue();
});

it('seed currencies table contains USD and SYP', function () {
    Currency::factory()->create(['code' => 'USD', 'is_default' => true]);
    Currency::factory()->create(['code' => 'SYP', 'is_default' => false]);

    expect(Currency::count())->toBe(2)
        ->and(Currency::where('code', 'USD')->first()->is_default)->toBeTrue()
        ->and(Currency::where('code', 'SYP')->first()->is_default)->toBeFalse();
});
