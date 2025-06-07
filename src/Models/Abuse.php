<?php

declare(strict_types=1);

namespace IPLocate\Models;

class Abuse
{
    public function __construct(
        public ?string $address = null,
        public ?string $countryCode = null,
        public ?string $email = null,
        public ?string $name = null,
        public ?string $network = null,
        public ?string $phone = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            address: $data['address'] ?? null,
            countryCode: $data['country_code'] ?? null,
            email: $data['email'] ?? null,
            name: $data['name'] ?? null,
            network: $data['network'] ?? null,
            phone: $data['phone'] ?? null,
        );
    }
}
