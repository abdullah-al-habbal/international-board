<?php

declare(strict_types=1);

namespace App\Services\Certification\Exceptions;

use RuntimeException;

final class MissingHeaderException extends RuntimeException
{
    /**
     * @param  list<string>  $missing
     */
    public function __construct(array $missing)
    {
        parent::__construct('Missing required CSV header(s): '.implode(', ', $missing));
    }
}
