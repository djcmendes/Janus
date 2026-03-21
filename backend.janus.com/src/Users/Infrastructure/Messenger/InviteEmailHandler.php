<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\Messenger;

use App\Users\Domain\Message\InviteEmailMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
final class InviteEmailHandler
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {}

    public function __invoke(InviteEmailMessage $message): void
    {
        $acceptUrl = sprintf(
            '%s/accept-invite?token=%s',
            rtrim($message->appBaseUrl, '/'),
            urlencode($message->inviteToken),
        );

        $inviter = $message->inviterName ?? 'An admin';

        $text = <<<TEXT
            {$inviter} has invited you to join Janus.

            Click the link below to set your password and activate your account
            (valid for 48 hours):

            {$acceptUrl}

            If you were not expecting this invitation, you can safely ignore this email.
            TEXT;

        $html = sprintf(
            '<p><strong>%s</strong> has invited you to join <strong>Janus</strong>.</p>'
            . '<p><a href="%s">Accept invitation</a></p>'
            . '<p>Or copy this link into your browser:<br><code>%s</code></p>'
            . '<p>This link is valid for 48 hours. If you were not expecting this, ignore this email.</p>',
            htmlspecialchars($inviter, ENT_QUOTES),
            htmlspecialchars($acceptUrl, ENT_QUOTES),
            htmlspecialchars($acceptUrl, ENT_QUOTES),
        );

        $email = (new Email())
            ->to($message->recipientEmail)
            ->subject('You have been invited to Janus')
            ->text($text)
            ->html($html);

        $this->mailer->send($email);
    }
}
