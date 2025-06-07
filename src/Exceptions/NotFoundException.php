<?php

declare(strict_types=1);

namespace IPLocate\Exceptions;

class NotFoundException extends IPLocateException
{
    public function __construct(
        string $message = 'IP address not found',
        ?string $responseBody = null,
    ) {
        parent::__construct($message, 404, null, $responseBody);
    }
}
