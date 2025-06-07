<?php

declare(strict_types=1);

namespace IPLocate\Exceptions;

use Exception;

class IPLocateException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Exception $previous = null,
        public ?string $responseBody = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
