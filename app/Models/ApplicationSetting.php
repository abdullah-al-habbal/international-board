<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SettingType;
use App\Models\Traits\ApplicationSetting\ApplicationSettingHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationSetting extends Model
{
    use HasFactory;
    use ApplicationSettingHelpers;

    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'array',
            'type' => SettingType::class,
        ];
    }
}
