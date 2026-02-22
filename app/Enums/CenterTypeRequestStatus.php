<?php

declare(strict_types=1);

namespace App\Enums;

enum CenterTypeRequestStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }

    public function label(): string
    {
        return __('enums.center_type_request_status.'.$this->value);
    }
}
