<?php

declare(strict_types=1);

use Illuminate\Routing\Route;
use Tests\Support\Spider;
use Tests\TestCase;

/**
 * Authenticated multi-guard spider + public multilingual Blade crawler.
 *
 * For each panel we authenticate the matching Eloquent model on its own guard,
 * then issue GET requests across every crawlable route. The hard assertion is
 * "no route returns HTTP >= 500"; 4xx responses are surfaced as warnings only
 * (tenant-scoped record routes legitimately 403/404 on foreign ids).
 */

/**
 * @param  array<int, Route>  $routes
 * @return array{crawled: int, skipped: int, failures: array<int, string>, warnings: array<int, string>}
 */
function crawlRoutes(TestCase $test, Spider $spider, array $routes, ?string $locale = null): array
{
    $crawled = 0;
    $skipped = 0;
    $failures = [];
    $warnings = [];

    foreach ($routes as $route) {
        $path = $spider->pathFor($route, $locale);
        if ($path === null) {
            $skipped++;

            continue;
        }

        $status = $test->get($path)->getStatusCode();
        $crawled++;

        $label = ($route->getName() ?? $path).' -> '.$path.' ['.$status.']';
        if ($status >= 500) {
            $failures[] = $label;
        } elseif ($status >= 400) {
            $warnings[] = $label;
        }
    }

    return compact('crawled', 'skipped', 'failures', 'warnings');
}

it('crawls the admin panel without server errors', function () {
    $spider = new Spider;
    $spider->seed();

    $this->actingAs($spider->admin, 'web');

    $result = crawlRoutes($this, $spider, $spider->routes('filament.admin.'));

    expect($result['crawled'])->toBeGreaterThan(0);
    expect($result['failures'])->toBe([], "Admin 5xx routes:\n".implode("\n", $result['failures']));
});

it('crawls the center panel without server errors', function () {
    $spider = new Spider;
    $spider->seed();

    $this->actingAs($spider->center, 'certified_center');

    $result = crawlRoutes($this, $spider, $spider->routes('filament.center.'));

    expect($result['crawled'])->toBeGreaterThan(0);
    expect($result['failures'])->toBe([], "Center 5xx routes:\n".implode("\n", $result['failures']));
});

it('crawls the trainer panel without server errors', function () {
    $spider = new Spider;
    $spider->seed();

    $this->actingAs($spider->trainer, 'trainer');

    $result = crawlRoutes($this, $spider, $spider->routes('filament.trainer.'));

    expect($result['crawled'])->toBeGreaterThan(0);
    expect($result['failures'])->toBe([], "Trainer 5xx routes:\n".implode("\n", $result['failures']));
});

it('crawls the public site in every locale without server errors', function (string $locale) {
    $this->withoutVite();

    $spider = new Spider;
    $spider->seed();

    // Exercise LocaleMiddleware + spatie/laravel-translatable.
    $this->get('/web/lang/'.$locale);

    // ViewServiceProvider skips its View::share() calls under runningInConsole(),
    // so replicate the public view globals before rendering Blade.
    $spider->sharePublicViewGlobals($locale);

    $result = crawlRoutes($this, $spider, $spider->routes('web.'), $locale);

    expect($result['crawled'])->toBeGreaterThan(0);
    expect($result['failures'])->toBe([], "Public 5xx routes ({$locale}):\n".implode("\n", $result['failures']));
})->with(['en', 'ar']);
