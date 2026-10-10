<?php

declare(strict_types=1);

namespace App\Services\Certification\Exceptions;

use RuntimeException;

final class MissingValueException extends RuntimeException
{
    public function __construct(string $column, ?int $rowIndex = null)
    {
        $message = 'Empty required value in column "'.$column.'"';

        if ($rowIndex !== null) {
            $message .= " (row {$rowIndex})";
        }

        parent::__construct($message);
    }
}
