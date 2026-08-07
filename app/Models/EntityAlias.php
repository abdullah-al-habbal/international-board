<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * A known spelling that maps to an entity.
 *
 * This table is the answer to "how do we ever resolve 'allebanon' to Lebanon?".
 * We do not guess it. A human resolves it once; the merge writes the alias; every
 * import after that resolves it exactly, in one indexed lookup, forever.
 *
 * @property string $aliasable_type
 * @property int $aliasable_id
 * @property string $alias_key
 * @property string|null $alias_label
 * @property string $source
 */
#[Table('entity_aliases')]
#[Fillable([
    'aliasable_type',
    'aliasable_id',
    'alias_key',
    'alias_label',
    'source',
    'created_by',
])]
final class EntityAlias extends Model
{
    public function aliasable(): MorphTo
    {
        return $this->morphTo();
    }
}
