<?php

declare(strict_types=1);

namespace App\Services\Certification\Handlers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ResolveTrainerHandler
{
    use HasStringNormalization;

    private array $cache = [];

    public function warmUp(): void
    {
        DB::table('trainers')->orderBy('id')->chunk(5000, function ($trainers): void {
            foreach ($trainers as $trainer) {
                if (! empty($trainer->name)) {
                    $this->cache[$this->normalizeString((string) $trainer->name)] = (int) $trainer->id;
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
        $candidate = 'IBVTQ'.$now->format('Ymd').'-'.Str::uuid()->toString();

        $id = DB::table('trainers')->insertGetId([
            'name' => $name,
            'accreditation_number' => $candidate,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->cache[$normalized] = $id;

        return $id;
    }
}
