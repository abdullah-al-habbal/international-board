<?php

declare(strict_types=1);

use App\Models\CertifiedCenter;
use App\Models\Trainee;
use App\Models\Trainer;
use App\Models\User;
use Filament\Facades\Filament;

/**
 * NotifiesAdminOnMutation only fires while a trainer/center guard is
 * authenticated, so the *active* panel at send time is theirs — not the admin
 * panel that owns the resource the notification links to. Building the URL
 * without naming the panel asked for a route that does not exist in the acting
 * panel and threw RouteNotFoundException mid-send.
 */
it('links a trainer-initiated trainee notification at the admin panel, not the trainer panel', function () {
    User::factory()->admin()->create();
    $trainer = Trainer::factory()->create();

    $this->actingAs($trainer, 'trainer');
    Filament::setCurrentPanel('trainer');

    $trainee = Trainee::factory()->create();

    $notification = DB::table('notifications')->latest('created_at')->first();

    expect($notification)->not->toBeNull();

    $url = data_get(json_decode($notification->data, true), 'actions.0.url');

    expect($url)->toContain('/admin/')
        ->and($url)->not->toContain('/trainer/')
        ->and($url)->toContain((string) $trainee->getKey());
});

it('does not throw while notifying from the center panel', function () {
    User::factory()->admin()->create();
    $center = CertifiedCenter::factory()->create();

    $this->actingAs($center, 'certified_center');
    Filament::setCurrentPanel('center');

    Trainee::factory()->create();

    expect(DB::table('notifications')->count())->toBeGreaterThan(0);
});

it('still links correctly when no panel is active', function () {
    User::factory()->admin()->create();
    $trainer = Trainer::factory()->create();

    $this->actingAs($trainer, 'trainer');

    Trainee::factory()->create();

    $notification = DB::table('notifications')->latest('created_at')->first();
    $url = data_get(json_decode($notification->data, true), 'actions.0.url');

    expect($url)->toContain('/admin/');
});
