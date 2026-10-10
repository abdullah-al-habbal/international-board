<?php

declare(strict_types=1);

use App\Models\Certification;
use App\Models\Trainee;
use App\Models\Trainer;
use App\Models\User;
use App\Repositories\Certification\CertificationRepository;
use App\Services\Stats\TrainerStatsService;
use Tests\Support\Spider;

/**
 * On production almost every certification is created by an admin (User) and
 * linked to its trainer through `assigned_trainer_id`, not through the
 * `creator` morph. Counting only the morph made public profiles read 0 for
 * trainers holding hundreds of certifications.
 */
function publicTrainer(): Trainer
{
    return Trainer::factory()->create([
        'show_in_public_website' => true,
        'accreditation_period_start' => today()->subYear(),
        'accreditation_period_end' => today()->addYear(),
    ]);
}

function assignedCertification(Trainer $trainer, bool $visible = true): Certification
{
    return Certification::factory()->create([
        'creator_type' => User::class,
        'creator_id' => User::factory(),
        'assigned_trainer_id' => $trainer->id,
        'show_in_public_website' => $visible,
        'trainee_id' => Trainee::factory()->create(['show_in_public_website' => true]),
    ]);
}

it('counts admin-created certifications assigned to the trainer', function () {
    $trainer = publicTrainer();

    foreach (range(1, 3) as $ignored) {
        assignedCertification($trainer);
    }

    // ViewServiceProvider skips its View::share calls in console, so the public
    // layout needs those globals re-shared, exactly as Spider does.
    (new Spider)->sharePublicViewGlobals(app()->getLocale());

    $this->get(route('web.trainers.show', $trainer->id))
        ->assertOk()
        ->assertSee('3');

    // The morph alone still reports zero — the fix must not rely on it.
    expect($trainer->certifications()->count())->toBe(0);
});

it('counts certifications the trainer created and ones assigned to them', function () {
    $trainer = publicTrainer();

    Certification::factory()->create([
        'creator_type' => Trainer::class,
        'creator_id' => $trainer->id,
        'show_in_public_website' => true,
        'trainee_id' => Trainee::factory()->create(['show_in_public_website' => true]),
    ]);

    assignedCertification($trainer);
    assignedCertification($trainer);

    expect(publicCertificationCountFor($trainer))->toBe(3);
});

it('never double counts a certification the trainer created and was assigned', function () {
    $trainer = publicTrainer();

    Certification::factory()->create([
        'creator_type' => Trainer::class,
        'creator_id' => $trainer->id,
        'assigned_trainer_id' => $trainer->id,
        'show_in_public_website' => true,
        'trainee_id' => Trainee::factory()->create(['show_in_public_website' => true]),
    ]);

    expect(publicCertificationCountFor($trainer))->toBe(1);
});

it('excludes certifications hidden by their own visibility flag', function () {
    $trainer = publicTrainer();

    assignedCertification($trainer);
    assignedCertification($trainer, visible: false);

    expect(publicCertificationCountFor($trainer))->toBe(1);
});

it('excludes certifications whose trainee is hidden', function () {
    $trainer = publicTrainer();

    assignedCertification($trainer);

    Certification::factory()->create([
        'creator_type' => User::class,
        'creator_id' => User::factory(),
        'assigned_trainer_id' => $trainer->id,
        'show_in_public_website' => true,
        'trainee_id' => Trainee::factory()->create(['show_in_public_website' => false]),
    ]);

    expect(publicCertificationCountFor($trainer))->toBe(1);
});

it('shows assigned certifications on the trainer panel dashboard stats', function () {
    $trainer = publicTrainer();

    assignedCertification($trainer);
    assignedCertification($trainer);

    $stats = app(TrainerStatsService::class)
        ->getTrainerDashboardStats($trainer);

    expect($stats['total_certifications'])->toBe(2);
});

function publicCertificationCountFor(Trainer $trainer): int
{
    return app(CertificationRepository::class)
        ->countPubliclyVisibleForTrainer($trainer->id);
}
