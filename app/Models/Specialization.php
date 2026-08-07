<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Translatable(['name'])]
#[Fillable([
    'name',
])]
class Specialization extends Model
{
    use HasFactory;
    use HasTranslations;

    public function trainers(): BelongsToMany
    {
        return $this->belongsToMany(Trainer::class, 'specialization_trainer')
            ->withTimestamps();
    }
}
