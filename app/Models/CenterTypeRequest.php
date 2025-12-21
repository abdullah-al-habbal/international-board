<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CenterTypeRequestStatus;
use App\Enums\CenterTypeRequestType;
use App\Policies\CenterTypeRequestPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(CenterTypeRequestPolicy::class)]
class CenterTypeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'certified_center_id',
        'document_type_id',
        'requested_name',
        'requested_description',
        'type',
        'status',
        'rejection_message',
    ];

    protected function casts(): array
    {
        return [
            'type' => CenterTypeRequestType::class,
            'status' => CenterTypeRequestStatus::class,
        ];
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(CertifiedCenter::class, 'certified_center_id');
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }
}
