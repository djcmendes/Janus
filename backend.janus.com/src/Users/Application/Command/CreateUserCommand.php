<?php

declare(strict_types=1);

namespace App\Users\Application\Command;

final class CreateUserCommand
{
    public function __construct(
        public readonly string  $email,
        public readonly string  $password,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName  = null,
        public readonly array   $roles     = [],
    ) {}
}
