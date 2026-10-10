<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\Attributes\Translatable;
use Spatie\Translatable\HasTranslations;

#[Translatable(['title', 'description'])]
#[Fillable([
    'slug',
    'title',
    'description',
])]
class Membership extends Model
{
    use HasFactory, HasTranslations;
}
