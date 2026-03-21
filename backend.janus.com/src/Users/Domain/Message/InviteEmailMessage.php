<?php

declare(strict_types=1);

namespace App\Users\Domain\Message;

/**
 * Messenger message payload for a user-invite email.
 * Routed to the sync transport so the email is sent immediately.
 */
final class InviteEmailMessage
{
    public function __construct(
        public readonly string $recipientEmail,
        public readonly string $inviteToken,
        public readonly string $appBaseUrl,
        public readonly ?string $inviterName = null,
    ) {}
}
