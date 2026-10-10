<?php

declare(strict_types=1);

namespace App\Models;

use App\Policies\TrainerRolePolicy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Translatable(['name'])]
#[UsePolicy(TrainerRolePolicy::class)]
#[Fillable([
    'name',
])]
class TrainerRole extends Model
{
    use HasFactory;
    use HasTranslations;

    protected function casts(): array
    {
        return [
            'name' => 'array',
        ];
    }

    public function trainers(): HasMany
    {
        return $this->hasMany(Trainer::class);
    }
}
