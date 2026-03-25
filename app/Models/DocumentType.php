<?php

// app/Models/DocumentType.php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class DocumentType extends Model
{
    use HasFactory;
    use HasTranslations;

    public $translatable = ['name'];

    protected $fillable = [
        'key',
        'name',
    ];

    public function approvedCenters(): HasMany
    {
        return $this->hasMany(CertifiedCenterDocumentType::class);
    }

    #[Scope]
    protected function byKey(Builder $query, string $key): void
    {
        $query->where('key', $key);
    }

    #[Scope]
    protected function orderByKey(Builder $query, string $direction = 'asc'): void
    {
        $query->orderBy('key', $direction);
    }

    #[Scope]
    protected function withCertifications(Builder $query): void
    {
        $query->has('certifications');
    }
}
