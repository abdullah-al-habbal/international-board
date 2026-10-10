<?php

declare(strict_types=1);

use App\Enums\AccreditationStatus;
use App\Models\CenterAccreditationRequest;
use App\Models\CertifiedCenter;
use App\Models\CertifiedCenterDocumentType;
use App\Models\Trainer;
use App\Models\TrainerAccreditationRequest;
use App\Models\User;
use App\Notifications\AdminActionNotification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

it('notifies a center when an admin approves their accreditation request', function () {
    $admin = User::factory()->admin()->create();
    $center = CertifiedCenter::factory()->create();

    $request = CenterAccreditationRequest::factory()->create([
        'certified_center_id' => $center->id,
        'status' => AccreditationStatus::Pending->value,
    ]);

    Auth::guard('web')->login($admin);

    $request->update([
        'status' => AccreditationStatus::Approved->value,
        'accreditation_start_date' => now(),
        'accreditation_end_date' => now()->addYear(),
        'reviewed_by' => $admin->id,
        'reviewed_at' => now(),
    ]);

    $notification = DatabaseNotification::query()
        ->where('notifiable_type', CertifiedCenter::class)
        ->where('notifiable_id', $center->id)
        ->where('type', AdminActionNotification::class)
        ->first();

    expect($notification)->not->toBeNull()
        ->and($notification->data)
        ->toMatchArray([
            'action' => 'approved',
            'model_class' => CenterAccreditationRequest::class,
            'format' => 'filament',
        ]);
});

it('notifies a trainer when an admin rejects their accreditation request', function () {
    $admin = User::factory()->admin()->create();
    $trainer = Trainer::factory()->create();

    $request = TrainerAccreditationRequest::factory()->create([
        'trainer_id' => $trainer->id,
        'status' => AccreditationStatus::Pending->value,
    ]);

    Auth::guard('web')->login($admin);

    $request->update([
        'status' => AccreditationStatus::Rejected->value,
        'reviewed_by' => $admin->id,
        'reviewed_at' => now(),
    ]);

    $notification = DatabaseNotification::query()
        ->where('notifiable_type', Trainer::class)
        ->where('notifiable_id', $trainer->id)
        ->where('type', AdminActionNotification::class)
        ->first();

    expect($notification)->not->toBeNull()
        ->and($notification->data)
        ->toMatchArray([
            'action' => 'rejected',
            'model_class' => TrainerAccreditationRequest::class,
            'format' => 'filament',
        ]);
});

it('notifies a center when an admin approves their document type', function () {
    $admin = User::factory()->admin()->create();
    $center = CertifiedCenter::factory()->create();

    $documentType = CertifiedCenterDocumentType::factory()->create([
        'certified_center_id' => $center->id,
        'status' => 'pending',
    ]);

    Auth::guard('web')->login($admin);

    $documentType->update(['status' => 'approved']);

    $notification = DatabaseNotification::query()
        ->where('notifiable_type', CertifiedCenter::class)
        ->where('notifiable_id', $center->id)
        ->where('type', AdminActionNotification::class)
        ->first();

    expect($notification)->not->toBeNull()
        ->and($notification->data)
        ->toMatchArray([
            'action' => 'approved',
            'model_class' => CertifiedCenterDocumentType::class,
        ]);
});

it('does not notify the owner when the actor is not an admin', function () {
    $center = CertifiedCenter::factory()->create();

    $documentType = CertifiedCenterDocumentType::factory()->create([
        'certified_center_id' => $center->id,
        'status' => 'pending',
    ]);

    Auth::guard('certified_center')->login($center);

    $documentType->update(['status' => 'approved']);

    $count = DatabaseNotification::query()
        ->where('notifiable_type', CertifiedCenter::class)
        ->where('notifiable_id', $center->id)
        ->where('type', AdminActionNotification::class)
        ->count();

    expect($count)->toBe(0);
});

it('builds a view url pointing to the owner panel', function () {
    $admin = User::factory()->admin()->create();
    $trainer = Trainer::factory()->create();

    $request = TrainerAccreditationRequest::factory()->create([
        'trainer_id' => $trainer->id,
        'status' => AccreditationStatus::Pending->value,
    ]);

    Auth::guard('web')->login($admin);

    $request->update([
        'status' => AccreditationStatus::Approved->value,
        'reviewed_by' => $admin->id,
        'reviewed_at' => now(),
    ]);

    $notification = DatabaseNotification::query()
        ->where('notifiable_type', Trainer::class)
        ->where('notifiable_id', $trainer->id)
        ->where('type', AdminActionNotification::class)
        ->first();

    $url = $notification->data['actions'][0]['url'] ?? null;

    expect($url)
        ->toContain('/trainer/trainer-accreditation-requests/')
        ->toContain((string) $request->getKey());
});
