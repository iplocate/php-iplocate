<?php

declare(strict_types=1);

namespace IPLocate\Models;

class Company
{
    public function __construct(
        public string $name,
        public string $domain,
        public string $countryCode,
        public string $type,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            domain: $data['domain'],
            countryCode: $data['country_code'],
            type: $data['type'],
        );
    }
}
