<?php

declare(strict_types=1);

namespace IPLocate\Models;

class Hosting
{
    public function __construct(
        public ?string $provider = null,
        public ?string $domain = null,
        public ?string $network = null,
        public ?string $region = null,
        public ?string $service = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            provider: $data['provider'] ?? null,
            domain: $data['domain'] ?? null,
            network: $data['network'] ?? null,
            region: $data['region'] ?? null,
            service: $data['service'] ?? null,
        );
    }
}
