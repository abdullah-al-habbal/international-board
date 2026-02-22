<?php

// filePath: app/Enums/AccreditationStatus.php
declare(strict_types=1);

namespace App\Enums;

enum AccreditationStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case UnderReview = 'under_review';

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::UnderReview => 'info',
        };
    }

    public function rgb(): string
    {
        return match ($this) {
            self::Pending => ChartColors::Warning->value,
            self::Approved => ChartColors::Primary->value,
            self::Rejected => ChartColors::Danger->value,
            self::UnderReview => ChartColors::Info->value,
        };
    }

    public function label(): string
    {
        return __('enums.accreditation_status.'.$this->value);
    }

    public function isReviewed(): bool
    {
        return in_array($this, [
            self::Approved,
            self::Rejected,
            self::UnderReview,
        ], true);
    }
}
