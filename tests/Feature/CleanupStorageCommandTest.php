<?php

declare(strict_types=1);

use App\Models\CertifiedCenter;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * Writes a file to the faked public disk and back-dates it so the command's
 * grace period does not protect it.
 */
function putAgedUpload(string $path, int $hoursOld = 48): void
{
    $disk = Storage::disk('public');
    $disk->put($path, 'x');
    touch($disk->path($path), time() - ($hoursOld * 3600));
}

it('deletes an orphaned trainer avatar', function () {
    Storage::fake('public');
    putAgedUpload('trainers/avatars/orphan.png');

    $this->artisan('cleanup:storage')->assertSuccessful();

    expect(Storage::disk('public')->exists('trainers/avatars/orphan.png'))->toBeFalse();
});

it('keeps a trainer avatar that is still referenced', function () {
    Storage::fake('public');
    putAgedUpload('trainers/avatars/kept.png');
    Trainer::factory()->create(['avatar' => 'trainers/avatars/kept.png']);

    $this->artisan('cleanup:storage')->assertSuccessful();

    expect(Storage::disk('public')->exists('trainers/avatars/kept.png'))->toBeTrue();
});

it('keeps a referenced center logo and deletes an unreferenced one', function () {
    Storage::fake('public');
    putAgedUpload('centers/logos/kept.png');
    putAgedUpload('centers/logos/orphan.png');
    CertifiedCenter::factory()->create(['logo' => 'centers/logos/kept.png']);

    $this->artisan('cleanup:storage')->assertSuccessful();

    expect(Storage::disk('public')->exists('centers/logos/kept.png'))->toBeTrue()
        ->and(Storage::disk('public')->exists('centers/logos/orphan.png'))->toBeFalse();
});

it('keeps a referenced user avatar and deletes an unreferenced one', function () {
    Storage::fake('public');
    putAgedUpload('users/avatars/kept.png');
    putAgedUpload('users/avatars/orphan.png');
    User::factory()->create(['avatar' => 'users/avatars/kept.png']);

    $this->artisan('cleanup:storage')->assertSuccessful();

    expect(Storage::disk('public')->exists('users/avatars/kept.png'))->toBeTrue()
        ->and(Storage::disk('public')->exists('users/avatars/orphan.png'))->toBeFalse();
});

it('spares an orphan that is still inside the grace period', function () {
    Storage::fake('public');
    putAgedUpload('trainers/avatars/just-uploaded.png', hoursOld: 1);

    $this->artisan('cleanup:storage')->assertSuccessful();

    expect(Storage::disk('public')->exists('trainers/avatars/just-uploaded.png'))->toBeTrue();
});

it('does not touch files outside the managed upload directories', function () {
    Storage::fake('public');
    putAgedUpload('exports/report.csv');
    putAgedUpload('unrelated.png');

    $this->artisan('cleanup:storage')->assertSuccessful();

    expect(Storage::disk('public')->exists('exports/report.csv'))->toBeTrue()
        ->and(Storage::disk('public')->exists('unrelated.png'))->toBeTrue();
});

it('frees the previous file once an avatar is replaced', function () {
    Storage::fake('public');
    putAgedUpload('trainers/avatars/old.png');
    putAgedUpload('trainers/avatars/new.png');

    $trainer = Trainer::factory()->create(['avatar' => 'trainers/avatars/old.png']);
    $trainer->update(['avatar' => 'trainers/avatars/new.png']);

    $this->artisan('cleanup:storage')->assertSuccessful();

    expect(Storage::disk('public')->exists('trainers/avatars/old.png'))->toBeFalse()
        ->and(Storage::disk('public')->exists('trainers/avatars/new.png'))->toBeTrue()
        ->and($trainer->fresh()->avatar_url)->toContain('new.png');
});

it('runs cleanly when there is nothing to sweep', function () {
    Storage::fake('public');

    $this->artisan('cleanup:storage')->assertSuccessful();
});
