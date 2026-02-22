<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

final class QueryServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // DB::listen(function ($query) {
        //     if (str_contains($query->sql, '*')) {
        //         throw new \LogicException("SELECT * is forbidden: {$query->sql}");
        //     }
        // });

        if (method_exists(DB::class, 'whenQueryingForLongerThan')) {
            DB::whenQueryingForLongerThan(100, function ($connection, $event) {
                logger()->warning("Slow query: {$event->sql}");
            });
        }

        QueryBuilder::macro('whereLike', function ($columns, $search) {
            $this->where(function ($query) use ($columns, $search) {
                foreach ((array) $columns as $column) {
                    $query->orWhere($column, 'LIKE', "%{$search}%");
                }
            });

            return $this;
        });
    }
}
