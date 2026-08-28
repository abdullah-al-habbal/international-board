<?php

declare(strict_types=1);

namespace App\Models;

use App\Policies\CountryPolicy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Translatable(['name'])]
#[UsePolicy(CountryPolicy::class)]
#[Fillable([
    'name',
    'code',
    'code_2',
])]
class Country extends Model
{
    use HasFactory;
    use HasTranslations;
}
