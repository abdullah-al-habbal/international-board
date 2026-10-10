<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Filament\Center\Resources\Trainers\TrainerResource;
use App\Models\CertifiedCenter;
use App\Models\Trainer;
use App\Models\TrainerRole;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function actingAsAdminPanel(): User
{
    $admin = User::factory()->admin()->create();

    test()->actingAs($admin, 'web');
    Filament::setCurrentPanel('admin');

    return $admin;
}

/*
|--------------------------------------------------------------------------
| Schema
|--------------------------------------------------------------------------
*/

it('has an avatar column on every avatar-bearing table', function () {
    expect(Schema::hasColumn('users', 'avatar'))->toBeTrue()
        ->and(Schema::hasColumn('trainers', 'avatar'))->toBeTrue()
        ->and(Schema::hasColumn('certified_centers', 'logo'))->toBeTrue();
});

it('keeps existing users valid with a null avatar after the migration', function () {
    $users = User::factory()->count(3)->create();

    expect(User::count())->toBe(3)
        ->and(User::whereNull('avatar')->count())->toBe(3);

    foreach ($users as $user) {
        expect($user->fresh()->email)->toBe($user->email)
            ->and($user->fresh()->avatar)->toBeNull();
    }
});

/*
|--------------------------------------------------------------------------
| Accessors and fallbacks
|--------------------------------------------------------------------------
*/

it('returns a public url when the avatar file exists on disk', function () {
    Storage::fake('public');
    Storage::disk('public')->put('users/avatars/a.png', 'x');

    $user = User::factory()->create(['avatar' => 'users/avatars/a.png']);

    expect($user->avatar_url)->toBe(Storage::disk('public')->url('users/avatars/a.png'))
        ->and($user->getFilamentAvatarUrl())->toBe($user->avatar_url);
});

it('falls back to null when the avatar is null, empty, or missing from disk', function () {
    Storage::fake('public');

    expect(User::factory()->create(['avatar' => null])->avatar_url)->toBeNull()
        ->and(User::factory()->create(['avatar' => ''])->avatar_url)->toBeNull()
        ->and(User::factory()->create(['avatar' => 'users/avatars/gone.png'])->avatar_url)->toBeNull();
});

it('exposes the trainer avatar through the accessor and the filament contract', function () {
    Storage::fake('public');
    Storage::disk('public')->put('trainers/avatars/t.png', 'x');

    $trainer = Trainer::factory()->create(['avatar' => 'trainers/avatars/t.png']);

    expect($trainer->avatar_url)->toBe(Storage::disk('public')->url('trainers/avatars/t.png'))
        ->and($trainer->getFilamentAvatarUrl())->toBe($trainer->avatar_url);
});

it('falls back to null for a trainer without an avatar', function () {
    Storage::fake('public');

    expect(Trainer::factory()->create(['avatar' => ''])->avatar_url)->toBeNull()
        ->and(Trainer::factory()->create(['avatar' => ''])->getFilamentAvatarUrl())->toBeNull();
});

it('uses the center logo as the center filament avatar', function () {
    Storage::fake('public');
    Storage::disk('public')->put('centers/logos/c.png', 'x');

    $center = CertifiedCenter::factory()->create(['logo' => 'centers/logos/c.png']);

    expect($center->logo_url)->toBe(Storage::disk('public')->url('centers/logos/c.png'))
        ->and($center->getFilamentAvatarUrl())->toBe($center->logo_url);
});

it('falls back to null for a center without a logo', function () {
    Storage::fake('public');

    expect(CertifiedCenter::factory()->create(['logo' => null])->getFilamentAvatarUrl())->toBeNull();
});

it('implements the filament HasAvatar contract on all three authenticatables', function () {
    foreach ([User::class, Trainer::class, CertifiedCenter::class] as $model) {
        expect(new $model)->toBeInstanceOf(HasAvatar::class);
    }
});

/*
|--------------------------------------------------------------------------
| Assignment, replacement, removal
|--------------------------------------------------------------------------
*/

it('allows the avatar to be mass assigned', function () {
    $user = User::factory()->create();

    $user->update(['avatar' => 'users/avatars/set.png']);

    expect($user->fresh()->avatar)->toBe('users/avatars/set.png');
});

it('replaces an existing avatar path', function () {
    Storage::fake('public');
    Storage::disk('public')->put('users/avatars/old.png', 'x');
    Storage::disk('public')->put('users/avatars/new.png', 'x');

    $user = User::factory()->create(['avatar' => 'users/avatars/old.png']);
    $user->update(['avatar' => 'users/avatars/new.png']);

    expect($user->fresh()->avatar)->toBe('users/avatars/new.png')
        ->and($user->fresh()->avatar_url)->toContain('new.png');
});

it('clears an avatar back to null', function () {
    Storage::fake('public');
    $user = User::factory()->create(['avatar' => 'users/avatars/x.png']);

    $user->update(['avatar' => null]);

    expect($user->fresh()->avatar)->toBeNull()
        ->and($user->fresh()->avatar_url)->toBeNull();
});

/*
|--------------------------------------------------------------------------
| Upload through the admin panel
|--------------------------------------------------------------------------
*/

it('uploads an avatar for a user through the admin form', function () {
    Storage::fake('public');
    actingAsAdminPanel();
    $user = User::factory()->create();

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->fillForm(['avatar' => UploadedFile::fake()->image('me.png', 200, 200)])
        ->call('save')
        ->assertHasNoFormErrors();

    $stored = $user->fresh()->avatar;

    expect($stored)->not->toBeNull()
        ->and($stored)->toStartWith('users/avatars/')
        ->and(Storage::disk('public')->exists($stored))->toBeTrue();
});

it('rejects a non-image upload', function () {
    Storage::fake('public');
    actingAsAdminPanel();
    $user = User::factory()->create();

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->fillForm(['avatar' => UploadedFile::fake()->create('payload.pdf', 16, 'application/pdf')])
        ->call('save')
        ->assertHasFormErrors(['avatar']);

    expect($user->fresh()->avatar)->toBeNull();
});

it('rejects an svg upload so scriptable images cannot be stored', function () {
    Storage::fake('public');
    actingAsAdminPanel();
    $user = User::factory()->create();

    $svg = UploadedFile::fake()->createWithContent(
        'x.svg',
        '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'
    );

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->fillForm(['avatar' => $svg])
        ->call('save')
        ->assertHasFormErrors(['avatar']);

    expect($user->fresh()->avatar)->toBeNull();
});

it('rejects a php payload disguised with an image extension', function () {
    Storage::fake('public');
    actingAsAdminPanel();
    $user = User::factory()->create();

    $php = UploadedFile::fake()->createWithContent('shell.png', '<?php echo "pwned"; ?>');

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->fillForm(['avatar' => $php])
        ->call('save')
        ->assertHasFormErrors(['avatar']);

    expect($user->fresh()->avatar)->toBeNull();
});

it('rejects an oversized image', function () {
    Storage::fake('public');
    actingAsAdminPanel();
    $user = User::factory()->create();

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->fillForm(['avatar' => UploadedFile::fake()->image('huge.png')->size(4096)])
        ->call('save')
        ->assertHasFormErrors(['avatar']);

    expect($user->fresh()->avatar)->toBeNull();
});

/*
|--------------------------------------------------------------------------
| Authorization
|--------------------------------------------------------------------------
*/

it('blocks non-admin users from the user resource that owns avatar upload', function () {
    $client = User::factory()->create();

    $this->actingAs($client, 'web')
        ->get(UserResource::getUrl('index'))
        ->assertForbidden();
});

it('scopes the center trainer query to the centers own trainers', function () {
    $mine = CertifiedCenter::factory()->create();
    $theirs = CertifiedCenter::factory()->create();

    $ownTrainer = Trainer::factory()->create(['center_id' => $mine->id]);
    $otherTrainer = Trainer::factory()->create(['center_id' => $theirs->id]);

    $this->actingAs($mine, 'certified_center');

    $ids = TrainerResource::getEloquentQuery()->pluck('id');

    expect($ids)->toContain($ownTrainer->id)
        ->and($ids)->not->toContain($otherTrainer->id);
});

/*
|--------------------------------------------------------------------------
| Cross-feature regression: avatar and trainer role coexist
|--------------------------------------------------------------------------
*/

it('keeps avatar and trainer role working together on one trainer', function () {
    Storage::fake('public');
    Storage::disk('public')->put('trainers/avatars/both.png', 'x');

    $role = TrainerRole::factory()->create();
    $trainer = Trainer::factory()->create([
        'avatar' => 'trainers/avatars/both.png',
        'trainer_role_id' => $role->id,
    ]);

    $fresh = Trainer::with('trainerRole')->find($trainer->id);

    expect($fresh->avatar_url)->toContain('both.png')
        ->and($fresh->getFilamentAvatarUrl())->toContain('both.png')
        ->and($fresh->trainerRole->is($role))->toBeTrue()
        ->and($fresh->canAccessPanel(Panel::make()->id('trainer')))->toBeTrue();
});

it('keeps user authentication and admin panel access intact alongside the avatar', function () {
    Storage::fake('public');
    Storage::disk('public')->put('users/avatars/admin.png', 'x');

    $admin = User::factory()->admin()->create(['avatar' => 'users/avatars/admin.png']);
    $client = User::factory()->create();

    expect($admin->canAccessPanel(Panel::make()->id('admin')))->toBeTrue()
        ->and($client->canAccessPanel(Panel::make()->id('admin')))->toBeFalse()
        ->and($admin->isAdmin())->toBeTrue()
        ->and($admin->avatar_url)->toContain('admin.png');

    $this->actingAs($admin, 'web')->get(UserResource::getUrl('index'))->assertSuccessful();
});
