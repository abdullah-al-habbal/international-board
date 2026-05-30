<?php

declare(strict_types=1);

namespace App\Enums;

enum SettingType: string
{
    case Text = 'text';
    case Number = 'number';
    case Boolean = 'boolean';
    case Json = 'json';
    case Email = 'email';
    case Phone = 'phone';
    case Url = 'url';
    case Html = 'html';
}
