<?php

declare(strict_types=1);

namespace App\Enums;

enum CertificateType: string
{
    case Basic = 'basic';
    case Accreditation = 'accreditation';
}
