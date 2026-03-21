<?php

declare(strict_types=1);

namespace App\Users\Presentation\DTO;

final class InviteUserRequest
{
    public function __construct(
        public readonly string  $email,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName  = null,
        public readonly array   $roles     = [],
    ) {}

    /** @throws \InvalidArgumentException */
    public static function fromArray(array $data): self
    {
        if (empty($data['email'])) {
            throw new \InvalidArgumentException('email is required.');
        }

        return new self(
            email:     trim($data['email']),
            firstName: isset($data['first_name']) ? trim($data['first_name']) : null,
            lastName:  isset($data['last_name'])  ? trim($data['last_name'])  : null,
            roles:     $data['roles'] ?? [],
        );
    }
}
