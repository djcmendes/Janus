<?php

declare(strict_types=1);

namespace App\Shares\Application\Command;

final class CreateShareCommand
{
    public function __construct(
        public readonly string  $collection,
        public readonly string  $item,
        public readonly string  $userId,
        public readonly ?string $name      = null,
        public readonly ?string $password  = null,
        public readonly ?string $expiresAt = null,
        public readonly ?int    $maxUses   = null,
    ) {}
}
