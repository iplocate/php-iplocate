<?php

declare(strict_types=1);

namespace IPLocate\Models;

class ASN
{
    public function __construct(
        public string $asn,
        public string $route,
        public string $netname,
        public string $name,
        public string $countryCode,
        public string $domain,
        public string $type,
        public string $rir,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            asn: $data['asn'],
            route: $data['route'],
            netname: $data['netname'],
            name: $data['name'],
            countryCode: $data['country_code'],
            domain: $data['domain'],
            type: $data['type'],
            rir: $data['rir'],
        );
    }
}
