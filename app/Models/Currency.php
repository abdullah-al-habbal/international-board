<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\CurrencyObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Table('currencies')]
#[ObservedBy([CurrencyObserver::class])]
#[Translatable(['name', 'symbol'])]
#[Fillable([
    'name',
    'code',
    'symbol',
    'is_default',
])]
class Currency extends Model
{
    use HasFactory;
    use HasTranslations;

    /**
     * ISO code shown for financial records that carry no currency.
     */
    public static function fallbackCode(): string
    {
        return (string) config('currencies.fallback_code', 'USD');
    }

    public function financialRequests(): HasMany
    {
        return $this->hasMany(FinancialRequest::class);
    }

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'symbol' => 'array',
            'is_default' => 'boolean',
        ];
    }
}
