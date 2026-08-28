<?php

declare(strict_types=1);

use App\Support\LocaleConfig;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Brings the shipped `currencies` schema in line with the model, additively.
 *
 * `2026_08_26_163143` created `currencies.name` / `currencies.symbol` as
 * VARCHAR and `2026_08_26_163316` added `financial_requests.currency_id` with
 * `nullOnDelete`. Both are already on production, so neither can be edited in
 * place — an edited migration is simply skipped by `migrate --force` and the
 * change would never reach the live schema. This migration therefore carries
 * the three corrections forward:
 *
 *  1. `name` / `symbol` become JSON, which is what `Currency` casts them to and
 *     what `spatie/laravel-translatable` needs to store per-locale values.
 *  2. `currency_id` is re-constrained with `restrict` instead of `null` on
 *     delete: a financial request is historical data, so deleting the currency
 *     it was denominated in must be refused rather than quietly blanking the
 *     denomination of past records.
 *  3. The configured reference currencies are inserted if absent. Deployment
 *     runs `migrate --force` only and never a seeder, so without this the
 *     `currencies` table stays empty on production and the required currency
 *     selector has nothing to offer.
 *
 * Existing `financial_requests` rows are deliberately left untouched: a NULL
 * `currency_id` keeps rendering through `currencies.fallback_code`, whereas
 * back-stamping every historical row with USD would assert a denomination
 * nobody recorded.
 */
return new class extends Migration
{
    public function up(): void
    {
        // A JSON payload does not fit VARCHAR(10); widen before rewriting.
        Schema::table('currencies', function (Blueprint $table): void {
            $table->string('name', 1000)->change();
            $table->string('symbol', 1000)->change();
        });

        $this->convertLegacyTranslationsToJson();

        Schema::table('currencies', function (Blueprint $table): void {
            $table->json('name')->change();
            $table->json('symbol')->change();
        });

        Schema::table('financial_requests', function (Blueprint $table): void {
            $table->dropForeign(['currency_id']);
            $table->foreign('currency_id')
                ->references('id')
                ->on('currencies')
                ->restrictOnDelete();
        });

        $this->insertMissingReferenceCurrencies();
    }

    public function down(): void
    {
        Schema::table('financial_requests', function (Blueprint $table): void {
            $table->dropForeign(['currency_id']);
            $table->foreign('currency_id')
                ->references('id')
                ->on('currencies')
                ->nullOnDelete();
        });

        // Mirror of up(), in reverse and in three steps, because neither order
        // works in one: while the columns are still JSON, MySQL's json_valid
        // check rejects a plain string, and once they are narrowed the JSON no
        // longer fits ("Data too long for column 'symbol'"). So drop the JSON
        // type first, then unwrap, then narrow.
        //
        // Reference currencies inserted by up() are kept: they may already be
        // referenced by financial requests.
        Schema::table('currencies', function (Blueprint $table): void {
            $table->string('name', 1000)->change();
            $table->string('symbol', 1000)->change();
        });

        $this->unwrapTranslationsToPlainStrings();

        Schema::table('currencies', function (Blueprint $table): void {
            $table->string('name')->change();
            $table->string('symbol', 10)->change();
        });
    }

    /**
     * Collapse each translated value back to a single locale, within the limits
     * the pre-JSON columns imposed.
     */
    private function unwrapTranslationsToPlainStrings(): void
    {
        $locale = LocaleConfig::defaultLocale();
        $limits = ['name' => 255, 'symbol' => 10];

        foreach (DB::table('currencies')->get(['id', 'name', 'symbol']) as $currency) {
            $update = [];

            foreach ($limits as $column => $limit) {
                $value = $currency->{$column};

                if ($value === null || ! $this->isTranslationJson($value)) {
                    continue;
                }

                $translations = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

                $update[$column] = mb_substr(
                    (string) ($translations[$locale] ?? reset($translations) ?: ''),
                    0,
                    $limit,
                );
            }

            if ($update !== []) {
                DB::table('currencies')->where('id', $currency->id)->update($update);
            }
        }
    }

    /**
     * Wrap pre-translation plain strings into a per-locale JSON object, using
     * the configured translations when the code is known.
     */
    private function convertLegacyTranslationsToJson(): void
    {
        $configured = collect(config('currencies.data', []))
            ->keyBy(static fn (array $currency): string => (string) $currency['code']);

        $locales = LocaleConfig::availableLocales();

        foreach (DB::table('currencies')->get(['id', 'code', 'name', 'symbol']) as $currency) {
            $update = [];

            foreach (['name', 'symbol'] as $column) {
                $value = $currency->{$column};

                if ($value === null || $this->isTranslationJson($value)) {
                    continue;
                }

                $translations = $configured[$currency->code][$column] ?? null;

                $update[$column] = json_encode(
                    is_array($translations)
                        ? $translations
                        : array_fill_keys($locales, $value),
                    JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE,
                );
            }

            if ($update !== []) {
                DB::table('currencies')->where('id', $currency->id)->update($update);
            }
        }
    }

    private function isTranslationJson(string $value): bool
    {
        if (! json_validate($value)) {
            return false;
        }

        return is_array(json_decode($value, true, 512, JSON_THROW_ON_ERROR));
    }

    private function insertMissingReferenceCurrencies(): void
    {
        $existing = DB::table('currencies')->pluck('code')->all();
        $now = now();

        $missing = collect(config('currencies.data', []))
            ->reject(static fn (array $currency): bool => in_array($currency['code'], $existing, true))
            ->map(static fn (array $currency): array => [
                'code' => $currency['code'],
                'name' => json_encode($currency['name'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                'symbol' => json_encode($currency['symbol'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                // Never flip an operator's existing default.
                'is_default' => $existing === [] ? $currency['is_default'] : false,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->values()
            ->all();

        if ($missing !== []) {
            DB::table('currencies')->insert($missing);
        }
    }
};
