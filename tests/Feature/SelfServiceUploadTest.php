<?php

declare(strict_types=1);

use App\Filament\Center\Pages\CenterProfilePage;
use App\Filament\Trainer\Pages\TrainerProfilePage;
use App\Models\CertifiedCenter;
use App\Models\Trainer;
use Filament\Facades\Filament;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function actingAsTrainerPanel(): Trainer
{
    $trainer = Trainer::factory()->create();

    test()->actingAs($trainer, 'trainer');
    Filament::setCurrentPanel('trainer');

    return $trainer;
}

function actingAsCenterPanel(): CertifiedCenter
{
    $center = CertifiedCenter::factory()->create();

    test()->actingAs($center, 'certified_center');
    Filament::setCurrentPanel('center');

    return $center;
}

/*
|--------------------------------------------------------------------------
| Trainer — self-service avatar
|--------------------------------------------------------------------------
*/

it('lets a trainer upload their own avatar into trainers/avatars', function () {
    Storage::fake('public');
    $trainer = actingAsTrainerPanel();

    Livewire::test(TrainerProfilePage::class)
        ->fillForm(['avatar' => UploadedFile::fake()->image('me.png', 300, 300)])
        ->call('save')
        ->assertHasNoFormErrors();

    $stored = $trainer->fresh()->avatar;

    expect($stored)->not->toBeNull()
        ->and($stored)->toStartWith('trainers/avatars/')
        ->and(Storage::disk('public')->exists($stored))->toBeTrue();
});

it('rejects a trainer avatar over the 2MB limit', function () {
    Storage::fake('public');
    $trainer = actingAsTrainerPanel();

    Livewire::test(TrainerProfilePage::class)
        ->fillForm(['avatar' => UploadedFile::fake()->image('huge.png')->size(4096)])
        ->call('save')
        ->assertHasFormErrors(['avatar']);

    expect($trainer->fresh()->avatar)->toBeEmpty();
});

it('rejects an svg trainer avatar', function () {
    Storage::fake('public');
    $trainer = actingAsTrainerPanel();

    $svg = UploadedFile::fake()->createWithContent(
        'x.svg',
        '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'
    );

    Livewire::test(TrainerProfilePage::class)
        ->fillForm(['avatar' => $svg])
        ->call('save')
        ->assertHasFormErrors(['avatar']);

    expect($trainer->fresh()->avatar)->toBeEmpty();
});

it('rejects a non-image trainer avatar', function () {
    Storage::fake('public');
    $trainer = actingAsTrainerPanel();

    Livewire::test(TrainerProfilePage::class)
        ->fillForm(['avatar' => UploadedFile::fake()->create('payload.pdf', 16, 'application/pdf')])
        ->call('save')
        ->assertHasFormErrors(['avatar']);

    expect($trainer->fresh()->avatar)->toBeEmpty();
});

/*
|--------------------------------------------------------------------------
| Center — self-service logo
|--------------------------------------------------------------------------
*/

it('lets a center upload its own logo into centers/logos', function () {
    Storage::fake('public');
    $center = actingAsCenterPanel();

    Livewire::test(CenterProfilePage::class)
        ->fillForm(['logo' => UploadedFile::fake()->image('logo.png', 300, 300)])
        ->call('save')
        ->assertHasNoFormErrors();

    $stored = $center->fresh()->logo;

    expect($stored)->not->toBeNull()
        ->and($stored)->toStartWith('centers/logos/')
        ->and(Storage::disk('public')->exists($stored))->toBeTrue();
});

it('rejects a center logo over the 2MB limit', function () {
    Storage::fake('public');
    $center = actingAsCenterPanel();

    Livewire::test(CenterProfilePage::class)
        ->fillForm(['logo' => UploadedFile::fake()->image('huge.png')->size(4096)])
        ->call('save')
        ->assertHasFormErrors(['logo']);

    expect($center->fresh()->logo)->toBeEmpty();
});

it('rejects an svg center logo', function () {
    Storage::fake('public');
    $center = actingAsCenterPanel();

    $svg = UploadedFile::fake()->createWithContent(
        'x.svg',
        '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'
    );

    Livewire::test(CenterProfilePage::class)
        ->fillForm(['logo' => $svg])
        ->call('save')
        ->assertHasFormErrors(['logo']);

    expect($center->fresh()->logo)->toBeEmpty();
});

/*
|--------------------------------------------------------------------------
| Scope — a subject only ever edits its own record
|--------------------------------------------------------------------------
*/

it('only ever writes the authenticated trainers own avatar', function () {
    Storage::fake('public');
    $mine = actingAsTrainerPanel();
    $theirs = Trainer::factory()->create(['avatar' => '']);

    Livewire::test(TrainerProfilePage::class)
        ->fillForm(['avatar' => UploadedFile::fake()->image('me.png')])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($mine->fresh()->avatar)->not->toBeNull()
        ->and($theirs->fresh()->avatar)->toBe('');
});

it('keeps the orphan sweep pointed at both self-service directories', function () {
    Storage::fake('public');

    Storage::disk('public')->put('trainers/avatars/orphan.png', 'x');
    Storage::disk('public')->put('centers/logos/orphan.png', 'x');
    touch(Storage::disk('public')->path('trainers/avatars/orphan.png'), time() - 172800);
    touch(Storage::disk('public')->path('centers/logos/orphan.png'), time() - 172800);

    $this->artisan('cleanup:storage')->assertSuccessful();

    expect(Storage::disk('public')->exists('trainers/avatars/orphan.png'))->toBeFalse()
        ->and(Storage::disk('public')->exists('centers/logos/orphan.png'))->toBeFalse();
});
