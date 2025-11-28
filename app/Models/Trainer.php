<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\Trainer\TrainerCheckers;
use App\Models\Traits\Trainer\TrainerHelpers;
use App\Models\Traits\Trainer\TrainerRelations;
use App\Models\Traits\Trainer\TrainerScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory;
    use TrainerCheckers, TrainerHelpers, TrainerRelations, TrainerScopes;

    protected $fillable = [
        'name',
        'avatar',
        'bio',
        'email',
        'phone',
        'address',
        'country_id',
        'specializations',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'address' => 'array',
            'specializations' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
