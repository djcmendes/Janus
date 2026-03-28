<?php

declare(strict_types=1);

namespace App\Heimdall\Application\DTO;

final readonly class PasswordApplyRequest
{
    public function __construct(
        public string $token,
        public string $password,
    ) {}
}
