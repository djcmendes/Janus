<?php

declare(strict_types=1);

namespace App\Users\Application\Command;

final class UpdateUserCommand
{
    public function __construct(
        public readonly string  $id,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName  = null,
        public readonly ?array  $roles     = null,
        public readonly ?string $password  = null,
        public readonly ?string $status    = null,
    ) {}
}
