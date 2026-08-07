<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * A suggested — never applied — merge between two rows of the same entity type.
 *
 * @property string $entity_type
 * @property int $primary_id
 * @property int $duplicate_id
 * @property float $score
 * @property string $strategy
 * @property string $status
 */
#[Table('entity_merge_candidates')]
#[Fillable([
    'entity_type',
    'primary_id',
    'duplicate_id',
    'primary_name',
    'duplicate_name',
    'score',
    'strategy',
    'status',
    'reviewed_by',
    'reviewed_at',
])]
final class EntityMergeCandidate extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_MERGED = 'merged';

    public const STATUS_REJECTED = 'rejected';

    protected function casts(): array
    {
        return [
            'score' => 'float',
            'reviewed_at' => 'datetime',
        ];
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
