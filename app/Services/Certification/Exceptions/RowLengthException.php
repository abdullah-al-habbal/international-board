<?php

declare(strict_types=1);

namespace App\Services\Certification\Exceptions;

use RuntimeException;

final class RowLengthException extends RuntimeException
{
    public function __construct(int $expected, int $actual, ?int $rowIndex = null)
    {
        $message = "Row has {$actual} columns, expected {$expected}";

        if ($rowIndex !== null) {
            $message .= " (row {$rowIndex})";
        }

        parent::__construct($message);
    }
}
