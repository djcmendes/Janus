<?php

declare(strict_types=1);

namespace App\Shares\Application\Command;

final class DeleteShareCommand
{
    public function __construct(
        public readonly string $id,
        public readonly string $requestingUserId,
        public readonly bool   $isAdmin = false,
    ) {}
}
