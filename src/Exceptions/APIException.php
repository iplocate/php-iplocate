<?php

declare(strict_types=1);

namespace IPLocate\Exceptions;

class APIException extends IPLocateException
{
    public function __construct(
        string $message,
        int $statusCode,
        ?string $responseBody = null,
    ) {
        parent::__construct($message, $statusCode, null, $responseBody);
    }
}
