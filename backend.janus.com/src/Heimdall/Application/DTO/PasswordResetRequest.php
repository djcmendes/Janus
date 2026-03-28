<?php

declare(strict_types=1);

namespace App\Heimdall\Application\DTO;
final readonly class PasswordResetRequest
{
    public function __construct(
        public string $email,
    ) {}
}
