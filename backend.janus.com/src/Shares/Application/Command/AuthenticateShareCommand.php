<?php

declare(strict_types=1);

namespace App\Shares\Application\Command;

final class AuthenticateShareCommand
{
    public function __construct(
        public readonly string  $token,
        public readonly ?string $password = null,
    ) {}
}
