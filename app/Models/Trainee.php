<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\Trainee\{
    TraineeCheckers,
    TraineeHelpers,
    TraineeRelations,
    TraineeScopes
};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainee extends Model
{
    use HasFactory;
    use TraineeCheckers, TraineeHelpers, TraineeRelations, TraineeScopes;

    protected $fillable = [
        'name',
    ];

    protected function casts(): array
    {
        return [];
    }
}
