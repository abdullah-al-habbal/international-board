<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SettingType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static Builder|static where($column, $operator = null, $value = null, $boolean = 'and')
 */
class ApplicationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'type' => SettingType::class,
        ];
    }

    public function getTypedValue(): mixed
    {
        return match ($this->type) {
            SettingType::Json => json_decode($this->value, true) ?? $this->value,
            SettingType::Boolean => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            SettingType::Number => is_numeric($this->value) ? (float) $this->value : $this->value,
            SettingType::Email => $this->value,
            SettingType::Url => $this->value,
            default => $this->value,
        };
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return $setting->getTypedValue();
    }

    protected function value(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->getTypedValue(),
        );
    }

    public static function set(string $key, mixed $value, SettingType $type = SettingType::Text): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }
}
