<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\DocumentTypeRequestStatus;
use App\Filament\Center\Resources\CertifiedCenterDocumentTypes\CertifiedCenterDocumentTypeResource;
use App\Models\CertifiedCenterDocumentType;
use App\Notifications\AdminActionNotification;
use Illuminate\Support\Facades\Auth;

class CertifiedCenterDocumentTypeObserver
{
    public function updated(CertifiedCenterDocumentType $documentType): void
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

        if (Auth::guard('web')->check() && $documentType->certifiedCenter) {
            $documentType->certifiedCenter->notify(new AdminActionNotification(
                $documentType,
                $status->value,
                'center',
                CertifiedCenterDocumentTypeResource::class,
            ));
        }
    }
}
