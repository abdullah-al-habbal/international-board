<?php

declare(strict_types=1);

use App\Models\CertifiedCenter;
use App\Models\CertifiedCenterDocumentType;
use App\Models\Trainer;
use App\Models\TrainerDocumentType;
use App\Models\User;
use App\Notifications\AdminActionPerformed;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

it('notifies admins when a center creates a document type', function () {
    $admin = User::factory()->admin()->create();
    $center = CertifiedCenter::factory()->create();

    Auth::guard('certified_center')->login($center);

    CertifiedCenterDocumentType::factory()->create(['certified_center_id' => $center->id]);

    $notification = DatabaseNotification::query()
        ->where('notifiable_type', User::class)
        ->where('notifiable_id', $admin->id)
        ->where('type', AdminActionPerformed::class)
        ->first();

    expect($notification)->not->toBeNull()
        ->and($notification->data)
        ->toMatchArray([
            'action' => 'created',
            'model_class' => CertifiedCenterDocumentType::class,
            'format' => 'filament',
        ]);
});

it('notifies admins when a trainer updates a document type', function () {
    $admin = User::factory()->admin()->create();
    $trainer = Trainer::factory()->create();

    Auth::guard('trainer')->login($trainer);

    $documentType = TrainerDocumentType::factory()->create(['trainer_id' => $trainer->id]);
    $documentType->update(['admin_notes' => 'updated by trainer']);

    $notifications = DatabaseNotification::query()
        ->where('notifiable_type', User::class)
        ->where('notifiable_id', $admin->id)
        ->where('type', AdminActionPerformed::class)
        ->get();

    $updated = $notifications->first(fn ($notification) => $notification->data['action'] === 'updated');

    expect($notifications)->toHaveCount(2)
        ->and($updated)->not->toBeNull()
        ->and($updated->data)
        ->toMatchArray([
            'action' => 'updated',
            'model_class' => TrainerDocumentType::class,
        ]);
});

it('does not notify admins when an admin performs the mutation', function () {
    $admin = User::factory()->admin()->create();

    Auth::guard('web')->login($admin);

    CertifiedCenterDocumentType::factory()->create();

    expect(DatabaseNotification::query()->count())->toBe(0);
});

it('does not notify admins when no actor guard is active', function () {
    User::factory()->admin()->create();

    CertifiedCenterDocumentType::factory()->create();

    expect(DatabaseNotification::query()->count())->toBe(0);
});
