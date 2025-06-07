<?php

declare(strict_types=1);

namespace IPLocate\Exceptions;

class RateLimitException extends IPLocateException
{
    public function __construct(
        string $message = 'Rate limit exceeded. Please upgrade your plan at iplocate.io/account',
        ?string $responseBody = null,
    ) {
        parent::__construct($message, 429, null, $responseBody);
    }
}
