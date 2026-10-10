<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\TrainerRoles\Pages\CreateTrainerRole;
use App\Filament\Admin\Resources\TrainerRoles\Pages\EditTrainerRole;
use App\Filament\Admin\Resources\TrainerRoles\TrainerRoleResource;
use App\Models\CertifiedCenter;
use App\Models\Trainer;
use App\Models\TrainerRole;
use App\Models\User;
use App\Repositories\Trainer\TrainerRepository;
use Database\Seeders\SpecializationSeeder;
use Database\Seeders\TrainerRoleSeeder;
use Database\Seeders\TrainerSeeder;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

it('creates a trainer role with localized name', function () {
    $role = TrainerRole::factory()->create([
        'name' => ['en' => 'Sales Manager', 'ar' => 'مدير مبيعات'],
    ]);

    expect($role->exists)->toBeTrue()
        ->and($role->getTranslation('name', 'en'))->toBe('Sales Manager')
        ->and($role->getTranslation('name', 'ar'))->toBe('مدير مبيعات');
});

it('updates a trainer role name', function () {
    $role = TrainerRole::factory()->create();

    $role->update(['name' => ['en' => 'Updated Role', 'ar' => 'دور محدث']]);

    expect($role->refresh()->getTranslation('name', 'en'))->toBe('Updated Role')
        ->and($role->getTranslation('name', 'ar'))->toBe('دور محدث');
});

it('seeds the deterministic role list', function () {
    $this->seed(TrainerRoleSeeder::class);

    expect(TrainerRole::count())->toBe(4)
        ->and(TrainerRole::query()->where('name->en', 'Senior Trainer')->exists())->toBeTrue();

    foreach (TrainerRole::all() as $role) {
        expect($role->getTranslation('name', 'en'))->not->toBeEmpty()
            ->and($role->getTranslation('name', 'ar'))->not->toBeEmpty();
    }
});

it('is idempotent when re-seeded', function () {
    $this->seed(TrainerRoleSeeder::class);
    $this->seed(TrainerRoleSeeder::class);

    expect(TrainerRole::count())->toBe(4);
});

it('has many trainers', function () {
    $role = TrainerRole::factory()->create();
    $trainers = Trainer::factory(2)->create(['trainer_role_id' => $role->id]);

    expect($role->trainers->pluck('id')->sort()->values())
        ->toEqual($trainers->pluck('id')->sort()->values());
});

it('belongs to a trainer role and allows trainers without one', function () {
    $role = TrainerRole::factory()->create();
    $assigned = Trainer::factory()->create(['trainer_role_id' => $role->id]);
    $unassigned = Trainer::factory()->create(['trainer_role_id' => null]);

    expect($assigned->trainerRole->is($role))->toBeTrue()
        ->and($unassigned->trainer_role_id)->toBeNull()
        ->and($unassigned->trainerRole)->toBeNull();
});

it('assigns an existing trainer role to an existing trainer without touching other data', function () {
    $trainer = Trainer::factory()->create();
    $originalName = $trainer->name;

    $role = TrainerRole::factory()->create(['name' => ['en' => 'Regional Manager', 'ar' => 'مدير إقليمي']]);
    $trainer->update(['trainer_role_id' => $role->id]);

    expect($trainer->refresh()->trainerRole?->getTranslation('name', 'en'))->toBe('Regional Manager')
        ->and($trainer->refresh()->name)->toBe($originalName);
});

it('nullifies trainer_role_id instead of losing the trainer when the role is deleted', function () {
    $role = TrainerRole::factory()->create();
    $trainer = Trainer::factory()->create(['trainer_role_id' => $role->id]);

    $role->delete();

    expect(Trainer::find($trainer->id))->not->toBeNull()
        ->and($trainer->refresh()->trainer_role_id)->toBeNull();
});

it('keeps existing trainers valid after the migration adds the column', function () {
    $before = Trainer::count();

    Trainer::factory(3)->create();

    expect(Trainer::whereNull('trainer_role_id')->count())->toBe($before + 3)
        ->and(DB::getSchemaBuilder()->getColumnType('trainers', 'trainer_role_id'))->not->toBeFalse();
});

it('allows admins to manage trainer roles and denies other users', function () {
    $admin = User::factory()->admin()->create();
    $client = User::factory()->create();
    $role = TrainerRole::factory()->create();

    expect($admin->can('viewAny', TrainerRole::class))->toBeTrue()
        ->and($admin->can('create', TrainerRole::class))->toBeTrue()
        ->and($admin->can('update', $role))->toBeTrue()
        ->and($admin->can('delete', $role))->toBeTrue()
        ->and($client->can('viewAny', TrainerRole::class))->toBeFalse()
        ->and($client->can('create', TrainerRole::class))->toBeFalse()
        ->and($client->can('update', $role))->toBeFalse()
        ->and($client->can('delete', $role))->toBeFalse();
});

it('does not grant trainer panel users access to the admin resource', function () {
    $trainer = Trainer::factory()->create();
    $adminPanel = Panel::make()->id('admin');

    expect($trainer->canAccessPanel($adminPanel))->toBeFalse();
});

it('renders the admin resource pages for admins', function () {
    $admin = User::factory()->admin()->create();
    $role = TrainerRole::factory()->create();

    $this->actingAs($admin, 'web')->get(TrainerRoleResource::getUrl('index'))
        ->assertSuccessful();

    $this->actingAs($admin, 'web')->get(TrainerRoleResource::getUrl('view', ['record' => $role]))
        ->assertSuccessful();

    $this->actingAs($admin, 'web')->get(TrainerRoleResource::getUrl('edit', ['record' => $role]))
        ->assertSuccessful();
});

it('blocks non-admin users from the admin resource', function () {
    $client = User::factory()->create();

    $this->actingAs($client, 'web')
        ->get(TrainerRoleResource::getUrl('index'))
        ->assertForbidden();
});

it('eager loads the role when listing public trainers to avoid n+1 queries', function () {
    $role = TrainerRole::factory()->create();
    Trainer::factory(5)->create([
        'show_in_public_website' => true,
        'trainer_role_id' => $role->id,
    ]);

    DB::enableQueryLog();
    DB::flushQueryLog();

    $trainers = app(TrainerRepository::class)->paginateActive(perPage: 10);
    $baseQueryCount = count(DB::getQueryLog());

    foreach ($trainers as $trainer) {
        $trainer->trainerRole?->name;
    }

    $totalQueryCount = count(DB::getQueryLog());

    DB::disableQueryLog();

    expect($totalQueryCount)->toBe($baseQueryCount);
});

/**
 * Signs in as an admin inside the admin panel so Filament page components
 * can be driven directly through Livewire.
 */
function actAsPanelAdmin(): User
{
    $admin = User::factory()->admin()->create();

    test()->actingAs($admin, 'web');
    Filament::setCurrentPanel('admin');

    return $admin;
}

it('rejects a trainer role with a missing english name', function () {
    actAsPanelAdmin();

    Livewire::test(CreateTrainerRole::class)
        ->fillForm(['name' => ['en' => '', 'ar' => 'مدرب أول']])
        ->call('create')
        ->assertHasFormErrors(['name.en' => 'required']);

    expect(TrainerRole::count())->toBe(0);
});

it('rejects a trainer role with a missing arabic name', function () {
    actAsPanelAdmin();

    Livewire::test(CreateTrainerRole::class)
        ->fillForm(['name' => ['en' => 'Senior Trainer', 'ar' => '']])
        ->call('create')
        ->assertHasFormErrors(['name.ar' => 'required']);

    expect(TrainerRole::count())->toBe(0);
});

it('rejects names longer than the column limit', function () {
    actAsPanelAdmin();

    Livewire::test(CreateTrainerRole::class)
        ->fillForm(['name' => ['en' => str_repeat('a', 256), 'ar' => str_repeat('ب', 256)]])
        ->call('create')
        ->assertHasFormErrors(['name.en' => 'max', 'name.ar' => 'max']);

    expect(TrainerRole::count())->toBe(0);
});

it('creates a trainer role through the admin form with both locales', function () {
    actAsPanelAdmin();

    Livewire::test(CreateTrainerRole::class)
        ->fillForm(['name' => ['en' => 'Lead Instructor', 'ar' => 'محاضر رئيسي']])
        ->call('create')
        ->assertHasNoFormErrors();

    $role = TrainerRole::sole();

    expect($role->getTranslation('name', 'en'))->toBe('Lead Instructor')
        ->and($role->getTranslation('name', 'ar'))->toBe('محاضر رئيسي');
});

it('updates a trainer role through the admin form without creating a new record', function () {
    actAsPanelAdmin();
    $role = TrainerRole::factory()->create(['name' => ['en' => 'Old', 'ar' => 'قديم']]);

    Livewire::test(EditTrainerRole::class, ['record' => $role->getRouteKey()])
        ->fillForm(['name' => ['en' => 'New', 'ar' => 'جديد']])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(TrainerRole::count())->toBe(1)
        ->and($role->refresh()->getTranslation('name', 'en'))->toBe('New')
        ->and($role->getTranslation('name', 'ar'))->toBe('جديد');
});

it('allows a duplicate localized name because no uniqueness rule is defined', function () {
    actAsPanelAdmin();
    TrainerRole::factory()->create(['name' => ['en' => 'Senior Trainer', 'ar' => 'مدرب أول']]);

    Livewire::test(CreateTrainerRole::class)
        ->fillForm(['name' => ['en' => 'Senior Trainer', 'ar' => 'مدرب أول']])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(TrainerRole::count())->toBe(2);
});

it('covers every policy ability for admins and denies clients', function () {
    $admin = User::factory()->admin()->create();
    $client = User::factory()->create();
    $role = TrainerRole::factory()->create();

    foreach (['viewAny', 'create'] as $ability) {
        expect($admin->can($ability, TrainerRole::class))->toBeTrue()
            ->and($client->can($ability, TrainerRole::class))->toBeFalse();
    }

    foreach (['view', 'update', 'delete', 'restore', 'forceDelete'] as $ability) {
        expect($admin->can($ability, $role))->toBeTrue()
            ->and($client->can($ability, $role))->toBeFalse();
    }
});

it('keeps center and trainer guard users out of the admin panel', function () {
    $adminPanel = Panel::make()->id('admin');

    expect(CertifiedCenter::factory()->create()->canAccessPanel($adminPanel))->toBeFalse()
        ->and(Trainer::factory()->create()->canAccessPanel($adminPanel))->toBeFalse();
});

it('does not register the trainer role resource outside the admin panel', function () {
    foreach (['center', 'trainer'] as $panelId) {
        expect(Filament::getPanel($panelId)->getResources())
            ->not->toContain(TrainerRoleResource::class);
    }

    expect(Filament::getPanel('admin')->getResources())
        ->toContain(TrainerRoleResource::class);
});

it('builds more roles than the reference dataset without overflowing', function () {
    $roles = TrainerRole::factory()->count(20)->create();

    expect($roles)->toHaveCount(20)
        ->and(TrainerRole::count())->toBe(20);

    foreach ($roles as $role) {
        expect($role->getTranslation('name', 'en'))->not->toBeEmpty()
            ->and($role->getTranslation('name', 'ar'))->not->toBeEmpty();
    }
});

it('assigns seeded roles to seeded trainers while keeping the optional path represented', function () {
    CertifiedCenter::factory()->count(2)->create();
    $this->seed(SpecializationSeeder::class);
    $this->seed(TrainerRoleSeeder::class);

    $this->seed(TrainerSeeder::class);

    expect(Trainer::whereNotNull('trainer_role_id')->count())->toBe(4)
        ->and(Trainer::whereNull('trainer_role_id')->count())->toBe(1);

    foreach (Trainer::whereNotNull('trainer_role_id')->get() as $trainer) {
        expect(TrainerRole::find($trainer->trainer_role_id))->not->toBeNull();
    }
});
