<?php

declare(strict_types=1);

namespace App\Users\Presentation\DTO;

final class UpdateUserRequest
{
    private const ALLOWED_STATUSES = ['active', 'invited', 'suspended'];

    public function __construct(
        public readonly ?string $firstName = null,
        public readonly ?string $lastName  = null,
        public readonly ?array  $roles     = null,
        public readonly ?string $password  = null,
        public readonly ?string $status    = null,
    ) {}

    /** @throws \InvalidArgumentException */
    public static function fromArray(array $data): self
    {
        if (isset($data['status']) && !in_array($data['status'], self::ALLOWED_STATUSES, true)) {
            throw new \InvalidArgumentException(
                sprintf('status must be one of: %s.', implode(', ', self::ALLOWED_STATUSES))
            );
        }

        return new self(
            firstName: isset($data['first_name']) ? trim($data['first_name']) : null,
            lastName:  isset($data['last_name'])  ? trim($data['last_name'])  : null,
            roles:     $data['roles']    ?? null,
            password:  $data['password'] ?? null,
            status:    $data['status']   ?? null,
        );
    }
}
