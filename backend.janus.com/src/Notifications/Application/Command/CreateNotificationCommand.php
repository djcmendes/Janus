<?php

declare(strict_types=1);

namespace App\Notifications\Application\Command;

final class CreateNotificationCommand
{
    public function __construct(
        public readonly string  $recipientId,
        public readonly string  $subject,
        public readonly string  $message,
        public readonly ?string $senderId   = null,
        public readonly ?string $collection = null,
        public readonly ?string $item       = null,
    ) {}
}
