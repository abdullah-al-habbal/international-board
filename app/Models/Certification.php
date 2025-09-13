<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CertificateType;
use App\Enums\DocumentType;
use App\Models\Traits\Certification\{
    CertificationActions,
    CertificationCheckers,
    CertificationHelpers,
    CertificationRelations,
    CertificationScopes
};
use App\Policies\CertificationPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Enum;

#[UsePolicy(CertificationPolicy::class)]
class Certification extends Model
{
    use HasFactory;
    use CertificationActions, CertificationCheckers, CertificationHelpers;
    use CertificationRelations, CertificationScopes;

    protected $fillable = [
        'certified_center_id',
        'certificate_type',
        'trainee_name',
        'accredited_serial_number',
        'document_code',
        'accreditation_number',
        'document_type_id',
        'accreditation_date',
        'trainer_name',
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
