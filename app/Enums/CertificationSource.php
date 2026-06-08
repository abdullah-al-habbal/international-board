<?php

declare(strict_types=1);

namespace App\Enums;

enum CertificationSource: string
{
    case Board = 'board';
    case Center = 'center';
    case Trainer = 'trainer';
}
