<?php

declare(strict_types=1);

namespace IPLocate\Exceptions;

class InvalidIPException extends IPLocateException
{
    public function __construct(
        string $ip,
        string $message = '',
        ?string $responseBody = null,
    ) {
        $fullMessage = $message ?: "Invalid IP address: {$ip}";
        parent::__construct($fullMessage, 400, null, $responseBody);
    }
}
