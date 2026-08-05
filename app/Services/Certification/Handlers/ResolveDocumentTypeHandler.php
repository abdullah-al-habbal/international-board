<?php

declare(strict_types=1);

namespace App\Services\Certification\Handlers;

use App\Services\Certification\Exceptions\MissingValueException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ResolveDocumentTypeHandler
{
    use HasStringNormalization;

    private array $cache = [];

    public function warmUp(): void
    {
        DB::table('board_document_types')->orderBy('id')->chunk(5000, function ($docs): void {
            foreach ($docs as $doc) {
                $names = json_decode($doc->name, true);

                if (is_array($names)) {
                    foreach ($names as $name) {
                        if (! empty($name)) {
                            $this->cache[$this->normalizeString((string) $name)] = (int) $doc->id;
                        }
                    }
                }
            }
        });
    }

    public function handle(string $name): int
    {
        $now = Carbon::now();

        if (empty(trim($name))) {
            throw new MissingValueException('document_type');
        }

        $normalized = $this->normalizeString($name);

        if (isset($this->cache[$normalized])) {
            return $this->cache[$normalized];
        }

        $id = DB::table('board_document_types')->insertGetId([
            'key' => 'imported_'.Str::slug($name).'_'.Str::random(4),
            'name' => json_encode(['en' => $name, 'ar' => $name], JSON_UNESCAPED_UNICODE),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->cache[$normalized] = $id;

        return $id;
    }
}
