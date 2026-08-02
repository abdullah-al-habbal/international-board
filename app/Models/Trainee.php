<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trainee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'country_id',
        'date_of_birth',
        'gender',
        'notes',
        'show_in_public_website',
    ];

    protected function casts(): array
    {
        return [
            'show_in_public_website' => 'boolean',
        ];
    }

    public function getDateOfBirthAttribute($value): ?string
    {
        if (blank($value)) {
            return null;
        }
        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }

    #[Scope]
    protected function publiclyVisible(Builder $query): void
    {
        $query->where('show_in_public_website', true);
    }
}
