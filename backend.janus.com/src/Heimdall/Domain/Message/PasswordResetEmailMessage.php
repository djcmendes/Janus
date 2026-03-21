<?php

declare(strict_types=1);

namespace App\Heimdall\Domain\Message;

/**
 * Messenger message payload for a password-reset email.
 * Routed to the sync transport so the email is sent immediately.
 */
final class PasswordResetEmailMessage
{
    public function __construct(
        public readonly string $recipientEmail,
        public readonly string $resetToken,
        public readonly string $appBaseUrl,
    ) {}
}
