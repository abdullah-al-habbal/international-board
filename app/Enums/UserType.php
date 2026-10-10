<?php

declare(strict_types=1);

namespace App\Enums;

enum UserType: string
{
    case Admin = 'admin';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::Admin => __('enums.user_type.admin'),
            self::Client => __('enums.user_type.client'),
        };
    }
}
