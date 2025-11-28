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
        'email',
        'phone',
        'country_id',
        'date_of_birth',
        'nationality',
        'gender',
        'occupation',
        'organization',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'medical_info',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }
}
