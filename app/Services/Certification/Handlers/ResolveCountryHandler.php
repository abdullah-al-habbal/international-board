<?php

declare(strict_types=1);

namespace App\Services\Certification\Handlers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class ResolveCountryHandler
{
    use HasStringNormalization;

    private array $cache = [];

    public function warmUp(): void
    {
        DB::table('countries')->orderBy('id')->chunk(5000, function ($countries): void {
            foreach ($countries as $country) {
                $names = json_decode($country->name, true);

                if (is_array($names)) {
                    foreach ($names as $name) {
                        if (! empty($name)) {
                            $this->cache[$this->normalizeString((string) $name)] = (int) $country->id;
                        }
                    }
                }
            }
        });
    }

    public function handle(string $name): ?int
    {
        if (empty(trim($name))) {
            return null;
        }

        $normalized = $this->normalizeString($name);

        if (isset($this->cache[$normalized])) {
            return $this->cache[$normalized];
        }

        $now = Carbon::now();

        $id = DB::table('countries')->insertGetId([
            'name' => json_encode(['en' => $name, 'ar' => $name], JSON_UNESCAPED_UNICODE),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->cache[$normalized] = $id;

        return $id;
    }
}
