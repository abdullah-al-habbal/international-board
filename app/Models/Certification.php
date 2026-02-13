<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\Certification\CertificationActions;
use App\Models\Traits\Certification\CertificationCheckers;
use App\Models\Traits\Certification\CertificationHelpers;
use App\Models\Traits\Certification\CertificationRelations;
use App\Models\Traits\Certification\CertificationScopes;
use App\Policies\CertificationPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UsePolicy(CertificationPolicy::class)]
class Certification extends Model
{
    use CertificationActions, CertificationCheckers, CertificationHelpers;
    use CertificationRelations, CertificationScopes;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * NOTE: certificate_type REMOVED - use document_type_id instead
     */
    protected $fillable = [
        'certified_center_id',
        'trainee_id',
        'nationality',
        'accredited_serial_number',
        'document_code',
        'accreditation_number',
        'document_type_id',
        'accreditation_date',
        'trainer_id',
        'country_id',
        'paper_received',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * NOTE: certificate_type cast REMOVED
     */
    protected function casts(): array
    {
        return [
            'accreditation_date' => 'date',
            'paper_received' => 'boolean',
        ];
    }
}
