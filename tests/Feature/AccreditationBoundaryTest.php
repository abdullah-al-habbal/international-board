<?php

declare(strict_types=1);

use App\Enums\AccreditationStatus;
use App\Models\CertifiedCenter;
use App\Models\Trainer;
use App\Models\TrainerAccreditationRequest;
use App\Services\Stats\TrainerStatsService;
use Carbon\Carbon;

/**
 * Accreditation periods are stored as datetimes but are a date-only concept:
 * every screen renders them as Y-m-d. Production rows carry an arbitrary
 * wall-clock time baked in by the DateTimePicker (e.g. `2027-04-30 19:05:52`),
 * so comparing against `now()` killed credentials mid-afternoon on their own
 * final day. A period must stay valid through the last instant of its end date.
 */
afterEach(function () {
    Carbon::setTestNow();
});

/** The exact production shape: end date carries a stray afternoon timestamp. */
function trainerEndingToday(): Trainer
{
    return Trainer::factory()->create([
        'accreditation_period_start' => today()->subYear()->setTime(16, 10, 33),
        'accreditation_period_end' => today()->setTime(19, 5, 52),
    ]);
}

function centerEndingToday(): CertifiedCenter
{
    return CertifiedCenter::factory()->create([
        'accreditation_period_start' => today()->subYear()->setTime(14, 22, 22),
        'accreditation_period_end' => today()->setTime(14, 24, 59),
    ]);
}

it('keeps a trainer active late on their final day', function () {
    Carbon::setTestNow(today()->setTime(23, 59, 59));

    expect(trainerEndingToday()->isAccreditationActive())->toBeTrue();
});

it('keeps a trainer active after the stray timestamp has passed', function () {
    Carbon::setTestNow(today()->setTime(19, 6, 0));

    expect(trainerEndingToday()->isAccreditationActive())->toBeTrue();
});

it('expires a trainer only once the end date is behind us', function () {
    $trainer = trainerEndingToday();

    Carbon::setTestNow(today()->addDay()->startOfDay());

    expect($trainer->isAccreditationActive())->toBeFalse();
});

it('keeps a trainer active at the very start of their first day', function () {
    $trainer = Trainer::factory()->create([
        'accreditation_period_start' => today()->setTime(16, 10, 33),
        'accreditation_period_end' => today()->addYear()->setTime(16, 10, 33),
    ]);

    Carbon::setTestNow(today()->startOfDay());

    expect($trainer->isAccreditationActive())->toBeTrue();
});

it('does not list a trainer ending today as expired', function () {
    $trainer = trainerEndingToday();

    Carbon::setTestNow(today()->setTime(23, 59, 59));

    expect(Trainer::query()->accreditationExpired()->whereKey($trainer->id)->exists())->toBeFalse()
        ->and(Trainer::query()->accreditationActive()->whereKey($trainer->id)->exists())->toBeTrue();
});

it('keeps a center active late on its final day', function () {
    Carbon::setTestNow(today()->setTime(23, 59, 59));

    expect(centerEndingToday()->isAccreditationActive())->toBeTrue();
});

it('does not list a center ending today as expired', function () {
    $center = centerEndingToday();

    Carbon::setTestNow(today()->setTime(23, 59, 59));

    expect(CertifiedCenter::query()->accreditationExpired()->whereKey($center->id)->exists())->toBeFalse()
        ->and(CertifiedCenter::query()->accreditationActive()->whereKey($center->id)->exists())->toBeTrue();
});

it('does not lock a trainer out of the panel on their final day', function () {
    $trainer = trainerEndingToday();

    TrainerAccreditationRequest::factory()->create([
        'trainer_id' => $trainer->id,
        'status' => AccreditationStatus::Approved,
        'accreditation_start_date' => today()->subYear()->setTime(16, 10, 33),
        'accreditation_end_date' => today()->setTime(19, 5, 52),
    ]);

    Carbon::setTestNow(today()->setTime(23, 59, 59));

    expect($trainer->hasApprovedNonExpiredRequest())->toBeTrue()
        ->and($trainer->canPerformActions())->toBeTrue()
        ->and($trainer->accreditationBlockReason())->toBeNull();
});

it('reports zero days remaining on the final day, not a negative count', function () {
    $trainer = trainerEndingToday();

    Carbon::setTestNow(today()->setTime(23, 59, 59));

    $stats = app(TrainerStatsService::class)
        ->getAccreditationStatusData($trainer);

    expect($stats['days_until_expiry'])->toBe(0)
        ->and($stats['status_label'])->toBe(__('widgets.status.active'))
        ->and($stats['color'])->not->toBe('danger');
});

/**
 * Both periods sit entirely in the future so the duplicate-active-request guard
 * cannot fire — the overlap check is the only thing that can reject this.
 * Under date-only semantics, a period ending on day X and one starting on day X
 * both cover day X and must collide, even though their clock times do not.
 */
it('rejects a period that starts on the day another one ends', function () {
    $trainer = Trainer::factory()->create();

    TrainerAccreditationRequest::factory()->create([
        'trainer_id' => $trainer->id,
        'status' => AccreditationStatus::Approved,
        'accreditation_start_date' => today()->addMonths(2),
        'accreditation_end_date' => today()->addMonths(3)->setTime(9, 0, 0),
    ]);

    expect(fn () => TrainerAccreditationRequest::factory()->create([
        'trainer_id' => $trainer->id,
        'status' => AccreditationStatus::Approved,
        'accreditation_start_date' => today()->addMonths(3)->setTime(17, 0, 0),
        'accreditation_end_date' => today()->addMonths(6),
    ]))->toThrow(DomainException::class);
});

it('allows a period starting the day after another one ends', function () {
    $trainer = Trainer::factory()->create();

    TrainerAccreditationRequest::factory()->create([
        'trainer_id' => $trainer->id,
        'status' => AccreditationStatus::Approved,
        'accreditation_start_date' => today()->addMonths(2),
        'accreditation_end_date' => today()->addMonths(3)->setTime(9, 0, 0),
    ]);

    $next = TrainerAccreditationRequest::factory()->create([
        'trainer_id' => $trainer->id,
        'status' => AccreditationStatus::Approved,
        'accreditation_start_date' => today()->addMonths(3)->addDay()->setTime(17, 0, 0),
        'accreditation_end_date' => today()->addMonths(6),
    ]);

    expect($next->exists)->toBeTrue();
});
