<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CertificateType;
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

    protected $fillable = [
        'certified_center_id',
        'certificate_type',
        'trainee_id',
        'trainee_name',
        'trainer_name',
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

    protected function casts(): array
    {
        return [
            'accreditation_date' => 'date',
            'certificate_type' => CertificateType::class,
            'paper_received' => 'boolean',
        ];
    }
}
