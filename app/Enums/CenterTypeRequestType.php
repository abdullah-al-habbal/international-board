<?php

declare(strict_types=1);

namespace App\Enums;

enum CenterTypeRequestType: string
{
    case Course = 'course';
    case DocumentType = 'document_type';

    public function label(): string
    {
        return __('enums.center_type_request_type.' . $this->value);
    }
}
