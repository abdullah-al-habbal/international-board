<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\DocumentTypeRequestStatus;
use App\Filament\Trainer\Resources\TrainerDocumentTypes\TrainerDocumentTypeResource;
use App\Models\TrainerDocumentType;
use App\Notifications\AdminActionNotification;
use Illuminate\Support\Facades\Auth;

class TrainerDocumentTypeObserver
{
    public function updated(TrainerDocumentType $documentType): void
    {
        if (! $documentType->wasChanged('status')) {
            return;
        }

        $status = $documentType->status;

        if (! in_array($status, [
            DocumentTypeRequestStatus::Approved,
            DocumentTypeRequestStatus::Rejected,
        ], true)) {
            return;
        }

        if (Auth::guard('web')->check() && $documentType->trainer) {
            $documentType->trainer->notify(new AdminActionNotification(
                $documentType,
                $status->value,
                'trainer',
                TrainerDocumentTypeResource::class,
            ));
        }
    }
}
