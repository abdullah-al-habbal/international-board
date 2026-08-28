<?php

declare(strict_types=1);

use App\Support\Money;

it('subtracts decimals exactly', function (string $minuend, string $subtrahend, string $expected) {
    expect(Money::subtract($minuend, $subtrahend))->toBe($expected);
})->with([
    ['100.00', '0.10', '99.90'],
    ['0.30', '0.10', '0.20'],
    ['100000.00', '25000.00', '75000.00'],
    ['1000.00', '999.99', '0.01'],
    // DECIMAL(12,2) upper bound: no precision is lost at the top of the range.
    ['9999999999.99', '0.01', '9999999999.98'],
    // A fully paid request leaves nothing.
    ['4.35', '4.35', '0.00'],
    // Overpayment is blocked by the domain, but the arithmetic still signs.
    ['10.00', '10.10', '-0.10'],
]);

it('adds decimals exactly', function () {
    expect(Money::add('0.1', '0.2'))->toBe('0.30')
        ->and(Money::add('0.1', '0.7'))->toBe('0.80')
        ->and(Money::add('99999.99', '0.01'))->toBe('100000.00');
});

it('avoids the binary float artefact that a naive cast hits', function () {
    // The canonical failure: 4.35 has no exact binary representation, so
    // 4.35 * 100 lands just under 435 and truncating gives 434.
    expect((int) (4.35 * 100))->toBe(434)
        ->and(Money::toMinorUnits('4.35'))->toBe(435)
        ->and(Money::subtract('4.35', '0.01'))->toBe('4.34');
});

it('normalizes user and database input to a fixed scale', function (int|float|string|null $input, string $expected) {
    expect(Money::normalize($input))->toBe($expected);
})->with([
    // Values as the $money mask submits them.
    ['1,000.5', '1000.50'],
    ['100,000.00', '100000.00'],
    ['1 000,00', '100000.00'],
    // Values as Eloquent's decimal:2 cast returns them.
    ['1000.50', '1000.50'],
    // Plain PHP input from factories and seeders.
    [1000, '1000.00'],
    [1000.5, '1000.50'],
    [null, '0.00'],
    ['', '0.00'],
    // Fractions shorter or longer than the scale.
    ['.5', '0.50'],
    ['7', '7.00'],
    ['1.005', '1.01'],
    ['1.004', '1.00'],
    ['1.999', '2.00'],
    // Negative, including the Unicode minus sign.
    ['-0.10', '-0.10'],
    ["\u{2212}0.10", '-0.10'],
]);

it('normalizes Arabic-Indic digits and separators', function () {
    // An RTL locale can render amounts with Arabic-Indic digits, an Arabic
    // decimal separator (U+066B) and an Arabic thousands separator (U+066C).
    expect(Money::normalize("\u{0661}\u{0660}\u{0660}\u{0660}\u{066B}\u{0665}\u{0660}"))->toBe('1000.50')
        ->and(Money::normalize("\u{0661}\u{066C}\u{0660}\u{0660}\u{0660}"))->toBe('1000.00');
});

it('compares amounts without float drift', function () {
    expect(Money::isGreaterThan('0.11', '0.10'))->toBeTrue()
        ->and(Money::isGreaterThan('0.10', '0.10'))->toBeFalse()
        ->and(Money::isGreaterThan('0.10', '0.11'))->toBeFalse()
        ->and(Money::compare('1000.00', '1000.000'))->toBe(0)
        ->and(Money::isPositive('0.00'))->toBeFalse()
        ->and(Money::isPositive('0.01'))->toBeTrue()
        ->and(Money::isNegative('-0.01'))->toBeTrue()
        ->and(Money::isNegative('0.00'))->toBeFalse();
});
