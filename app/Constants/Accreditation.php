<?php

declare(strict_types=1);

namespace App\Constants;

final class Accreditation
{
    public const EXPIRY_WARNING_DAYS = 30;
    public const NOTIFICATION_THRESHOLDS = [30, 15, 7, 1];
    public const MIN_VALID_DAYS = 0;
    
    public const STATUS_ACTIVE_LABEL = 'Active';
    public const STATUS_EXPIRED_LABEL = 'Expired';
}
