<?php

declare(strict_types=1);

namespace App\Services\Certification\Handlers;

use App\Models\DocumentType;
use App\Services\Certification\Exceptions\MissingValueException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ResolveDocumentTypeHandler extends ResolvesEntities
{
    /** @var array<int, string>|null */
    private ?array $pool = null;

    protected function table(): string
    {
        return 'board_document_types';
    }

    protected function entityType(): string
    {
        return DocumentType::class;
    }

    protected function isClosedSet(): bool
    {
        return true;
    }

    /**
     * @return list<string>
     */
    protected function noiseTokens(): array
    {
        return ['the', 'a', 'an', 'of', 'for', 'type', 'document', 'cert', 'unknown', 'na'];
    }

    protected function newEntityAttributes(string $rawName, string $normalized, string $key, array $context): array
    {
        return [
            'key' => 'imported_'.Str::slug($rawName).'_'.Str::random(4),
            'name' => json_encode(['en' => $rawName, 'ar' => $rawName], JSON_UNESCAPED_UNICODE),
            'name_normalized' => $normalized,
            'name_key' => $key,
            'review_status' => 'provisional',
        ];
    }

    protected function afterCreate(array $pending, array $created): void
    {
        foreach ($pending as $key => $item) {
            $this->reportUnresolved($item['raw'], $created[$key] ?? null);
        }
    }

    /**
     * @return array<int, string>
     */
    protected function suggestionPool(): array
    {
        if ($this->pool !== null) {
            return $this->pool;
        }

        $this->pool = [];

        foreach (DB::table('board_document_types')->select(['id', 'name'])->get() as $row) {
            $decoded = json_decode((string) $row->name, true);
            $label = is_array($decoded) ? (string) ($decoded['en'] ?? reset($decoded)) : (string) $row->name;

            if ($label !== '') {
                $this->pool[(int) $row->id] = $label;
            }
        }

        return $this->pool;
    }

    /**
     * @throws MissingValueException
     */
    public function handle(string $name): int
    {
        if (trim($name) === '') {
            throw new MissingValueException('document_type');
        }

        $id = $this->resolve($name);

        if ($id === null) {
            throw new MissingValueException('document_type');
        }

        return $id;
    }
}
