<?php

declare(strict_types=1);

namespace App\Filament\FinancialRequests;

use App\Filament\Components\MoneyColumn;
use App\Filament\Components\MoneyEntry;
use App\Filament\Components\MoneyInput;
use App\Models\Currency;
use App\Support\Money;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class FinancialRequestFields
{
    public static function amountFields(): array
    {
        $currencies = Currency::query()
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'is_default']);

        $codes = $currencies->pluck('code', 'id');
        $codeFor = static fn (mixed $currencyId): string => $codes->get((int) $currencyId)
            ?? Currency::fallbackCode();

        return [
            self::currencySelect($currencies),

            MoneyInput::make('total_payment')
                ->label(__('app.total_amount'))
                ->required()
                ->minValue(0.01)
                ->suffix(static fn (Get $get): string => $codeFor($get('currency_id'))),

            MoneyInput::make('amount_paid')
                ->label(__('app.paid_amount'))
                ->required()
                ->maxValue(static fn (Get $get): ?string => filled($get('total_payment'))
                    ? Money::normalize($get('total_payment'))
                    : null)
                ->suffix(static fn (Get $get): string => $codeFor($get('currency_id'))),

            self::remainingAmountPreview($codeFor),
        ];
    }

    public static function amountColumns(): array
    {
        return [
            MoneyColumn::make('total_payment')
                ->label(__('app.total_amount'))
                ->sortable(),

            MoneyColumn::make('amount_paid')
                ->label(__('app.paid_amount'))
                ->sortable(),

            MoneyColumn::make('remaining_amount')
                ->label(__('app.remaining_amount'))

                ->sortable(query: static fn (Builder $query, string $direction): Builder => $query
                    ->orderByRaw('(total_payment - amount_paid) '.($direction === 'desc' ? 'desc' : 'asc'))),
        ];
    }

    public static function amountEntries(): array
    {
        return [
            MoneyEntry::make('total_payment')
                ->label(__('app.total_amount')),

            MoneyEntry::make('amount_paid')
                ->label(__('app.paid_amount')),

            MoneyEntry::make('remaining_amount')
                ->label(__('app.remaining_amount')),
        ];
    }

    public static function agentPersonSelect(): Select
    {
        return Select::make('agent_person_id')
            ->label(__('app.agent_person'))
            ->relationship('agentPerson', 'name')
            ->searchable()
            ->preload()
            ->required();
    }

    public static function requestableSelect(string $model, string $label): Select
    {
        return Select::make('requestable_id')
            ->label($label)
            ->required()
            ->searchable()
            ->getSearchResultsUsing(static fn (string $search): array => $model::query()
                ->where('name', 'like', "%{$search}%")
                ->orderBy('name')
                ->limit(50)
                ->pluck('name', 'id')
                ->all())
            ->getOptionLabelUsing(static fn (mixed $value): ?string => $model::query()
                ->whereKey($value)
                ->value('name'))
            ->options(static fn (): array => $model::query()
                ->orderBy('name')
                ->limit(50)
                ->pluck('name', 'id')
                ->all())
            ->live();
    }

    private static function currencySelect(Collection $currencies): Select
    {
        return Select::make('currency_id')
            ->label(__('app.currency'))
            ->options($currencies
                ->mapWithKeys(static fn (Currency $currency): array => [
                    $currency->id => "{$currency->name} ({$currency->code})",
                ])
                ->all())
            ->default($currencies->firstWhere('is_default', true)?->id ?? $currencies->first()?->id)
            ->required()
            ->searchable()
            ->live();
    }

    private static function remainingAmountPreview(callable $codeFor): TextEntry
    {
        return TextEntry::make('remaining_amount')
            ->label(__('app.remaining_amount'))
            ->state(static fn (Get $get): string => Money::subtract(
                $get('total_payment'),
                $get('amount_paid'),
            ))
            ->money(static fn (Get $get): string => $codeFor($get('currency_id')))
            ->weight('bold');
    }
}
