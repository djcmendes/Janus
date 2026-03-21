<?php

declare(strict_types=1);

namespace App\Notifications\Application\Command;

final class MarkNotificationReadCommand
{
    public function __construct(
        public readonly string $id,
        public readonly string $requestingUserId,
        public readonly bool   $isAdmin = false,
    ) {}
}
