<?php

declare(strict_types=1);

use App\Models\CertifiedCenter;
use App\Models\Trainer;
use App\Rules\ActiveCenterTrainer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

function validateTrainerRule(mixed $value, int $centerId): array
{
    $validator = Validator::make(
        ['assigned_trainer_id' => $value],
        ['assigned_trainer_id' => [new ActiveCenterTrainer($centerId)]],
    );

    return $validator->errors()->all();
}

it('passes when the trainer belongs to the center and has active accreditation', function () {
    $center = CertifiedCenter::factory()->create();
    $trainer = Trainer::factory()->create([
        'center_id' => $center->id,
        'accreditation_period_start' => now()->subDay(),
        'accreditation_period_end' => now()->addDay(),
    ]);

    expect(validateTrainerRule($trainer->id, $center->id))->toBeEmpty();
});

it('fails when the trainer belongs to another center', function () {
    $center = CertifiedCenter::factory()->create();
    $otherCenter = CertifiedCenter::factory()->create();
    $trainer = Trainer::factory()->create([
        'center_id' => $otherCenter->id,
        'accreditation_period_start' => now()->subDay(),
        'accreditation_period_end' => now()->addDay(),
    ]);

    expect(validateTrainerRule($trainer->id, $center->id))
        ->toContain(__('app.trainer_not_available'));
});

it('fails when the trainer accreditation is not active', function () {
    $center = CertifiedCenter::factory()->create();
    $trainer = Trainer::factory()->create([
        'center_id' => $center->id,
        'accreditation_period_start' => now()->subDays(10),
        'accreditation_period_end' => now()->subDays(5),
    ]);

    expect(validateTrainerRule($trainer->id, $center->id))
        ->toContain(__('app.trainer_accreditation_expired'));
});

it('fails when the trainer does not exist', function () {
    $center = CertifiedCenter::factory()->create();

    expect(validateTrainerRule(999999, $center->id))
        ->toContain(__('app.trainer_not_available'));
});

it('passes when the value is blank', function () {
    $center = CertifiedCenter::factory()->create();

    expect(validateTrainerRule('', $center->id))->toBeEmpty();
});
