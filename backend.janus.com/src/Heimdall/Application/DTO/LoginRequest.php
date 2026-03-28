<?php

declare(strict_types=1);

namespace App\Heimdall\Application\DTO;

/**
 * DTO for auth login/register requests.
 */
final readonly class LoginRequest
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}
}
