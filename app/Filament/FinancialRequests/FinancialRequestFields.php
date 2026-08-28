<?php

declare(strict_types=1);

namespace App\Filament\FinancialRequests;

use App\Filament\Components\MoneyColumn;
use App\Filament\Components\MoneyEntry;
use App\Filament\Components\MoneyInput;
use App\Models\AgentPerson;
use App\Models\Currency;
use App\Support\Money;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * The financial pieces the four Financial Request surfaces genuinely share.
 *
 * Money formatting, the currency selector and the remaining-amount calculation
 * are identical wherever a Financial Request is shown; the rest — which
 * `requestable` a panel may pick, whether the record is editable at all — stays
 * in the panel's own schema. Only the shared part lives here, so no panel
 * inherits another panel's permissions.
 */
final class FinancialRequestFields
{
    /**
     * Currency selector, both money inputs and the live remaining amount.
     *
     * The currency rows are fetched once per schema build and closed over, so
     * the selector's options, the two input suffixes and the remaining-amount
     * formatter all share a single query instead of one lookup per field
     * render. This replaces the previous `Currency::pluck()` plus
     * `Currency::where('is_default')` plus `Currency::first()` trio.
     *
     * @return array<int, Component>
     */
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

    /**
     * Read-only money columns for a Financial Request table.
     *
     * @return array<int, Column>
     */
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
                // `remaining_amount` is an appended accessor, not a column, so
                // the default `ORDER BY remaining_amount` would be invalid SQL.
                ->sortable(query: static fn (Builder $query, string $direction): Builder => $query
                    ->orderByRaw('(total_payment - amount_paid) '.($direction === 'desc' ? 'desc' : 'asc'))),
        ];
    }

    /**
     * Read-only money entries for a Financial Request infolist.
     *
     * @return array<int, Component>
     */
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

    /**
     * Agent person selector.
     *
     * Resolved through the `agentPerson` relationship instead of
     * `AgentPerson::pluck('name', 'id')`: Filament then loads the options
     * lazily and searches in the database, so opening a form no longer pulls
     * the whole table into memory.
     */
    public static function agentPersonSelect(): Select
    {
        return Select::make('agent_person_id')
            ->label(__('app.agent_person'))
            ->relationship('agentPerson', 'name')
            ->searchable()
            ->preload()
            ->required();
    }

    /**
     * Searchable selector over a morph target that is not an Eloquent relation.
     *
     * `requestable_id` is one half of a `morphs()` pair, so `relationship()`
     * cannot be used. Options are resolved on demand and capped, which keeps a
     * table of thousands of trainers or centres off the initial render.
     *
     * @param  class-string<Model>  $model
     */
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

    /**
     * @param  Collection<int, Currency>  $currencies
     */
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

    /**
     * Live remaining amount.
     *
     * Display only: it re-runs the same {@see Money::subtract()} the model's
     * `remaining_amount` accessor uses, so the browser never becomes a second
     * implementation of the calculation, and it is an entry rather than a field
     * so nothing is dehydrated or written back.
     *
     * @param  callable(mixed): string  $codeFor
     */
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
