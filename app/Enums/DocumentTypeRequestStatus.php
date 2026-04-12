<?php

declare(strict_types=1);

namespace App\Enums;

enum DocumentTypeRequestStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('app.status_pending'),
            self::Approved => __('app.status_approved'),
            self::Rejected => __('app.status_rejected'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }
}
