<?php

declare(strict_types=1);

namespace IPLocate\Exceptions;

class AuthenticationException extends IPLocateException
{
    public function __construct(
        string $message = 'Invalid API key',
        ?string $responseBody = null,
    ) {
        parent::__construct($message, 403, null, $responseBody);
    }
}
