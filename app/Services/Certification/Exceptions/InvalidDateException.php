<?php

declare(strict_types=1);

namespace App\Services\Certification\Exceptions;

use RuntimeException;

final class InvalidDateException extends RuntimeException
{
    public function __construct(string $value, ?int $rowIndex = null)
    {
        $message = 'Invalid accreditation date: "'.$value.'"';

        if ($rowIndex !== null) {
            $message .= " (row {$rowIndex})";
        }

        parent::__construct($message);
    }
}
