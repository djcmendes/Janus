<?php

declare(strict_types=1);

namespace App\Policies\Presentation\DTO;

final class CreateAccessRequest
{
    public function __construct(
        public readonly string  $policyId,
        public readonly ?string $roleId = null,
    ) {}

    /** @throws \InvalidArgumentException */
    public static function fromArray(array $data): self
    {
        if (empty($data['policy'])) {
            throw new \InvalidArgumentException('policy (id) is required.');
        }

        return new self(
            policyId: $data['policy'],
            roleId:   $data['role'] ?? null,
        );
    }
}
