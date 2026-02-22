<?php

// file: app/Enums/CenterStatus.php
declare(strict_types=1);

namespace App\Enums;

enum CenterStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Pending = 'pending';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Active => __('widgets.status.active'),
            self::Inactive => __('widgets.status.inactive'),
            self::Pending => __('widgets.status.pending'),
            self::Suspended => __('widgets.status.suspended'),
        };
    }
}
