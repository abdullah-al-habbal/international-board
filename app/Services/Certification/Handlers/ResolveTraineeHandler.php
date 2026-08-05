<?php

declare(strict_types=1);

namespace App\Services\Certification\Handlers;

use App\Services\Certification\Exceptions\MissingValueException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class ResolveTraineeHandler
{
    use HasStringNormalization;

    private array $cache = [];

    public function warmUp(): void
    {
        DB::table('trainees')->orderBy('id')->chunk(5000, function ($trainees): void {
            foreach ($trainees as $trainee) {
                if (! empty($trainee->name)) {
                    $this->cache[$this->normalizeString((string) $trainee->name)] = (int) $trainee->id;
                }
            }
        });
    }

    public function handle(string $name, ?int $countryId): int
    {
        $now = Carbon::now();

        if (empty(trim($name))) {
            throw new MissingValueException('trainee_name');
        }

        $normalized = $this->normalizeString($name);

        if (isset($this->cache[$normalized])) {
            return $this->cache[$normalized];
        }

        $id = DB::table('trainees')->insertGetId([
            'name' => $name,
            'country_id' => $countryId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->cache[$normalized] = $id;

        return $id;
    }
}
