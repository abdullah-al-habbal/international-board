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
        'document_type',
        'accreditation_date',
        'trainer_name',
        'trainer_id',
        'nationality',
        'country_id',
        'paper_received',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'accreditation_date' => 'date',
            'certificate_type' => CertificateType::class,
            'document_type' => DocumentType::class,
            'paper_received' => 'string',
        ];
    }


    public static function getValidationRules(): array
    {
        return [
            'certified_center_id' => 'nullable|exists:certified_centers,id',
            'certificate_type' => ['required', new Enum(CertificateType::class)],
            'trainee_name' => 'required|string|max:255',
            'accredited_serial_number' => 'required|string|max:255|unique:certifications,accredited_serial_number',
            'document_code' => 'nullable|string|max:255',
            'accreditation_number' => 'nullable|string|max:255',
            'document_type' => ['required', new Enum(DocumentType::class)],
            'accreditation_date' => 'required|date|after:1900-01-01|before_or_equal:today',
            'trainer_name' => 'nullable|string|max:255',
            'trainer_id' => 'nullable|exists:trainers,id',
            'nationality' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'paper_received' => 'nullable|in:YES,NO,PENDING',
            'notes' => 'nullable|string',
        ];
    }
}
