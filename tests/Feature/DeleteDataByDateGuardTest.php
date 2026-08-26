<?php

declare(strict_types=1);

use App\Models\Trainee;
use Illuminate\Database\QueryException;

/**
 * delete:data-by-date removes rows from every table carrying a
 * created_at/updated_at column and, with --disable-fk, does so with foreign key
 * checks off. Production holds live data and is not backed up, so the command
 * must refuse to run there unless --force is passed explicitly.
 *
 * Only the guard is exercised here: the command body is MySQL-only (SHOW TABLES,
 * information_schema.columns) and the suite runs on SQLite, so a run that gets
 * past the guard cannot complete under test.
 */
it('refuses to run in production without --force', function () {
    app()->detectEnvironment(fn () => 'production');

    $this->artisan('delete:data-by-date', ['date' => now()->format('Y-m-d')])
        ->expectsOutputToContain('Refusing to run in production without --force.')
        ->assertFailed();
});

it('deletes nothing while the guard is refusing', function () {
    app()->detectEnvironment(fn () => 'production');

    Trainee::factory()->count(3)->create();
    $before = Trainee::count();

    $this->artisan('delete:data-by-date', ['date' => now()->format('Y-m-d')])
        ->assertFailed();

    expect(Trainee::count())->toBe($before);
});

it('warns why it refused rather than failing silently', function () {
    app()->detectEnvironment(fn () => 'production');

    $this->artisan('delete:data-by-date')
        ->expectsOutputToContain('deletes rows from every table')
        ->expectsOutputToContain('no backups')
        ->assertFailed();
});

it('gets past the guard outside production', function () {
    expect(app()->environment())->not->toBe('production');

    // Reaching the MySQL-only body means the guard let it through; on SQLite it
    // then fails on `SHOW TABLES`, which is the expected boundary under test.
    expect(fn () => $this->artisan('delete:data-by-date', [
        'date' => now()->format('Y-m-d'),
        '--dry-run' => true,
    ])->run())->toThrow(QueryException::class);
});

it('gets past the guard in production when --force is passed', function () {
    app()->detectEnvironment(fn () => 'production');

    expect(fn () => $this->artisan('delete:data-by-date', [
        'date' => now()->format('Y-m-d'),
        '--dry-run' => true,
        '--force' => true,
    ])->run())->toThrow(QueryException::class);
});
