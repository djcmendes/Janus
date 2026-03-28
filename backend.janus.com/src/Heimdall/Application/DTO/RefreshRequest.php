<?php

declare(strict_types=1);

namespace App\Heimdall\Application\DTO;

final readonly class RefreshRequest
{
    public function __construct(
        public string $refreshToken,
    ) {}
}
